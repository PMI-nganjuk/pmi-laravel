<?php

namespace App\Services;

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
        ];
    }
}
