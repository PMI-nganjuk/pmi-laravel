<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\RoleEnum;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and users for each test run
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_seeded_users_are_assigned_correct_roles(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole(RoleEnum::ADMIN));

        $stafKeuangan = User::where('email', 'stafkeuangan@pmi-nganjuk.or.id')->first();
        $this->assertNotNull($stafKeuangan);
        $this->assertTrue($stafKeuangan->hasRole(RoleEnum::FINANCE_STAFF));

        $karyawan = User::where('email', 'karyawan@pmi-nganjuk.or.id')->first();
        $this->assertNotNull($karyawan);
        $this->assertTrue($karyawan->hasRole(RoleEnum::STAFF));
        $this->assertTrue($karyawan->hasRole(RoleEnum::USER)); // STAFF has USER role

        $pengguna = User::where('email', 'pengguna@pmi-nganjuk.or.id')->first();
        $this->assertNotNull($pengguna);
        $this->assertTrue($pengguna->hasRole(RoleEnum::USER));
        $this->assertTrue($pengguna->hasRole(RoleEnum::STAFF)); // USER has STAFF role
    }

    public function test_has_any_role_checks(): void
    {
        $karyawan = User::where('email', 'karyawan@pmi-nganjuk.or.id')->first();
        $this->assertTrue($karyawan->hasAnyRole([RoleEnum::STAFF, RoleEnum::ADMIN]));
        $this->assertTrue($karyawan->hasAnyRole([RoleEnum::USER, RoleEnum::ADMIN])); // can check by equivalent USER role
    }

    public function test_admin_bypasses_all_gate_permission_checks(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $karyawan = User::where('email', 'karyawan@pmi-nganjuk.or.id')->first();

        // Admin has all permission checks bypassed by Gate::before
        $this->assertTrue($admin->can('manage-anything-random'));

        // Karyawan does NOT bypass since they aren't Admin
        $this->assertFalse($karyawan->can('manage-anything-random'));
    }

    public function test_role_constants_and_helper_methods(): void
    {
        $expectedRoles = [
            'Admin',
            'Staf Keuangan',
            'Karyawan',
            'Pengguna Umum',
        ];

        $this->assertEquals($expectedRoles, User::getRoles());
        $this->assertEquals('Admin', User::ROLE_ADMIN);
        $this->assertEquals('Staf Keuangan', User::ROLE_STAF_KEUANGAN);
        $this->assertEquals('Karyawan', User::ROLE_KARYAWAN);
        $this->assertEquals('Pengguna Umum', User::ROLE_PENGGUNA);
    }
}
