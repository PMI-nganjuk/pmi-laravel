<?php

namespace App\Services;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * Get data required to render the view page.
     */
    public function getPageData(array $filters = []): array
    {
        return [
            'users' => $this->repository->getPaginated($filters),
            'roles' => RoleEnum::cases(),
        ];
    }

    /**
     * Store a newly registered user.
     */
    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            return $this->repository->create($data);
        });
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            return $this->repository->update($user, $data);
        });
    }

    /**
     * Delete an existing user.
     *
     * @throws ValidationException
     */
    public function delete(User $user): void
    {
        if (auth()->id() === $user->id) {
            throw new \Exception('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($user) {
            $this->repository->delete($user);
        });
    }
}
