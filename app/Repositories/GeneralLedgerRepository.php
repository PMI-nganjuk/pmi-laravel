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

