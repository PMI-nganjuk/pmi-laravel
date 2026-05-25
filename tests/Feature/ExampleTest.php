<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/welcome');
    }

    /**
     * Guest visiting welcome page redirects to login.
     */
    public function test_guest_visiting_welcome_redirects_to_login(): void
    {
        $response = $this->get('/welcome');

        $response->assertRedirect('/login');
    }

    /**
     * Authenticated user visiting welcome page redirects to dashboard.
     */
    public function test_auth_user_visiting_welcome_redirects_to_dashboard(): void
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/welcome');

        $response->assertRedirect('/dashboard');
    }
}
