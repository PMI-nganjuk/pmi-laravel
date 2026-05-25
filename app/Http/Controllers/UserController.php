<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Inject the UserService.
     */
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-users');

        $data = $this->userService->getPageData($request->all());

        return view('users.index', [
            'users' => $data['users'],
            'roles' => $data['roles'],
        ]);
    }

    /**
     * Store a newly created user in storage (registration).
     */
    public function store(UserRequest $request)
    {
        Gate::authorize('manage-users');

        $user = $this->userService->store($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Pengguna ' . $user->name . ' berhasil didaftarkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        Gate::authorize('manage-users');

        $this->userService->update($user, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('manage-users');

        try {
            $name = $user->name;
            $this->userService->delete($user);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna ' . $name . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', $e->getMessage());
        }
    }
}
