<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitLossSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan pengunjung tanpa login tidak dapat mengakses halaman laba rugi.
     */
    public function test_guest_cannot_access_profit_loss_page(): void
    {
        $response = $this->get(route('profit-loss.index'));
        $response->assertRedirect('/login');
    }

    /**
     * Memastikan karyawan biasa tanpa izin keuangan tidak dapat mengakses halaman laba rugi.
     */
    public function test_unauthorized_user_cannot_access_profit_loss_page(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::STAFF,
        ]);

        $response = $this->actingAs($user)->get(route('profit-loss.index'));
        $response->assertStatus(403);
    }

    /**
     * Memastikan input tanggal dengan format tidak valid ditolak oleh sistem.
     */
    public function test_invalid_date_inputs_are_rejected(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN,
        ]);

        $response = $this->actingAs($user)->get(route('profit-loss.index', [
            'start_date' => 'tanggal-salah',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_date']);
    }

    /**
     * Memastikan input payload SQL Injection pada filter tanggal ditolak oleh validasi.
     */
    public function test_sql_injection_payloads_are_rejected(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN,
        ]);

        $response = $this->actingAs($user)->get(route('profit-loss.index', [
            'start_date' => "2026-01-01' OR '1'='1",
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_date']);
    }

    /**
     * Memastikan input payload XSS pada filter tanggal ditolak oleh validasi.
     */
    public function test_xss_payloads_are_rejected(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN,
        ]);

        $response = $this->actingAs($user)->get(route('profit-loss.index', [
            'start_date' => '"><script>alert(1)</script>',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start_date']);
    }

    /**
     * Memastikan filter tanggal dengan format yang benar dapat diterima dan diproses.
     */
    public function test_valid_date_inputs_are_accepted(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN,
        ]);

        $response = $this->actingAs($user)->get(route('profit-loss.index', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(200);
    }
}
