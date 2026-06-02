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
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GeneralLedgerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $financeStaff;
    private User $regularUser;
    private ChartOfAccount $cashAccount;
    private ChartOfAccount $revenueAccount;
    private Program $program;

    protected function tearDown(): void
    {
        Cache::store('array')->flush();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Cache::store('array')->flush();

        // Setup active financial period (May 2026)
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

        $this->cashAccount = ChartOfAccount::create([
            'id'                     => '11001-00',
            'account_subcategory_id' => $cashSubcategory->id,
            'account_name'           => 'Kas Operasional',
            'normal_balance'         => 'D',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->revenueAccount = ChartOfAccount::create([
            'id'                     => '41001-00',
            'account_subcategory_id' => $cashSubcategory->id,
            'account_name'           => 'Pendapatan Usaha',
            'normal_balance'         => 'C',
            'financial_report_type_id' => $reportType->id,
        ]);

        $this->admin = User::create([
            'name'     => 'Admin PMI',
            'email'    => 'admin@example.test',
            'password' => 'password',
            'role'     => RoleEnum::ADMIN,
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

        $this->program = Program::create([
            'name'        => 'Program Sosial Nganjuk',
            'description' => 'Sosial kemasyarakatan',
            'user_id'     => $this->admin->id,
        ]);
    }

    public function test_guest_cannot_access_general_ledger(): void
    {
        $response = $this->get(route('general-ledger.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_general_ledger(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('general-ledger.index'));
        $response->assertForbidden();
    }

    public function test_finance_staff_and_admin_can_access_general_ledger(): void
    {
        $response = $this->actingAs($this->financeStaff)->get(route('general-ledger.index'));
        $response->assertOk();
        $response->assertSee('Buku Besar (General Ledger)');

        $responseAdmin = $this->actingAs($this->admin)->get(route('general-ledger.index'));
        $responseAdmin->assertOk();
    }

    public function test_general_ledger_renders_entries_with_proper_computations(): void
    {
        $transaction = Transaction::create([
            'transaction_date' => '2026-05-10',
            'document_number'  => 'BKMUDD001',
            'transaction_type' => TransactionTypeEnum::INCOME->value,
            'user_id'          => $this->financeStaff->id,
            'program_id'       => $this->program->id,
            'reference'        => 'REF-999',
            'description'      => 'Pemasukan Kas Abdimas',
        ]);

        $transaction->generalLedgers()->createMany([
            [
                'chart_of_account_id' => $this->cashAccount->id,
                'debit'               => 1500000,
                'credit'              => 0,
                'note'                => 'Debit Kas',
            ],
            [
                'chart_of_account_id' => $this->revenueAccount->id,
                'debit'               => 0,
                'credit'              => 1500000,
                'note'                => 'Kredit Pendapatan',
            ]
        ]);

        $response = $this->actingAs($this->financeStaff)->get(route('general-ledger.index'));
        $response->assertOk();

        // Check columns
        $response->assertSee('10/05/2026');
        $response->assertSee('BKMUDD001');
        $response->assertSee('Program Sosial Nganjuk');
        $response->assertSee('REF-999');
        $response->assertSee('Kas Operasional');
        $response->assertSee('Pendapatan Usaha');
        
        // Check debit / credit values and formats
        $response->assertSee('Rp 1.500.000');
        
        // Check BS/PL Impact
        // Debit Kas: Debit 1.500.000, Credit 0 -> BS = 1.500.000 (positive, emerald), PL = -1.500.000 (negative, red)
        $response->assertSee('Rp 1.500.000'); // BS Impact
        $response->assertSee('-Rp 1.500.000'); // PL Impact
        
        $response->assertSee('Debit Kas');
        $response->assertSee('Kredit Pendapatan');
    }

    public function test_general_ledger_filters_by_financial_period(): void
    {
        // Transaction inside period (May 2026)
        $txIn = Transaction::create([
            'transaction_date' => '2026-05-15',
            'document_number'  => 'BKMUDD001',
            'transaction_type' => TransactionTypeEnum::INCOME->value,
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Pemasukan Kas Periode Aktif',
        ]);
        $txIn->generalLedgers()->create([
            'chart_of_account_id' => $this->cashAccount->id,
            'debit'               => 500000,
            'credit'              => 0,
            'note'                => 'Aktif',
        ]);

        // Transaction outside period (June 2026)
        $txOut = Transaction::create([
            'transaction_date' => '2026-06-01',
            'document_number'  => 'BKMUDD002',
            'transaction_type' => TransactionTypeEnum::INCOME->value,
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Pemasukan Kas Luar Periode',
        ]);
        $txOut->generalLedgers()->create([
            'chart_of_account_id' => $this->cashAccount->id,
            'debit'               => 700000,
            'credit'              => 0,
            'note'                => 'Luar',
        ]);

        $response = $this->actingAs($this->financeStaff)->get(route('general-ledger.index'));
        $response->assertOk();
        $response->assertSee('Pemasukan Kas Periode Aktif');
        $response->assertDontSee('Pemasukan Kas Luar Periode');
    }

    public function test_general_ledger_global_search(): void
    {
        // Create matching transaction
        $tx = Transaction::create([
            'transaction_date' => '2026-05-10',
            'document_number'  => 'BKMUDD-UNIQUE',
            'transaction_type' => TransactionTypeEnum::INCOME->value,
            'user_id'          => $this->financeStaff->id,
            'description'      => 'Pencarian Khusus',
        ]);
        $tx->generalLedgers()->create([
            'chart_of_account_id' => $this->cashAccount->id,
            'debit'               => 200000,
            'credit'              => 0,
            'note'                => 'MatchMe',
        ]);

        $responseMatched = $this->actingAs($this->financeStaff)->get(route('general-ledger.index', ['search' => 'UNIQUE']));
        $responseMatched->assertOk();
        $responseMatched->assertSee('BKMUDD-UNIQUE');

        $responseUnmatched = $this->actingAs($this->financeStaff)->get(route('general-ledger.index', ['search' => 'NOMATCH']));
        $responseUnmatched->assertOk();
        $responseUnmatched->assertDontSee('BKMUDD-UNIQUE');
    }
}
