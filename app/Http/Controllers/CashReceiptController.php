<?php

namespace App\Http\Controllers;

use App\Data\CashReceiptData;
use App\Http\Requests\StoreCashReceiptRequest;
use App\Services\CashReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\View\View;

class CashReceiptController extends Controller
{
    public function __construct(
        protected CashReceiptService $service,
    ) {}

    public function index(Request $request): View
    {
        return view('pages.receipts', $this->service->getPageData($request->query()));
    }

    public function store(StoreCashReceiptRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = $validated['user_id'] ?? $request->user()->id;

            $this->service->store(CashReceiptData::fromArray($validated));

            return redirect()->route('receipts.index')->with('success', 'Penerimaan kas berhasil disimpan!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan penerimaan kas: ' . $e->getMessage());
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

    public function update(StoreCashReceiptRequest $request, Transaction $transaction): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = $validated['user_id'] ?? $request->user()->id;

            $this->service->update($transaction, CashReceiptData::fromArray($validated));

            return redirect()->route('receipts.index')->with('success', 'Penerimaan kas berhasil diperbarui!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui penerimaan kas: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        try {
            $this->service->destroy($transaction);

            return redirect()->route('receipts.index')->with('success', 'Penerimaan kas berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->route('receipts.index')->with('error', 'Gagal menghapus penerimaan kas: ' . $e->getMessage());
        }
    }
}
