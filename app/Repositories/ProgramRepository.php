<?php

namespace App\Repositories;

use App\Models\Program;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

class ProgramRepository
{
    /**
     * Get paginated programs with filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Program::with('user');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($userId = $filters['user_id'] ?? null) {
            $query->where('user_id', $userId);
        }

        $allowedSorts = ['id', 'name', 'description', 'user_id', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? null, $allowedSorts, true) ? $filters['sort_by'] : 'id';
        $sortDir = ($filters['sort_dir'] ?? null) === 'desc' ? 'desc' : 'asc';

        return $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new program.
     */
    public function create(array $attributes): Program
    {
        return Program::create($attributes);
    }

    /**
     * Update an existing program.
     */
    public function update(Program $program, array $attributes): Program
    {
        $program->update($attributes);
        return $program;
    }

    /**
     * Delete a program.
     */
    public function delete(Program $program): void
    {
        $program->delete();
    }

    /**
     * Get users pluck name and id for options list.
     */
    public function getUserOptions(): SupportCollection
    {
        return User::orderBy('name')->pluck('name', 'id');
    }
}
