<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use App\Models\ChartOfAccount;
use App\Models\FinancialReportType;
use App\Models\GeneralLedger;
use App\Models\OrganizationProfile;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CashDisbursementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 *
 * Cakupan pengujian mencakup semua jalur eksekusi (execution paths) internal dari:
 *   - CashDisbursementController (index, store, update, destroy, suggestDescription)
 *   - CashDisbursementService   (store GL double-entry, update GL replace, destroy atomic, suggestDescription rules)
 *   - StoreCashDisbursementRequest (validation rules)
 *   - TransactionRepository::getPaginated() (filter type, period, search, sort)
 *   - DocumentNumberService::generate() (prefix BKKUDD, sequence increment)
 *   - Authorization (RBAC)
 */
class CashDisbursementTest extends TestCase
{
    use RefreshDatabase;

    private User             $financeStaff;
    private User             $regularUser;
    private ChartOfAccount   $cashAccount;
    private ChartOfAccount   $transactionAccount;
    private AccountCategory  $assetCategory;
    private AccountSubcategory $cashSubcategory;

    protected function tearDown(): void
    {
        Cache::store('array')->flush();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Cache::store('array')->flush();

        $reportType = FinancialReportType::create(['name' => 'Neraca']);

        $this->assetCategory = AccountCategory::create(['name' => 'Aset']);

        $this->cashSubcategory = AccountSubcategory::create([
            'account_category_id' => $this->assetCategory->id,
            'name'                => 'Kas dan Setara Kas',
        ]);

        $expenseCategory    = AccountCategory::create(['name' => 'Beban']);
        $expenseSubcategory = AccountSubcategory::create([
            'account_category_id' => $expenseCategory->id,
            'name'                => 'Beban Operasional',
        ]);

        $this->cashAccount = ChartOfAccount::create([
            'id'                       => '11101-00',
            'account_subcategory_id'   => $this->cashSubcategory->id,
            'account_name'             => 'Kas Operasional',
            'normal_balance'           => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->transactionAccount = ChartOfAccount::create([
            'id'                       => '51101-00',
            'account_subcategory_id'   => $expenseSubcategory->id,
            'account_name'             => 'Beban Operasional Umum',
            'normal_balance'           => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->financeStaff = User::create([
            'name'     => 'Staf Keuangan',
            'email'    => 'staf@example.test',
            'password' => 'password',
            'role'     => RoleEnum::FINANCE_STAFF,
        ]);

        $this->regularUser = User::create([
            'name'     => 'Pengguna Biasa',
            'email'    => 'pengguna@example.test',
            'password' => 'password',
            'role'     => RoleEnum::USER,
        ]);
    }

    public function test_finance_staff_can_access_disbursements_index(): void
    {
        $response = $this->actingAs($this->financeStaff)->get(route('disbursements.index'));

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_disbursements_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('disbursements.index'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('disbursements.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_store_disbursement(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('disbursements.store'), $this->validPayload());

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_delete_disbursement(): void
    {
        $this->actingAs($this->financeStaff)->post(route('disbursements.store'), $this->validPayload());
        $transaction = Transaction::first();

        $response = $this->actingAs($this->regularUser)
            ->delete(route('disbursements.destroy', $transaction->id));

        $response->assertForbidden();
    }

    public function test_store_creates_one_transaction_record(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload());

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'transaction_type' => TransactionTypeEnum::EXPENSE->value,
        ]);
    }

    public function test_store_creates_exactly_two_general_ledger_entries(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload());

        $this->assertDatabaseCount('general_ledgers', 2);
    }

    public function test_store_redirects_to_index_with_success_message(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload());

        $response->assertRedirect(route('disbursements.index'));
        $response->assertSessionHas('success');
    }

    // =========================================================================
    // GL DOUBLE-ENTRY CORRECTNESS (White-Box: internal GL logic inversion)
    // Path: CashDisbursementService::store() → generalLedgerRepository->createMany()
    // =========================================================================

    public function test_double_entry_debit_goes_to_transaction_account(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 500_000]));

        $transaction = Transaction::first();

        // Kode Transaksi (beban bertambah) HARUS Debit
        $debitEntry = $transaction->generalLedgers->first(fn($gl) => (float) $gl->debit > 0);

        $this->assertNotNull($debitEntry);
        $this->assertEquals($this->transactionAccount->id, $debitEntry->chart_of_account_id);
        $this->assertEquals(500_000, (float) $debitEntry->debit);
        $this->assertEquals(0, (float) $debitEntry->credit);
    }

    public function test_double_entry_credit_goes_to_cash_account(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 500_000]));

        $transaction = Transaction::first();

        // Rekening Kas (kas berkurang) HARUS Credit
        $creditEntry = $transaction->generalLedgers->first(fn($gl) => (float) $gl->credit > 0);

        $this->assertNotNull($creditEntry);
        $this->assertEquals($this->cashAccount->id, $creditEntry->chart_of_account_id);
        $this->assertEquals(500_000, (float) $creditEntry->credit);
        $this->assertEquals(0, (float) $creditEntry->debit);
    }

    public function test_double_entry_total_debit_equals_total_credit(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 1_250_000]));

        $transaction = Transaction::first();

        $debitTotal  = $transaction->generalLedgers->sum(fn($gl) => (float) $gl->debit);
        $creditTotal = $transaction->generalLedgers->sum(fn($gl) => (float) $gl->credit);

        $this->assertEquals(1_250_000, $debitTotal);
        $this->assertEquals(1_250_000, $creditTotal);
        $this->assertEquals($debitTotal, $creditTotal, 'GL harus balance (debit = credit)');
    }

    public function test_gl_entry_is_inverted_vs_receipt_pattern(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 300_000]));

        $transaction = Transaction::first();

        $debitEntry  = $transaction->generalLedgers->first(fn($gl) => (float) $gl->debit > 0);
        $creditEntry = $transaction->generalLedgers->first(fn($gl) => (float) $gl->credit > 0);

        // Debit: BUKAN cash account
        $this->assertNotEquals($this->cashAccount->id, $debitEntry->chart_of_account_id);
        // Credit: cash account
        $this->assertEquals($this->cashAccount->id, $creditEntry->chart_of_account_id);
    }

    // =========================================================================
    // DOCUMENT NUMBER GENERATION (Branch: empty DB → seq=1; existing → seq+1)
    // Path: DocumentNumberService::generate(EXPENSE)
    // =========================================================================

    public function test_document_number_is_auto_generated_with_bkkudd_prefix(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload());

        $transaction = Transaction::first();

        $this->assertStringStartsWith('BKKUDD', $transaction->document_number);
    }

    public function test_first_document_number_is_bkkudd001(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload());

        $this->assertDatabaseHas('transactions', ['document_number' => 'BKKUDD001']);
    }

    public function test_second_disbursement_increments_document_number_sequence(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload());
        $this->post(route('disbursements.store'), $this->validPayload());

        $numbers = Transaction::where('transaction_type', TransactionTypeEnum::EXPENSE->value)
            ->orderBy('id')
            ->pluck('document_number')
            ->toArray();

        $this->assertEquals('BKKUDD001', $numbers[0]);
        $this->assertEquals('BKKUDD002', $numbers[1]);
    }

    public function test_disbursement_sequence_independent_from_receipt_sequence(): void
    {
        $this->actingAs($this->financeStaff);

        Transaction::create([
            'transaction_date' => date('Y-m-d'),
            'document_number'  => 'BKMUDD001',
            'transaction_type' => 'PEMASUKAN',
            'user_id'          => $this->financeStaff->id,
        ]);
        Transaction::create([
            'transaction_date' => date('Y-m-d'),
            'document_number'  => 'BKMUDD002',
            'transaction_type' => 'PEMASUKAN',
            'user_id'          => $this->financeStaff->id,
        ]);

        $this->post(route('disbursements.store'), $this->validPayload());

        $this->assertDatabaseHas('transactions', ['document_number' => 'BKKUDD001']);
    }

    // =========================================================================
    // CUSTOM DOCUMENT NUMBER (Branch: user provides own document_number)
    // Path: CashDisbursementService::store() - documentNumber ternary
    // =========================================================================

    public function test_store_with_custom_document_number_uses_that_number(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload([
            'document_number' => 'CUSTOM-BKK-999',
        ]));

        $this->assertDatabaseHas('transactions', ['document_number' => 'CUSTOM-BKK-999']);
    }

    // =========================================================================
    // VALIDATION TESTS (Branch: invalid input paths)
    // Path: StoreCashDisbursementRequest::rules()
    // =========================================================================

    public function test_store_validates_all_required_fields(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), []);

        $response->assertSessionHasErrors([
            'transaction_date',
            'cash_account_code',
            'transaction_account_code',
            'amount',
            'user_id',
        ]);
    }

    public function test_store_validates_amount_must_be_at_least_one(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload(['amount' => 0]));

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_store_validates_amount_must_be_numeric(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload(['amount' => 'bukan-angka']));

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_store_validates_cash_account_must_exist_in_coa(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload([
            'cash_account_code' => 'NONEXISTENT-999',
        ]));

        $response->assertSessionHasErrors(['cash_account_code']);
    }

    public function test_store_validates_transaction_account_must_exist_in_coa(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload([
            'transaction_account_code' => 'NONEXISTENT-999',
        ]));

        $response->assertSessionHasErrors(['transaction_account_code']);
    }

    public function test_store_validates_document_number_must_be_unique(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload([
            'document_number' => 'BKKUDD-DUP',
        ]));

        $response = $this->post(route('disbursements.store'), $this->validPayload([
            'document_number' => 'BKKUDD-DUP',
        ]));

        $response->assertSessionHasErrors(['document_number']);
    }

    public function test_store_accepts_nullable_program_id(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload([
            'program_id' => null,
        ]));

        $response->assertSessionDoesntHaveErrors(['program_id']);
        $this->assertDatabaseHas('transactions', ['program_id' => null]);
    }

    public function test_store_accepts_nullable_reference_and_description(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('disbursements.store'), $this->validPayload([
            'reference'   => null,
            'description' => null,
        ]));

        $response->assertSessionDoesntHaveErrors(['reference', 'description']);
    }

    public function test_transaction_type_is_always_pengeluaran_regardless_of_input(): void
    {
        $this->actingAs($this->financeStaff);

        // User mencoba inject transaction_type lain → harus tetap PENGELUARAN
        $this->post(route('disbursements.store'), $this->validPayload([
            'transaction_type' => 'PEMASUKAN',
        ]));

        $this->assertDatabaseHas('transactions', [
            'transaction_type' => TransactionTypeEnum::EXPENSE->value,
        ]);
        $this->assertDatabaseMissing('transactions', [
            'transaction_type' => 'PEMASUKAN',
        ]);
    }

    public function test_update_disbursement_persists_new_field_values(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 500_000]));
        $transaction = Transaction::first();

        $response = $this->put(route('disbursements.update', $transaction->id), $this->validPayload([
            'amount'      => 750_000,
            'description' => 'Keterangan diperbarui',
        ]));

        $response->assertRedirect(route('disbursements.index'));
        $response->assertSessionHas('success');

        $transaction->refresh();
        $this->assertEquals('Keterangan diperbarui', $transaction->description);
    }

    public function test_update_replaces_old_gl_entries_with_new_ones(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 500_000]));
        $transaction = Transaction::first();
        $oldGlIds    = $transaction->generalLedgers->pluck('id')->toArray();

        $this->put(route('disbursements.update', $transaction->id), $this->validPayload([
            'amount' => 750_000,
        ]));

        foreach ($oldGlIds as $oldId) {
            $this->assertDatabaseMissing('general_ledgers', ['id' => $oldId]);
        }

        $this->assertDatabaseCount('general_ledgers', 2);
    }

    public function test_update_new_gl_reflects_correct_amounts(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['amount' => 500_000]));
        $transaction = Transaction::first();

        $this->put(route('disbursements.update', $transaction->id), $this->validPayload([
            'amount' => 900_000,
        ]));

        $transaction->refresh();
        $transaction->load('generalLedgers');

        $debitTotal  = $transaction->generalLedgers->sum(fn($gl) => (float) $gl->debit);
        $creditTotal = $transaction->generalLedgers->sum(fn($gl) => (float) $gl->credit);

        $this->assertEquals(900_000, $debitTotal);
        $this->assertEquals(900_000, $creditTotal);
    }

    public function test_update_ignores_blank_document_number_and_keeps_original(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload([
            'document_number' => 'BKKUDD-ORIG',
        ]));
        $transaction = Transaction::first();

        $this->put(route('disbursements.update', $transaction->id), $this->validPayload([
            'document_number' => null,
        ]));

        $transaction->refresh();
        $this->assertEquals('BKKUDD-ORIG', $transaction->document_number);
    }

    public function test_update_validates_unique_document_number_ignores_own_id(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['document_number' => 'BKK-A']));
        $this->post(route('disbursements.store'), $this->validPayload(['document_number' => 'BKK-B']));

        $txA = Transaction::where('document_number', 'BKK-A')->first();
        $txB = Transaction::where('document_number', 'BKK-B')->first();

        $responseOk = $this->put(route('disbursements.update', $txA->id), $this->validPayload([
            'document_number' => 'BKK-A',
        ]));
        $responseOk->assertSessionDoesntHaveErrors(['document_number']);

        $responseFail = $this->put(route('disbursements.update', $txA->id), $this->validPayload([
            'document_number' => 'BKK-B',
        ]));
        $responseFail->assertSessionHasErrors(['document_number']);
    }

    public function test_destroy_removes_transaction_and_all_general_ledgers(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload());
        $transaction = Transaction::first();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('general_ledgers', 2);

        $response = $this->delete(route('disbursements.destroy', $transaction->id));

        $response->assertRedirect(route('disbursements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('general_ledgers', 0);
    }

    public function test_suggest_description_returns_json_with_correct_structure(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('disbursements.suggest-description', [
            'transaction_account_code' => $this->transactionAccount->id,
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => ['description'],
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_suggest_description_returns_null_for_nonexistent_coa(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('disbursements.suggest-description', [
            'transaction_account_code' => 'NONEXISTENT-999',
        ]));

        $response->assertOk();
        $this->assertNull($response->json('data.description'));
    }

    #[DataProvider('descriptionKeywordProvider')]
    public function test_suggest_description_keyword_rules(
        string $accountName,
        string $expectedPrefix
    ): void {
        $reportType = FinancialReportType::first();

        $coa = ChartOfAccount::create([
            'id'                       => 'TEST-' . str()->random(4),
            'account_subcategory_id'   => $this->cashSubcategory->id,
            'account_name'             => $accountName,
            'normal_balance'           => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('disbursements.suggest-description', [
            'transaction_account_code' => $coa->id,
        ]));

        $response->assertOk();
        $this->assertStringContainsString(
            $expectedPrefix,
            $response->json('data.description') ?? '',
            "Expected '{$expectedPrefix}' in description for account '{$accountName}'"
        );
    }

    public static function descriptionKeywordProvider(): array
    {
        return [
            'keyword Hutang'                   => ['Hutang Usaha Jangka Pendek', 'Pembayaran Hutang'],
            'keyword Tunjangan'                => ['Tunjangan Transportasi', 'Pembayaran Manajemen Organisasi'],
            'keyword BPJS'                     => ['Biaya BPJS Ketenagakerjaan', 'Pembayaran BPJS'],
            'keyword Gaji'                     => ['Beban Gaji Karyawan', 'Pembayaran Gaji'],
            'keyword Insentif'                 => ['Insentif Relawan', 'Pembayaran Jasa'],
            'keyword Internet, Listrik dan Air'=> ['Internet, Listrik dan Air', 'Pembayaran Rekening'],
        ];
    }

    public function test_suggest_description_priority_internet_over_other_keywords(): void
    {
        $reportType = FinancialReportType::first();

        $coa = ChartOfAccount::create([
            'id'                       => 'TEST-INET',
            'account_subcategory_id'   => $this->cashSubcategory->id,
            'account_name'             => 'Internet, Listrik dan Air',
            'normal_balance'           => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('disbursements.suggest-description', [
            'transaction_account_code' => $coa->id,
        ]));

        $this->assertStringStartsWith('Pembayaran Rekening', $response->json('data.description'));
    }

    public function test_suggest_description_returns_empty_string_for_unmatched_account(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('disbursements.suggest-description', [
            'transaction_account_code' => $this->transactionAccount->id, // 'Beban Operasional Umum'
        ]));

        $response->assertOk();
        $this->assertEquals('', $response->json('data.description'));
    }

    public function test_suggest_description_requires_transaction_account_code_param(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('disbursements.suggest-description'));

        $response->assertUnprocessable(); // 422
    }

    public function test_index_only_shows_expense_transactions_not_income(): void
    {
        $this->actingAs($this->financeStaff);

        Transaction::create([
            'transaction_date' => date('Y-m-d'),
            'document_number'  => 'BKMUDD001',
            'transaction_type' => 'PEMASUKAN',
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Ini penerimaan',
        ]);

        $this->post(route('disbursements.store'), $this->validPayload([
            'description' => 'Ini pengeluaran',
        ]));

        $response = $this->get(route('disbursements.index'));

        $response->assertOk();
        $response->assertSee('Ini pengeluaran');
        $response->assertDontSee('Ini penerimaan');
    }

    public function test_index_filters_by_financial_period(): void
    {
        $profile = OrganizationProfile::firstOrCreate(['id' => 1]);
        $profile->update([
            'financial_period_start' => '2026-01-01',
            'financial_period_end'   => '2026-01-31',
        ]);

        $this->actingAs($this->financeStaff);

        Transaction::create([
            'transaction_date' => '2026-01-15',
            'document_number'  => 'BKKUDD001',
            'transaction_type' => 'PENGELUARAN',
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Dalam Periode',
        ]);

        Transaction::create([
            'transaction_date' => '2026-03-01',
            'document_number'  => 'BKKUDD002',
            'transaction_type' => 'PENGELUARAN',
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Luar Periode',
        ]);

        $response = $this->get(route('disbursements.index'));

        $response->assertOk();
        $response->assertSee('Dalam Periode');
        $response->assertDontSee('Luar Periode');
    }

    public function test_global_search_finds_by_document_number(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['document_number' => 'BKKUDD-SEARCH']));
        $this->post(route('disbursements.store'), $this->validPayload(['document_number' => 'BKKUDD-OTHER']));

        $response = $this->get(route('disbursements.index', ['search' => 'SEARCH']));

        $response->assertOk();
        $response->assertSee('BKKUDD-SEARCH');
        $response->assertDontSee('BKKUDD-OTHER');
    }

    public function test_global_search_finds_by_description(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['description' => 'Bayar listrik kantor']));
        $this->post(route('disbursements.store'), $this->validPayload(['description' => 'Bayar gaji rutin']));

        $response = $this->get(route('disbursements.index', ['search' => 'listrik']));

        $response->assertOk();
        $response->assertSee('Bayar listrik kantor');
        $response->assertDontSee('Bayar gaji rutin');
    }

    public function test_global_search_finds_by_program_name(): void
    {
        $this->actingAs($this->financeStaff);

        $program = Program::create([
            'name'    => 'Program Kesehatan Masyarakat',
            'user_id' => $this->financeStaff->id,
        ]);

        $this->post(route('disbursements.store'), $this->validPayload([
            'program_id'  => $program->id,
            'description' => 'Ada program',
        ]));
        $this->post(route('disbursements.store'), $this->validPayload([
            'description' => 'Tanpa program',
        ]));

        $response = $this->get(route('disbursements.index', ['search' => 'Kesehatan']));

        $response->assertOk();
        $response->assertSee('Ada program');
        $response->assertDontSee('Tanpa program');
    }

    public function test_global_search_finds_by_user_name(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['description' => 'Dibayar ke staf']));

        $response = $this->get(route('disbursements.index', ['search' => 'Staf Keuangan']));

        $response->assertOk();
        $response->assertSee('Dibayar ke staf');
    }

    public function test_global_search_finds_by_reference(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('disbursements.store'), $this->validPayload(['reference' => 'INV-2026-001']));
        $this->post(route('disbursements.store'), $this->validPayload(['reference' => 'INV-2026-002']));

        $response = $this->get(route('disbursements.index', ['search' => 'INV-2026-001']));

        $response->assertOk();
        $response->assertSee('INV-2026-001');
        $response->assertDontSee('INV-2026-002');
    }

    public function test_index_view_receives_all_required_data_keys(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->get(route('disbursements.index'));

        $response->assertOk();
        $response->assertViewHasAll([
            'cashAccounts',
            'transactionAccounts',
            'programs',
            'users',
            'nextDocumentNumber',
            'disbursements',
        ]);
    }

    public function test_next_document_number_on_index_has_bkkudd_prefix(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->get(route('disbursements.index'));

        $nextDocNumber = $response->viewData('nextDocumentNumber');
        $this->assertStringStartsWith('BKKUDD', $nextDocNumber);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'transaction_date'         => date('Y-m-d'),
            'cash_account_code'        => $this->cashAccount->id,
            'transaction_account_code' => $this->transactionAccount->id,
            'amount'                   => 1_000_000,
            'description'              => 'Test pengeluaran kas',
            'reference'                => 'INV-TEST-001',
            'user_id'                  => $this->financeStaff->id,
        ], $overrides);
    }
}
