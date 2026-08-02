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

        $perPage = (int) ($filters['per_page'] ?? $perPage);

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

        if (!empty($filters['filter_tanggal'])) {
            $query->where('transaction_date', 'like', '%' . $filters['filter_tanggal'] . '%');
        }
        if (!empty($filters['filter_no_dokumen'])) {
            $query->where('document_number', 'like', '%' . $filters['filter_no_dokumen'] . '%');
        }
        if (!empty($filters['filter_program'])) {
            $query->whereHas('program', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['filter_program'] . '%');
            });
        }
        if (!empty($filters['filter_referensi'])) {
            $query->where('reference', 'like', '%' . $filters['filter_referensi'] . '%');
        }
        if (!empty($filters['filter_keterangan'])) {
            $query->where('description', 'like', '%' . $filters['filter_keterangan'] . '%');
        }
        if (!empty($filters['filter_pic'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['filter_pic'] . '%');
            });
        }
        if (!empty($filters['filter_coa'])) {
            $query->whereHas('generalLedgers.chartOfAccount', function ($q) use ($filters) {
                $q->where('account_name', 'like', '%' . $filters['filter_coa'] . '%')
                  ->orWhere('id', 'like', '%' . $filters['filter_coa'] . '%');
            });
        }
        if (!empty($filters['filter_nominal'])) {
            $query->whereHas('generalLedgers', function ($q) use ($filters) {
                $q->where('debit', 'like', '%' . $filters['filter_nominal'] . '%')
                  ->orWhere('credit', 'like', '%' . $filters['filter_nominal'] . '%');
            });
        }
        if (!empty($filters['filter_debit'])) {
            $query->whereHas('generalLedgers', function ($q) use ($filters) {
                $q->where('debit', 'like', '%' . $filters['filter_debit'] . '%');
            });
        }
        if (!empty($filters['filter_kredit'])) {
            $query->whereHas('generalLedgers', function ($q) use ($filters) {
                $q->where('credit', 'like', '%' . $filters['filter_kredit'] . '%');
            });
        }
        if (!empty($filters['filter_jenis_entri'])) {
            $query->whereHas('generalLedgers', function ($q) use ($filters) {
                $q->where('note', 'like', '%' . $filters['filter_jenis_entri'] . '%');
            });
        }

        $allowedSorts = ['transaction_date', 'document_number', 'created_at'];
        $sortBy  = in_array($filters['sort_by'] ?? null, $allowedSorts, true) ? $filters['sort_by'] : 'transaction_date';
        $sortDir = ($filters['sort_dir'] ?? null) === 'asc' ? 'asc' : 'desc';

        if ($perPage === -1) {
            $perPage = $query->count();
            if ($perPage === 0) $perPage = 1; 
        }

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
    }
}
