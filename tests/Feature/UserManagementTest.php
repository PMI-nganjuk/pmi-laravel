<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\RoleEnum;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default roles and users
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Test that guests cannot access user management.
     */
    public function test_guest_cannot_access_user_management(): void
    {
        $response1 = $this->get('/dashboard/users');
        $response1->assertRedirect('/login');

        $response2 = $this->get('/dashboard/users/create');
        $response2->assertRedirect('/login');

        $response3 = $this->post('/dashboard/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => RoleEnum::STAFF->value,
            'password' => 'password123',
        ]);
        $response3->assertRedirect('/login');
    }

    /**
     * Test that non-admin authenticated users cannot access user management.
     */
    public function test_non_admin_cannot_access_user_management(): void
    {
        $nonAdmin = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response1 = $this->actingAs($nonAdmin)->get('/dashboard/users');
        $response1->assertStatus(403);

        $response2 = $this->actingAs($nonAdmin)->get('/dashboard/users/create');
        $response2->assertStatus(403);

        $response3 = $this->actingAs($nonAdmin)->post('/dashboard/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => RoleEnum::STAFF->value,
            'password' => 'password123',
        ]);
        $response3->assertStatus(403);
    }

    /**
     * Test that admin can view user management and the user registration form.
     */
    public function test_admin_can_access_user_management(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response1 = $this->actingAs($admin)->get('/dashboard/users');
        $response1->assertStatus(200);
        $response1->assertSee('Manajemen Akun');

        $response2 = $this->actingAs($admin)->get('/dashboard/users/create');
        $response2->assertStatus(200);
        $response2->assertSee('Formulir Registrasi Akun');
    }

    /**
     * Test that admin can successfully register a new user.
     */
    public function test_admin_can_register_new_user(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name' => 'Jane Doe',
            'email' => 'jane@pmi-nganjuk.or.id',
            'role' => RoleEnum::FINANCE_STAFF->value,
            'password' => 'securepassword123',
        ]);

        $response->assertRedirect('/dashboard/users');
        $response->assertSessionHas('success');

        // Assert user was created in DB
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Doe',
            'email' => 'jane@pmi-nganjuk.or.id',
            'role' => RoleEnum::FINANCE_STAFF->value,
        ]);
    }

    /**
     * Test that registering a user requires valid input data.
     */
    public function test_admin_registration_validation(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name' => '', // invalid
            'email' => 'not-an-email', // invalid
            'role' => 'InvalidRole', // invalid
            'password' => 'short', // invalid
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    /**
     * Test that admin can access the edit user form.
     */
    public function test_admin_can_access_edit_form(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $targetUser = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->get("/dashboard/users/{$targetUser->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Formulir Edit Akun');
        $response->assertSee($targetUser->name);
        $response->assertSee($targetUser->email);
    }

    /**
     * Test that admin can update user details without updating the password.
     */
    public function test_admin_can_update_user_without_password(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $targetUser = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->put("/dashboard/users/{$targetUser->id}", [
            'name' => 'Updated Manager Name',
            'email' => 'manager-updated@pmi-nganjuk.or.id',
            'role' => RoleEnum::FINANCIAL_MANAGER->value,
            'password' => '', // blank password should not change it
        ]);

        $response->assertRedirect('/dashboard/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Manager Name',
            'email' => 'manager-updated@pmi-nganjuk.or.id',
        ]);
    }

    /**
     * Test that admin can update user details and change password.
     */
    public function test_admin_can_update_user_with_new_password(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $targetUser = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->put("/dashboard/users/{$targetUser->id}", [
            'name' => 'Manager Name Updated Again',
            'email' => 'manager-updated-again@pmi-nganjuk.or.id',
            'role' => RoleEnum::FINANCIAL_MANAGER->value,
            'password' => 'newbrandnewpassword123',
        ]);

        $response->assertRedirect('/dashboard/users');
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newbrandnewpassword123', $targetUser->password));
    }

    /**
     * Test that admin update triggers validation.
     */
    public function test_admin_update_validation(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $targetUser = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->put("/dashboard/users/{$targetUser->id}", [
            'name' => '', // invalid
            'email' => 'not-an-email', // invalid
            'role' => 'InvalidRole', // invalid
            'password' => 'short', // invalid
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    /**
     * Test that admin can delete another user.
     */
    public function test_admin_can_delete_other_user(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $targetUser = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->delete("/dashboard/users/{$targetUser->id}");

        $response->assertRedirect('/dashboard/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    /**
     * Test that admin cannot delete their own account.
     */
    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->delete("/dashboard/users/{$admin->id}");

        $response->assertRedirect('/dashboard/users');
        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    /**
     * Test search filters.
     */
    public function test_user_index_search(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        // Create specific dummy user
        User::create([
            'name' => 'Budi Sudarsono',
            'email' => 'budi@pmi-nganjuk.or.id',
            'role' => RoleEnum::STAFF,
            'password' => 'password123',
        ]);

        // Search by name
        $response = $this->actingAs($admin)->get('/dashboard/users?search=Budi');
        $response->assertStatus(200);
        $response->assertSee('Budi Sudarsono');

        // Search by email
        $response = $this->actingAs($admin)->get('/dashboard/users?search=budi@pmi');
        $response->assertStatus(200);
        $response->assertSee('Budi Sudarsono');

        // Search that returns empty
        $response = $this->actingAs($admin)->get('/dashboard/users?search=NonExistentUser');
        $response->assertStatus(200);
        $response->assertSee('Tidak Ada Pengguna Ditemukan');
    }

    /**
     * Test role filters.
     */
    public function test_user_index_role_filter(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        // There should be a Financial Manager and Finance Staff seeded
        $response1 = $this->actingAs($admin)->get('/dashboard/users?role=' . urlencode(RoleEnum::FINANCIAL_MANAGER->value));
        $response1->assertStatus(200);
        $response1->assertSee('manager@pmi-nganjuk.or.id');
        $response1->assertDontSee('karyawan@pmi-nganjuk.or.id');

        $response2 = $this->actingAs($admin)->get('/dashboard/users?role=' . urlencode(RoleEnum::STAFF->value));
        $response2->assertStatus(200);
        $response2->assertSee('karyawan@pmi-nganjuk.or.id');
        $response2->assertDontSee('manager@pmi-nganjuk.or.id');
    }

    /**
     * Test sorting.
     */
    public function test_user_index_sorting(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        // Sorted by name asc (default)
        $response = $this->actingAs($admin)->get('/dashboard/users?sort_by=name&sort_dir=asc');
        $response->assertStatus(200);

        // Sorted by email desc
        $response = $this->actingAs($admin)->get('/dashboard/users?sort_by=email&sort_dir=desc');
        $response->assertStatus(200);
    }

    /**
     * Test pagination.
     */
    public function test_user_index_pagination(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        // Seed 15 extra users to trigger pagination (since we set limit to 10)
        for ($i = 0; $i < 15; $i++) {
            User::create([
                'name' => "Paginated User {$i}",
                'email' => "paginated.user{$i}@pmi-nganjuk.or.id",
                'role' => RoleEnum::STAFF,
                'password' => 'password123',
            ]);
        }

        // View page 1
        $response = $this->actingAs($admin)->get('/dashboard/users?page=1');
        $response->assertStatus(200);
        
        // View page 2
        $response2 = $this->actingAs($admin)->get('/dashboard/users?page=2');
        $response2->assertStatus(200);
    }
}
