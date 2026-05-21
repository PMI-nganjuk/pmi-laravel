<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\RoleEnum;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and users for each test run
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('PMI Nganjuk');
        $response->assertSee('Sistem Manajemen Keuangan');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@pmi-nganjuk.or.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals('admin@pmi-nganjuk.or.id', Auth::user()->email);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@pmi-nganjuk.or.id',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    public function test_authenticated_user_can_access_dashboard_and_sees_role_badge(): void
    {
        $user = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali, Admin PMI');
        $response->assertSee('Admin'); // Role badge
        $response->assertSee('Audit Akses RBAC'); // Admin specific action
    }

    public function test_financial_manager_sees_financial_stats_and_actions(): void
    {
        $user = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali, Manager Keuangan PMI');
        $response->assertSee('Manager keuangan'); // Role badge
        $response->assertSee('Total Saldo Kas'); // Financial stat
        $response->assertSee('Persetujuan Pencairan Kas'); // Financial manager action
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}
