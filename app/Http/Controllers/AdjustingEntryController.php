<?php

namespace App\Http\Controllers;

use App\Data\AdjustingEntryData;
use App\Http\Requests\StoreAdjustingEntryRequest;
use App\Models\Transaction;
use App\Services\AdjustingEntryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdjustingEntryController extends Controller
{
    public function __construct(
        protected AdjustingEntryService $service,
    ) {}

    public function index(Request $request): View
    {
        return view('pages.adjusting-entries', $this->service->getPageData($request->query()));
    }

    public function store(StoreAdjustingEntryRequest $request): RedirectResponse
    {
        try {
            $validated            = $request->validated();
            $validated['user_id'] = $validated['user_id'] ?? $request->user()->id;

            $this->service->store(AdjustingEntryData::fromArray($validated));

            return redirect()->route('adjusting-entries.index')->with('success', 'Jurnal penyesuaian berhasil disimpan!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan jurnal penyesuaian: ' . $e->getMessage());
        }
    }

    public function update(StoreAdjustingEntryRequest $request, Transaction $transaction): RedirectResponse
    {
        try {
            $validated            = $request->validated();
            $validated['user_id'] = $validated['user_id'] ?? $request->user()->id;

            $this->service->update($transaction, AdjustingEntryData::fromArray($validated));

            return redirect()->route('adjusting-entries.index')->with('success', 'Jurnal penyesuaian berhasil diperbarui!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui jurnal penyesuaian: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        try {
            $this->service->destroy($transaction);

            return redirect()->route('adjusting-entries.index')->with('success', 'Jurnal penyesuaian berhasil dihapus!');
        } catch (\Throwable $e) {
            return redirect()->route('adjusting-entries.index')->with('error', 'Gagal menghapus jurnal penyesuaian: ' . $e->getMessage());
        }
    }
}
