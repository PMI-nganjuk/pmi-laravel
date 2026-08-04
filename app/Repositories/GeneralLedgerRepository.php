<?php

namespace App\Repositories;

use App\Models\GeneralLedger;
use App\Services\OrganizationProfileService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GeneralLedgerRepository
{
    public function createMany(array $entries): void
    {
        foreach ($entries as $entry) {
            GeneralLedger::create($entry);
        }
    }

    public function getPaginated(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? $perPage);
        // allow "all" option if per_page is -1 or very large, but usually paginate handles it if we pass a large number
        if ($perPage === -1) {
            $perPage = 999999999;
        }
        $query = GeneralLedger::query()
            ->join('transactions', 'general_ledgers.transaction_id', '=', 'transactions.id')
            ->select('general_ledgers.*')
            ->with(['chartOfAccount', 'transaction.program', 'transaction.user']);

        try {
            $profile = app(OrganizationProfileService::class)->getProfile();
            if ($profile && $profile->financial_period_start && $profile->financial_period_end) {
                $query->whereBetween('transactions.transaction_date', [
                    $profile->financial_period_start->format('Y-m-d'),
                    $profile->financial_period_end->format('Y-m-d'),
                ]);
            }
        } catch (\Throwable $e) {
            // Keep it unfiltered if DB is not migrated or profile service fails
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('transactions.document_number', 'like', "%{$search}%")
                  ->orWhere('transactions.reference', 'like', "%{$search}%")
                  ->orWhere('transactions.description', 'like', "%{$search}%")
                  ->orWhere('general_ledgers.note', 'like', "%{$search}%")
                  ->orWhereHas('chartOfAccount', function ($qc) use ($search) {
                      $qc->where('account_name', 'like', "%{$search}%")
                         ->orWhere('id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transaction.program', function ($qp) use ($search) {
                      $qp->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transaction.user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['filter_tanggal'])) {
            $query->where('transactions.transaction_date', 'like', '%' . $filters['filter_tanggal'] . '%');
        }
        if (!empty($filters['filter_no_dokumen'])) {
            $query->where('transactions.document_number', 'like', '%' . $filters['filter_no_dokumen'] . '%');
        }
        if (!empty($filters['filter_program'])) {
            $query->whereHas('transaction.program', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['filter_program'] . '%');
            });
        }
        if (!empty($filters['filter_referensi'])) {
            $query->where('transactions.reference', 'like', '%' . $filters['filter_referensi'] . '%');
        }
        if (!empty($filters['filter_coa'])) {
            $query->whereHas('chartOfAccount', function ($q) use ($filters) {
                $q->where('account_name', 'like', '%' . $filters['filter_coa'] . '%')
                  ->orWhere('id', 'like', '%' . $filters['filter_coa'] . '%');
            });
        }
        if (!empty($filters['filter_debit'])) {
            $query->where('general_ledgers.debit', 'like', '%' . $filters['filter_debit'] . '%');
        }
        if (!empty($filters['filter_kredit'])) {
            $query->where('general_ledgers.credit', 'like', '%' . $filters['filter_kredit'] . '%');
        }
        if (!empty($filters['filter_keterangan'])) {
            $query->where('transactions.description', 'like', '%' . $filters['filter_keterangan'] . '%');
        }
        if (!empty($filters['filter_pic'])) {
            $query->whereHas('transaction.user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['filter_pic'] . '%');
            });
        }
        if (!empty($filters['filter_bs'])) {
            // BS Impact = debit - credit
            $query->whereRaw('(general_ledgers.debit - general_ledgers.credit) LIKE ?', ['%' . $filters['filter_bs'] . '%']);
        }
        if (!empty($filters['filter_pl'])) {
            // PL Impact = credit - debit
            $query->whereRaw('(general_ledgers.credit - general_ledgers.debit) LIKE ?', ['%' . $filters['filter_pl'] . '%']);
        }
        if (!empty($filters['filter_note'])) {
            $query->where('general_ledgers.note', 'like', '%' . $filters['filter_note'] . '%');
        }

        $sortBy = $filters['sort_by'] ?? 'transaction_date';
        $sortDir = ($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'transaction_date') {
            $query->orderBy('transactions.transaction_date', $sortDir);
        } elseif ($sortBy === 'document_number') {
            $query->orderBy('transactions.document_number', $sortDir);
        } else {
            $query->orderBy('general_ledgers.id', $sortDir);
        }

        $query->orderBy('general_ledgers.id', 'desc');

        return $query->paginate($perPage)->withQueryString();
    }
}

