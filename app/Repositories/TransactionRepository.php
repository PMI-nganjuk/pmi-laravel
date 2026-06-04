<?php

namespace App\Repositories;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Services\OrganizationProfileService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionRepository
{
    public function create(array $attributes): Transaction
    {
        return Transaction::create($attributes);
    }

    /**
     * Retrieve the last document number matching a given prefix.
     * Used by DocumentNumberService to determine the next sequence.
     */
    public function getLastDocumentNumberByPrefix(string $prefix): ?string
    {
        return Transaction::where('document_number', 'like', $prefix . '%')
            ->orderByDesc('document_number')
            ->value('document_number');
    }

    /**
     * Retrieve paginated transactions for a given type with optional filters.
     * Applies financial period filter at database level for performance.
     * Supports global search across document_number, description, reference,
     * program name, user name, COA name, and numeric amount fields.
     */
    public function getPaginated(
        TransactionTypeEnum $type,
        array $filters = [],
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Transaction::with(['program', 'user', 'generalLedgers.chartOfAccount'])
            ->where('transaction_type', $type->value);

        try {
            $profile = app(OrganizationProfileService::class)->getProfile();
            if ($profile && $profile->financial_period_start && $profile->financial_period_end) {
                $query->whereBetween('transaction_date', [
                    $profile->financial_period_start->format('Y-m-d'),
                    $profile->financial_period_end->format('Y-m-d'),
                ]);
            }
        } catch (\Throwable $e) {
            // Keep it unfiltered if DB is not migrated or profile service fails
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('program', function ($qp) use ($search) {
                      $qp->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('generalLedgers.chartOfAccount', function ($qc) use ($search) {
                      $qc->where('account_name', 'like', "%{$search}%")
                         ->orWhere('id', 'like', "%{$search}%");
                  });

                if (is_numeric($search)) {
                    $q->orWhereHas('generalLedgers', function ($qg) use ($search) {
                        $qg->where('debit', 'like', "%{$search}%")
                           ->orWhere('credit', 'like', "%{$search}%");
                    });
                }
            });
        }

        if ($date = $filters['transaction_date'] ?? null) {
            $query->whereDate('transaction_date', $date);
        }

        $allowedSorts = ['transaction_date', 'document_number', 'created_at'];
        $sortBy  = in_array($filters['sort_by'] ?? null, $allowedSorts, true) ? $filters['sort_by'] : 'transaction_date';
        $sortDir = ($filters['sort_dir'] ?? null) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
    }
}
