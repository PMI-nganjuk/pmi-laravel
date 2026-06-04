<?php

namespace App\Http\Controllers;

use App\Data\CashDisbursementData;
use App\Http\Requests\StoreCashDisbursementRequest;
use App\Models\Transaction;
use App\Services\CashDisbursementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashDisbursementController extends Controller
{
    public function __construct(
        protected CashDisbursementService $service,
    ) {}

    public function index(Request $request): View
    {
        return view('pages.disbursements', $this->service->getPageData($request->query()));
    }

    public function store(StoreCashDisbursementRequest $request): RedirectResponse
    {
        try {
            $validated             = $request->validated();
            $validated['user_id']  = $validated['user_id'] ?? $request->user()->id;

            $this->service->store(CashDisbursementData::fromArray($validated));

            return redirect()->route('disbursements.index')->with('success', 'Pengeluaran kas berhasil disimpan!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan pengeluaran kas: ' . $e->getMessage());
        }
    }

    public function getCashAccounts(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->getPageData()['cashAccounts'],
        ]);
    }

    public function getTransactionAccounts(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->getPageData()['transactionAccounts'],
        ]);
    }

    public function suggestDescription(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_account_code' => ['required', 'string'],
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'description' => $this->service->suggestDescription(
                    $request->string('transaction_account_code')->toString()
                ),
            ],
        ]);
    }

    public function update(StoreCashDisbursementRequest $request, Transaction $transaction): RedirectResponse
    {
        try {
            $validated            = $request->validated();
            $validated['user_id'] = $validated['user_id'] ?? $request->user()->id;

            $this->service->update($transaction, CashDisbursementData::fromArray($validated));

            return redirect()->route('disbursements.index')->with('success', 'Pengeluaran kas berhasil diperbarui!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui pengeluaran kas: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        try {
            $this->service->destroy($transaction);

            return redirect()->route('disbursements.index')->with('success', 'Pengeluaran kas berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->route('disbursements.index')->with('error', 'Gagal menghapus pengeluaran kas: ' . $e->getMessage());
        }
    }
}
