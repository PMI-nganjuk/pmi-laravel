<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Program;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $picUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $this->adminUser = User::where('email', 'admin@pmi-nganjuk.or.id')->first();
        $this->picUser = User::where('email', 'karyawan@pmi-nganjuk.or.id')->first();
    }

    /**
     * Test that guests cannot access program management.
     */
    public function test_guest_cannot_access_program_management(): void
    {
        $response = $this->get(route('programs.index'));
        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can access program management page.
     */
    public function test_authenticated_user_can_access_program_management(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('programs.index'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Program Kerja');
    }

    /**
     * Test validation rules for storing program.
     */
    public function test_store_program_validation_errors(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('programs.store'), [
            'name' => '',
            'user_id' => 9999, // Non-existent user id
        ]);

        $response->assertSessionHasErrors(['name', 'user_id']);
    }

    /**
     * Test user can store program.
     */
    public function test_user_can_store_program(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('programs.store'), [
            'name' => 'Program Bencana Alam',
            'user_id' => $this->picUser->id,
            'description' => 'Program untuk mitigasi bencana alam di Kabupaten Nganjuk.',
        ]);

        $response->assertRedirect(route('programs.index'));
        $response->assertSessionHas('success', 'Program kerja berhasil ditambahkan!');

        $this->assertDatabaseHas('programs', [
            'name' => 'Program Bencana Alam',
            'user_id' => $this->picUser->id,
            'description' => 'Program untuk mitigasi bencana alam di Kabupaten Nganjuk.',
        ]);
    }

    /**
     * Test user can update program.
     */
    public function test_user_can_update_program(): void
    {
        $program = Program::create([
            'name' => 'Program Kerja Awal',
            'user_id' => $this->picUser->id,
            'description' => 'Keterangan awal.',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('programs.update', $program), [
            'name' => 'Program Kerja Update',
            'user_id' => $this->adminUser->id,
            'description' => 'Keterangan diupdate.',
        ]);

        $response->assertRedirect(route('programs.index'));
        $response->assertSessionHas('success', 'Program kerja berhasil diperbarui!');

        $this->assertDatabaseHas('programs', [
            'id' => $program->id,
            'name' => 'Program Kerja Update',
            'user_id' => $this->adminUser->id,
            'description' => 'Keterangan diupdate.',
        ]);
    }

    /**
     * Test user can delete program.
     */
    public function test_user_can_delete_program(): void
    {
        $program = Program::create([
            'name' => 'Program Hapus',
            'user_id' => $this->picUser->id,
            'description' => 'Keterangan.',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('programs.destroy', $program));

        $response->assertRedirect(route('programs.index'));
        $response->assertSessionHas('success', 'Program kerja berhasil dihapus!');

        $this->assertDatabaseMissing('programs', [
            'id' => $program->id,
        ]);
    }
}
