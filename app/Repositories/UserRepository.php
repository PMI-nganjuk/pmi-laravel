<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Get paginated users with filters and sorting.
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleVal = $filters['role'] ?? null) {
            if (in_array($roleVal, RoleEnum::values())) {
                $query->where('role', $roleVal);
            }
        }

        $sortableFields = ['name', 'email', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? null, $sortableFields, true) ? $filters['sort_by'] : 'name';
        $sortDir = ($filters['sort_dir'] ?? null) === 'desc' ? 'desc' : 'asc';

        return $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new user.
     */
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);
        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): void
    {
        $user->delete();
    }
}
