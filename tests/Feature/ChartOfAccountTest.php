<?php

namespace Tests\Feature;

use App\Enums\EntryTypeEnum;
use App\Enums\RoleEnum;
use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use App\Models\ChartOfAccount;
use App\Models\FinancialReportType;
use App\Models\User;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountTest extends TestCase
{
    use RefreshDatabase;

    private FinancialReportType $balanceSheetReportType;
    private FinancialReportType $incomeStatementReportType;
    private AccountCategory $assetCategory;
    private AccountSubcategory $currentAssetSubcategory;
    private AccountSubcategory $fixedAssetSubcategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'name' => 'Admin PMI',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => RoleEnum::ADMIN,
        ]));

        $this->assetCategory = AccountCategory::create([
            'name' => 'Aset (Assets)',
        ]);

        $this->currentAssetSubcategory = AccountSubcategory::create([
            'account_category_id' => $this->assetCategory->id,
            'name' => 'Aset Lancar (Current Assets)',
        ]);

        $this->fixedAssetSubcategory = AccountSubcategory::create([
            'account_category_id' => $this->assetCategory->id,
            'name' => 'Aset Tetap (Fixed Assets)',
        ]);

        $this->balanceSheetReportType = FinancialReportType::create([
            'name' => 'Neraca (Balance Sheet)',
        ]);

        $this->incomeStatementReportType = FinancialReportType::create([
            'name' => 'Laba Rugi (Income Statement)',
        ]);
    }

    public function test_index_page_renders_chart_of_account_data(): void
    {
        ChartOfAccount::create($this->validChartOfAccountAttributes([
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
        ]));

        $response = $this->get(route('coa.index'));

        $response->assertOk();
        $response->assertSee('Daftar Chart of Accounts');
        $response->assertSee('Kas Operasional');
        $response->assertSee('Aset Lancar (Current Assets)');
        $response->assertSee('Neraca (Balance Sheet)');
    }

    public function test_store_creates_chart_of_account(): void
    {
        $response = $this->post(route('coa.store'), $this->validFormPayload([
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
        ]));

        $response->assertRedirect(route('coa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('chart_of_accounts', [
            'id' => '111001 - 00',
            'account_subcategory_id' => $this->currentAssetSubcategory->id,
            'account_name' => 'Kas Operasional',
            'normal_balance' => EntryTypeEnum::DEBIT->value,
            'financial_report_type_id' => $this->balanceSheetReportType->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('coa.store'), []);

        $response->assertSessionHasErrors([
            'account_category_id',
            'account_subcategory_id',
            'id',
            'account_name',
            'normal_balance',
            'financial_report_type_id',
        ]);
    }

    public function test_update_uses_route_model_binding_and_updates_chart_of_account(): void
    {
        $chartOfAccount = ChartOfAccount::create($this->validChartOfAccountAttributes([
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
        ]));

        $response = $this->put(route('coa.update', $chartOfAccount), $this->validFormPayload([
            'id' => '111001 - 00',
            'account_name' => 'Kas Utama',
            'financial_report_type_id' => $this->incomeStatementReportType->id,
        ]));

        $response->assertRedirect(route('coa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('chart_of_accounts', [
            'id' => '111001 - 00',
            'account_name' => 'Kas Utama',
            'financial_report_type_id' => $this->incomeStatementReportType->id,
        ]);
    }

    public function test_destroy_uses_route_model_binding_and_deletes_chart_of_account(): void
    {
        $chartOfAccount = ChartOfAccount::create($this->validChartOfAccountAttributes([
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
        ]));

        $response = $this->delete(route('coa.destroy', $chartOfAccount));

        $response->assertRedirect(route('coa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('chart_of_accounts', [
            'id' => '111001 - 00',
        ]);
    }

    public function test_account_subcategory_endpoint_returns_standard_json_response(): void
    {
        $response = $this->getJson(route('coa.account-subcategory', [
            'account_category_id' => $this->assetCategory->id,
        ]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                $this->currentAssetSubcategory->id => 'Aset Lancar (Current Assets)',
                $this->fixedAssetSubcategory->id => 'Aset Tetap (Fixed Assets)',
            ],
        ]);
    }

    public function test_generate_code_endpoint_returns_next_account_code(): void
    {
        $prefix = $this->assetCategory->id . $this->currentAssetSubcategory->id;
        ChartOfAccount::create($this->validChartOfAccountAttributes([
            'id' => $prefix . '001-00',
        ]));

        $response = $this->getJson(route('coa.generate-code', [
            'account_category_id' => $this->assetCategory->id,
            'account_subcategory_id' => $this->currentAssetSubcategory->id,
        ]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'code' => $prefix . '011-00',
            ],
        ]);
    }

    public function test_search_filter_and_sort_requests_render_successfully(): void
    {
        ChartOfAccount::create($this->validChartOfAccountAttributes([
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
        ]));

        $response = $this->get(route('coa.index', [
            'search' => 'Kas',
            'normal_balance' => EntryTypeEnum::DEBIT->value,
            'financial_report_type_id' => $this->balanceSheetReportType->id,
            'sort_by' => 'account_name',
            'sort_dir' => 'desc',
        ]));

        $response->assertOk();
        $response->assertSee('Kas Operasional');
    }

    public function test_updating_chart_of_account_clears_cached_accounts(): void
    {
        $repo = app(ChartOfAccountRepository::class);

        $initialCount = $repo->getCashAccounts()->count();

        $repo->create($this->validChartOfAccountAttributes([
            'id' => '111002 - 00',
            'account_name' => 'Kas Tambahan Baru',
        ]));

        $updatedCashAccounts = $repo->getCashAccounts();

        $this->assertEquals($initialCount + 1, $updatedCashAccounts->count());
        $this->assertTrue($updatedCashAccounts->contains('account_name', 'Kas Tambahan Baru'));
    }

    private function validFormPayload(array $overrides = []): array
    {
        return array_merge([
            'account_category_id' => $this->assetCategory->id,
            'account_subcategory_id' => $this->currentAssetSubcategory->id,
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
            'normal_balance' => EntryTypeEnum::DEBIT->value,
            'financial_report_type_id' => $this->balanceSheetReportType->id,
        ], $overrides);
    }

    private function validChartOfAccountAttributes(array $overrides = []): array
    {
        return array_merge([
            'id' => '111001 - 00',
            'account_subcategory_id' => $this->currentAssetSubcategory->id,
            'account_name' => 'Kas Operasional',
            'normal_balance' => EntryTypeEnum::DEBIT->value,
            'financial_report_type_id' => $this->balanceSheetReportType->id,
        ], $overrides);
    }
}
