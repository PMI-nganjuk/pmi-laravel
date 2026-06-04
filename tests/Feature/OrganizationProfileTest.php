<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\OrganizationProfile;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default roles and users
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Test guest access restrictions.
     */
    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get('/dashboard/settings');
        $response->assertRedirect('/login');

        $response2 = $this->put('/dashboard/settings', [
            'organization_name' => 'PMI Nganjuk',
        ]);
        $response2->assertRedirect('/login');
    }

    /**
     * Test non-admin access restrictions.
     */
    public function test_non_admin_cannot_access_settings(): void
    {
        $manager = User::where('email', 'manager@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($manager)->get('/dashboard/settings');
        $response->assertStatus(403);

        $response2 = $this->actingAs($manager)->put('/dashboard/settings', [
            'organization_name' => 'PMI Nganjuk',
        ]);
        $response2->assertStatus(403);
    }

    /**
     * Test admin can view settings.
     */
    public function test_admin_can_view_settings(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->get('/dashboard/settings');
        $response->assertStatus(200);
        $response->assertSee('Profil Organisasi');
        $response->assertSee('Nama Entitas');
    }

    /**
     * Test admin can update organization profile.
     */
    public function test_admin_can_update_profile(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->put('/dashboard/settings', [
            'organization_name' => 'PMI Cabang Nganjuk',
            'address' => 'Jl. Dondong No 5, Nganjuk',
            'chairperson' => 'Drs. H. Isa Ansori',
            'headquarters_treasurer' => 'Budi Sudarsono',
            'blood_donation_unit_treasurer' => 'Siti Aminah',
            'financial_period_start' => '2026-01-01',
            'financial_period_end' => '2026-12-31',
            'fiscal_year' => 2026,
        ]);

        $response->assertRedirect('/dashboard/settings');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('organization_profiles', [
            'id' => 1,
            'organization_name' => 'PMI Cabang Nganjuk',
            'address' => 'Jl. Dondong No 5, Nganjuk',
            'chairperson' => 'Drs. H. Isa Ansori',
            'headquarters_treasurer' => 'Budi Sudarsono',
            'blood_donation_unit_treasurer' => 'Siti Aminah',
            'financial_period_start' => '2026-01-01 00:00:00',
            'financial_period_end' => '2026-12-31 00:00:00',
            'fiscal_year' => 2026,
        ]);
    }

    /**
     * Test validation rules for end date.
     */
    public function test_validation_prevents_invalid_period_dates(): void
    {
        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->put('/dashboard/settings', [
            'financial_period_start' => '2026-12-31',
            'financial_period_end' => '2026-01-01', // End date before start date
        ]);

        $response->assertSessionHasErrors(['financial_period_end']);
    }

    /**
     * Test header displays financial period when it is set.
     */
    public function test_header_displays_financial_period_when_configured(): void
    {
        $profile = OrganizationProfile::firstOrCreate(['id' => 1]);
        $profile->update([
            'financial_period_start' => '2026-01-01',
            'financial_period_end' => '2026-12-31',
        ]);

        $admin = User::where('email', 'admin@pmi-nganjuk.or.id')->first();

        $response = $this->actingAs($admin)->get('/dashboard/settings');
        $response->assertStatus(200);
        $response->assertSee('Periode: 01/01/2026 s.d 31/12/2026');
    }
}
