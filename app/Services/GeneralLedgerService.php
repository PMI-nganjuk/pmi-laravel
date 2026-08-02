<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Program;
use App\Models\User;
use App\Repositories\GeneralLedgerRepository;

class GeneralLedgerService
{
    public function __construct(
        protected GeneralLedgerRepository $repository
    ) {}

    /**
     * Get data required to render the general ledger report page.
     */
    public function getPageData(array $filters = []): array
    {
        return [
            'entries' => $this->repository->getPaginated($filters),
            'programs' => Program::orderBy('name')->get(),
            'coas' => ChartOfAccount::orderBy('id')->get(),
            'users' => User::orderBy('name')->get(),
        ];
    }
}
