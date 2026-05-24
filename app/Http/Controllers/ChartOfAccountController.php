<?php

namespace App\Http\Controllers;

use App\Data\ChartOfAccountData;
use App\Http\Requests\GenerateAccountCodeRequest;
use App\Http\Requests\GetCategoryTwoOptionsRequest;
use App\Http\Requests\StoreChartOfAccountRequest;
use App\Models\ChartOfAccount;
use App\Services\ChartOfAccountService;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function __construct(
        protected ChartOfAccountService $service
    ) {}

    public function index(Request $request)
    {
        return view('pages.coa', $this->service->getPageData($request->query()));
    }

    public function create(Request $request)
    {
        return $this->index($request);
    }

    public function store(StoreChartOfAccountRequest $request)
    {
        try {
            $this->service->store(ChartOfAccountData::fromArray($request->validated()));

            return redirect()->route('coa.index')->with('success', 'COA berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan COA: ' . $e->getMessage());
        }
    }

    public function update(StoreChartOfAccountRequest $request, ChartOfAccount $coa)
    {
        try {
            $this->service->update($coa, ChartOfAccountData::fromArray($request->validated()));

            return redirect()->route('coa.index')->with('success', 'COA berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui COA: ' . $e->getMessage());
        }
    }

    public function destroy(ChartOfAccount $coa)
    {
        try {
            $this->service->delete($coa);

            return redirect()->route('coa.index')->with('success', 'COA berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus COA: ' . $e->getMessage());
        }
    }

    public function getCategoryTwo(GetCategoryTwoOptionsRequest $request)
    {
        $validated = $request->validated();

        return response()->json([
            'success' => true,
            'data' => $this->service->getCategoryTwoOptions($validated['category_one']),
        ]);
    }

    public function generateCode(GenerateAccountCodeRequest $request)
    {
        $validated = $request->validated();

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $this->service->generateAccountCode(
                    $validated['category_one'],
                    $validated['category_two'],
                ),
            ],
        ]);
    }
}
