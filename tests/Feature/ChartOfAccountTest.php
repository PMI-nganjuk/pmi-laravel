<?php

namespace Tests\Feature;

use App\Enums\EntryTypeEnum;
use App\Enums\RoleEnum;
use App\Models\CategoryOne;
use App\Models\CategoryTwo;
use App\Models\ChartOfAccount;
use App\Models\ReportTypes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountTest extends TestCase
{
    use RefreshDatabase;

    private ReportTypes $balanceSheetReportType;
    private ReportTypes $incomeStatementReportType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'name' => 'Admin PMI',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => RoleEnum::ADMIN,
        ]));

        CategoryOne::create([
            'category_code' => '1',
            'category_name' => 'Aset (Assets)',
        ]);

        CategoryTwo::create([
            'category_one' => '1',
            'category_code' => '11',
            'category_name' => 'Aset Lancar (Current Assets)',
        ]);

        CategoryTwo::create([
            'category_one' => '1',
            'category_code' => '12',
            'category_name' => 'Aset Tetap (Fixed Assets)',
        ]);

        $this->balanceSheetReportType = ReportTypes::create([
            'report_name' => 'Neraca (Balance Sheet)',
        ]);

        $this->incomeStatementReportType = ReportTypes::create([
            'report_name' => 'Laba Rugi (Income Statement)',
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
            'category_two' => '11',
            'account_name' => 'Kas Operasional',
            'entry_type' => EntryTypeEnum::DEBIT->value,
            'report_type_id' => $this->balanceSheetReportType->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('coa.store'), []);

        $response->assertSessionHasErrors([
            'category_one',
            'category_two',
            'id',
            'account_name',
            'entry_type',
            'report_type_id',
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
            'report_type_id' => $this->incomeStatementReportType->id,
        ]));

        $response->assertRedirect(route('coa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('chart_of_accounts', [
            'id' => '111001 - 00',
            'account_name' => 'Kas Utama',
            'report_type_id' => $this->incomeStatementReportType->id,
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

    public function test_category_two_endpoint_returns_standard_json_response(): void
    {
        $response = $this->getJson(route('coa.category-two', [
            'category_one' => '1',
        ]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                '11' => 'Aset Lancar (Current Assets)',
                '12' => 'Aset Tetap (Fixed Assets)',
            ],
        ]);
    }

    public function test_generate_code_endpoint_returns_next_account_code(): void
    {
        ChartOfAccount::create($this->validChartOfAccountAttributes([
            'id' => '111001 - 00',
        ]));

        $response = $this->getJson(route('coa.generate-code', [
            'category_one' => '1',
            'category_two' => '11',
        ]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'code' => '111002 - 00',
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
            'entry_type' => EntryTypeEnum::DEBIT->value,
            'report_type_id' => $this->balanceSheetReportType->id,
            'sort_by' => 'account_name',
            'sort_dir' => 'desc',
        ]));

        $response->assertOk();
        $response->assertSee('Kas Operasional');
    }

    private function validFormPayload(array $overrides = []): array
    {
        return array_merge([
            'category_one' => '1',
            'category_two' => '11',
            'id' => '111001 - 00',
            'account_name' => 'Kas Operasional',
            'entry_type' => EntryTypeEnum::DEBIT->value,
            'report_type_id' => $this->balanceSheetReportType->id,
        ], $overrides);
    }

    private function validChartOfAccountAttributes(array $overrides = []): array
    {
        return array_merge([
            'id' => '111001 - 00',
            'category_two' => '11',
            'account_name' => 'Kas Operasional',
            'entry_type' => EntryTypeEnum::DEBIT->value,
            'report_type_id' => $this->balanceSheetReportType->id,
        ], $overrides);
    }
}
