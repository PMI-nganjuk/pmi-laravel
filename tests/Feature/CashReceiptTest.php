<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use App\Models\ChartOfAccount;
use App\Models\FinancialReportType;
use App\Models\OrganizationProfile;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashReceiptTest extends TestCase
{
    use RefreshDatabase;

    private User             $financeStaff;
    private User             $regularUser;
    private ChartOfAccount   $cashAccount;
    private ChartOfAccount   $transactionAccount;
    private AccountCategory  $assetCategory;
    private AccountSubcategory $cashSubcategory;

    protected function setUp(): void
    {
        parent::setUp();

        $reportType = FinancialReportType::create(['name' => 'Neraca']);

        $this->assetCategory = AccountCategory::create(['name' => 'Aset']);

        $this->cashSubcategory = AccountSubcategory::create([
            'account_category_id' => $this->assetCategory->id,
            'name'                => 'Kas dan Setara Kas',
        ]);

        $incomeCategory    = AccountCategory::create(['name' => 'Pendapatan']);
        $incomeSubcategory = AccountSubcategory::create([
            'account_category_id' => $incomeCategory->id,
            'name'                => 'Pendapatan Operasional',
        ]);

        $this->cashAccount = ChartOfAccount::create([
            'id'                     => '11101-00',
            'account_subcategory_id' => $this->cashSubcategory->id,
            'account_name'           => 'Kas Operasional',
            'normal_balance'         => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->transactionAccount = ChartOfAccount::create([
            'id'                     => '41101-00',
            'account_subcategory_id' => $incomeSubcategory->id,
            'account_name'           => 'Pendapatan Netto Tidak Terikat Periode Berjalan',
            'normal_balance'         => 'C',
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

    public function test_finance_staff_can_access_receipts_index(): void
    {
        $response = $this->actingAs($this->financeStaff)->get(route('receipts.index'));

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_receipts(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('receipts.index'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('receipts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_creates_transaction_and_two_general_ledger_entries(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('receipts.store'), $this->validPayload());

        $response->assertRedirect(route('receipts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('general_ledgers', 2);

        $this->assertDatabaseHas('transactions', [
            'transaction_type' => TransactionTypeEnum::INCOME->value,
        ]);
    }

    public function test_double_entry_amounts_are_balanced(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('receipts.store'), $this->validPayload(['amount' => 500000]));

        $transaction = Transaction::first();

        $debitTotal  = $transaction->generalLedgers->sum('debit');
        $creditTotal = $transaction->generalLedgers->sum('credit');

        $this->assertEquals(500000, $debitTotal);
        $this->assertEquals(500000, $creditTotal);
        $this->assertEquals($debitTotal, $creditTotal);
    }

    public function test_document_number_is_auto_generated_with_correct_prefix(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('receipts.store'), $this->validPayload());

        $transaction = Transaction::first();

        $this->assertStringStartsWith('BKMUDD', $transaction->document_number);
    }

    public function test_second_receipt_increments_document_number_sequence(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('receipts.store'), $this->validPayload());
        $this->post(route('receipts.store'), $this->validPayload());

        $numbers = Transaction::orderBy('id')->pluck('document_number')->toArray();

        $this->assertEquals('BKMUDD001', $numbers[0]);
        $this->assertEquals('BKMUDD002', $numbers[1]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('receipts.store'), []);

        $response->assertSessionHasErrors([
            'transaction_date',
            'cash_account_code',
            'transaction_account_code',
            'amount',
            'user_id',
        ]);
    }

    public function test_store_validates_amount_must_be_positive(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('receipts.store'), $this->validPayload(['amount' => 0]));

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_store_validates_cash_account_must_exist(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('receipts.store'), $this->validPayload([
            'cash_account_code' => 'NONEXISTENT',
        ]));

        $response->assertSessionHasErrors(['cash_account_code']);
    }

    public function test_regular_user_cannot_store_receipt(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->post(route('receipts.store'), $this->validPayload());

        $response->assertForbidden();
    }

    public function test_update_receipt_successfully(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('receipts.store'), $this->validPayload(['amount' => 500000]));
        $transaction = Transaction::first();

        $response = $this->put(route('receipts.update', $transaction->id), $this->validPayload([
            'amount' => 750000,
            'description' => 'Updated description',
        ]));

        $response->assertRedirect(route('receipts.index'));
        $response->assertSessionHas('success');

        $transaction->refresh();
        $this->assertEquals('Updated description', $transaction->description);
        
        $debitTotal  = $transaction->generalLedgers->sum('debit');
        $creditTotal = $transaction->generalLedgers->sum('credit');

        $this->assertEquals(750000, $debitTotal);
        $this->assertEquals(750000, $creditTotal);
    }

    public function test_destroy_receipt_successfully(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('receipts.store'), $this->validPayload());
        $transaction = Transaction::first();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('general_ledgers', 2);

        $response = $this->delete(route('receipts.destroy', $transaction->id));

        $response->assertRedirect(route('receipts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('general_ledgers', 0);
    }

    public function test_suggest_description_endpoint_returns_json(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('receipts.suggest-description', [
            'transaction_account_code' => $this->transactionAccount->id,
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => ['description'],
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_suggest_description_returns_known_suggestion(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->getJson(route('receipts.suggest-description', [
            'transaction_account_code' => $this->transactionAccount->id,
        ]));

        $response->assertOk();

        $this->assertStringContainsString(
            'Penerimaan BPPD',
            $response->json('data.description') ?? ''
        );
    }

    public function test_index_page_filters_receipts_by_financial_period(): void
    {
        $profile = OrganizationProfile::firstOrCreate(['id' => 1]);
        $profile->update([
            'financial_period_start' => '2026-02-01',
            'financial_period_end' => '2026-02-28',
        ]);

        $this->actingAs($this->financeStaff);

        $inPeriod = Transaction::create([
            'transaction_date' => '2026-02-15',
            'document_number' => 'BKMUDD001',
            'transaction_type' => 'PEMASUKAN',
            'user_id' => $this->financeStaff->id,
            'description' => 'Dalam Periode',
        ]);

        $outPeriod = Transaction::create([
            'transaction_date' => '2026-03-01',
            'document_number' => 'BKMUDD002',
            'transaction_type' => 'PEMASUKAN',
            'user_id' => $this->financeStaff->id,
            'description' => 'Luar Periode',
        ]);

        $response = $this->get(route('receipts.index'));

        $response->assertStatus(200);
        $response->assertSee('Dalam Periode');
        $response->assertDontSee('Luar Periode');
    }

    public function test_store_with_custom_document_number(): void
    {
        $this->actingAs($this->financeStaff);

        $payload = $this->validPayload([
            'document_number' => 'CUSTOM-DOC-123',
        ]);

        $response = $this->post(route('receipts.store'), $payload);

        $response->assertRedirect(route('receipts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'document_number' => 'CUSTOM-DOC-123',
        ]);
    }

    public function test_index_page_search_filters_by_all_table_columns(): void
    {
        $this->actingAs($this->financeStaff);

        $program = Program::create([
            'name' => 'Program Bencana Alam',
            'user_id' => $this->financeStaff->id,
        ]);

        Transaction::create([
            'transaction_date' => date('Y-m-d'),
            'document_number' => 'BKM-001',
            'transaction_type' => 'PEMASUKAN',
            'program_id' => $program->id,
            'user_id' => $this->financeStaff->id,
            'description' => 'Normal transaction',
        ]);

        Transaction::create([
            'transaction_date' => date('Y-m-d'),
            'document_number' => 'BKM-002',
            'transaction_type' => 'PEMASUKAN',
            'user_id' => $this->financeStaff->id,
            'description' => 'Other text',
        ]);

        $response = $this->get(route('receipts.index', ['search' => 'Bencana']));
        $response->assertStatus(200);
        $response->assertSee('BKM-001');
        $response->assertDontSee('BKM-002');
    }


    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'transaction_date'         => date('Y-m-d'),
            'cash_account_code'        => $this->cashAccount->id,
            'transaction_account_code' => $this->transactionAccount->id,
            'amount'                   => 1_000_000,
            'description'              => 'Test penerimaan kas',
            'reference'                => 'KWT-001',
            'user_id'                  => $this->financeStaff->id,
        ], $overrides);
    }
}
