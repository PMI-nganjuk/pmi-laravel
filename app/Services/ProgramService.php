<?php

namespace App\Services;

use App\Models\Program;
use App\Repositories\ProgramRepository;
use Illuminate\Support\Facades\DB;

class ProgramService
{
    public function __construct(
        protected ProgramRepository $repository
    ) {}

    /**
     * Get data required to render the view page.
     */
    public function getPageData(array $filters = []): array
    {
        return [
            'userOptions' => $this->repository->getUserOptions(),
            'programs' => $this->repository->getPaginated($filters),
        ];
    }

    /**
     * Store a new program.
     */
    public function store(array $data): Program
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    /**
     * Update an existing program.
     */
    public function update(Program $program, array $data): Program
    {
        return DB::transaction(function () use ($program, $data) {
            return $this->repository->update($program, $data);
        });
    }

    /**
     * Delete an existing program.
     */
    public function delete(Program $program): void
    {
        DB::transaction(function () use ($program) {
            $this->repository->delete($program);
        });
    }
}
