<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\JournalEntryTypeEnum;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use App\Models\ChartOfAccount;
use App\Models\FinancialReportType;
use App\Models\OrganizationProfile;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdjustingEntryTest extends TestCase
{
    use RefreshDatabase;

    private User             $financeStaff;
    private User             $regularUser;
    private ChartOfAccount   $debitAccount;
    private ChartOfAccount   $creditAccount;

    protected function tearDown(): void
    {
        Cache::store('array')->flush();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Cache::store('array')->flush();

        // Setup active financial period to May 2026
        $profile = OrganizationProfile::firstOrCreate(['id' => 1]);
        $profile->update([
            'financial_period_start' => '2026-05-01',
            'financial_period_end'   => '2026-05-31',
        ]);

        $reportType = FinancialReportType::create(['name' => 'Neraca']);
        $assetCategory = AccountCategory::create(['name' => 'Aset']);
        $cashSubcategory = AccountSubcategory::create([
            'account_category_id' => $assetCategory->id,
            'name'                => 'Kas dan Setara Kas',
        ]);

        $this->debitAccount = ChartOfAccount::create([
            'id'                     => '11001-00',
            'account_subcategory_id' => $cashSubcategory->id,
            'account_name'           => 'Kas Operasional',
            'normal_balance'         => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->creditAccount = ChartOfAccount::create([
            'id'                     => '12001-00',
            'account_subcategory_id' => $cashSubcategory->id,
            'account_name'           => 'Piutang Operasional',
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

    public function test_finance_staff_can_access_adjusting_entries_index(): void
    {
        $response = $this->actingAs($this->financeStaff)->get(route('adjusting-entries.index'));

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_adjusting_entries(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('adjusting-entries.index'));

        $response->assertForbidden();
    }

    public function test_store_creates_adjustment_transaction_and_balanced_gl_entries(): void
    {
        $this->actingAs($this->financeStaff);

        $response = $this->post(route('adjusting-entries.store'), $this->validPayload());

        $response->assertRedirect(route('adjusting-entries.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('general_ledgers', 2);

        $transaction = Transaction::first();
        $this->assertEquals(TransactionTypeEnum::ADJUSTMENT, $transaction->transaction_type);

        $debitGl = $transaction->generalLedgers->first(fn($gl) => $gl->chart_of_account_id === $this->debitAccount->id);
        $creditGl = $transaction->generalLedgers->first(fn($gl) => $gl->chart_of_account_id === $this->creditAccount->id);

        $this->assertNotNull($debitGl);
        $this->assertEquals(500000, (float) $debitGl->debit);
        $this->assertEquals(0, (float) $debitGl->credit);
        $this->assertEquals(JournalEntryTypeEnum::ADJUSTING_ENTRIES->value, $debitGl->note);

        $this->assertNotNull($creditGl);
        $this->assertEquals(0, (float) $creditGl->debit);
        $this->assertEquals(500000, (float) $creditGl->credit);
        $this->assertEquals(JournalEntryTypeEnum::ADJUSTING_ENTRIES->value, $creditGl->note);
    }

    public function test_store_validates_active_financial_period(): void
    {
        $this->actingAs($this->financeStaff);

        // Date outside May 2026 (active period)
        $payload = $this->validPayload(['transaction_date' => '2026-06-01']);

        $response = $this->post(route('adjusting-entries.store'), $payload);

        $response->assertSessionHasErrors(['transaction_date']);
    }

    public function test_store_applies_beginning_balances_prefix(): void
    {
        $this->actingAs($this->financeStaff);

        $payload = $this->validPayload([
            'journal_entry_type' => JournalEntryTypeEnum::BEGINNING_BALANCES->value,
            'description'        => 'Setup Awal Kas',
        ]);

        $this->post(route('adjusting-entries.store'), $payload);

        $transaction = Transaction::first();
        $this->assertStringStartsWith('[SALDO AWAL]', $transaction->description);
        $this->assertEquals('[SALDO AWAL] Setup Awal Kas', $transaction->description);

        $glNotes = $transaction->generalLedgers->pluck('note')->toArray();
        $this->assertEquals([JournalEntryTypeEnum::BEGINNING_BALANCES->value, JournalEntryTypeEnum::BEGINNING_BALANCES->value], $glNotes);
    }

    public function test_store_prevents_same_debit_and_credit_accounts(): void
    {
        $this->actingAs($this->financeStaff);

        $payload = $this->validPayload([
            'credit_account_id' => $this->debitAccount->id, // Same account
        ]);

        $response = $this->post(route('adjusting-entries.store'), $payload);

        $response->assertSessionHasErrors(['credit_account_id']);
    }

    public function test_update_cross_feature_overrides_reference_with_original_cash_account(): void
    {
        $this->actingAs($this->financeStaff);

        // Create a transaction of type INCOME (Pemasukan Kas) originally
        $transaction = Transaction::create([
            'transaction_date' => '2026-05-15',
            'document_number'  => 'BKMUDD001',
            'transaction_type' => TransactionTypeEnum::INCOME->value,
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Original Receipt',
            'reference'        => 'REF-INCOME',
        ]);

        // General Ledger: Debit -> Kas (debitAccount), Credit -> Pendapatan (creditAccount)
        $transaction->generalLedgers()->createMany([
            [
                'chart_of_account_id' => $this->debitAccount->id, // Cash account
                'debit'               => 1000000,
                'credit'              => 0,
                'note'                => 'Penerimaan kas',
            ],
            [
                'chart_of_account_id' => $this->creditAccount->id,
                'debit'               => 0,
                'credit'              => 1000000,
                'note'                => 'Pendapatan',
            ]
        ]);

        // Update it via Jurnal Penyesuaian (which forces it to become an ADJUSTMENT type)
        $response = $this->put(route('adjusting-entries.update', $transaction->id), $this->validPayload([
            'transaction_date' => '2026-05-20',
            'reference'        => 'NEW-REF-ATTEMPT', // Should be overridden by the cash account code ($this->debitAccount->id)
        ]));

        $response->assertRedirect(route('adjusting-entries.index'));

        $transaction->refresh();
        $this->assertEquals(TransactionTypeEnum::ADJUSTMENT, $transaction->transaction_type);
        // Reference is overridden by cash account code of the original receipt (which was $this->debitAccount->id)
        $this->assertEquals($this->debitAccount->id, $transaction->reference);
    }

    public function test_destroy_adjusting_entry(): void
    {
        $this->actingAs($this->financeStaff);

        $this->post(route('adjusting-entries.store'), $this->validPayload());
        $transaction = Transaction::first();

        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('general_ledgers', 2);

        $response = $this->delete(route('adjusting-entries.destroy', $transaction->id));

        $response->assertRedirect(route('adjusting-entries.index'));
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('general_ledgers', 0);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'transaction_date'   => '2026-05-15', // Inside May 2026
            'debit_account_id'   => $this->debitAccount->id,
            'credit_account_id'  => $this->creditAccount->id,
            'amount'             => 500000,
            'description'        => 'Test jurnal penyesuaian',
            'reference'          => 'REF-001',
            'journal_entry_type' => JournalEntryTypeEnum::ADJUSTING_ENTRIES->value,
            'user_id'            => $this->financeStaff->id,
        ], $overrides);
    }
}
