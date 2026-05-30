<?php

namespace App\Repositories;

use App\Models\GeneralLedger;

class GeneralLedgerRepository
{
    public function createMany(array $entries): void
    {
        foreach ($entries as $entry) {
            GeneralLedger::create($entry);
        }
    }
}
