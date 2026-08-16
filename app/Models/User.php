<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Role Constants
    public const ROLE_ADMIN             = 'Admin';
    public const ROLE_STAF_KEUANGAN     = 'Staf Keuangan';
    public const ROLE_KARYAWAN          = 'Karyawan';
    public const ROLE_PENGGUNA          = 'Pengguna Umum';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleEnum::class,
        ];
    }

    /**
     * Get all role values (for validation or Filament select options).
     */
    public static function getRoles(): array
    {
        return RoleEnum::values();
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(RoleEnum $role): bool
    {
        if ($role === RoleEnum::STAFF || $role === RoleEnum::USER) {
            return $this->role === RoleEnum::STAFF || $this->role === RoleEnum::USER;
        }

        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }
}
