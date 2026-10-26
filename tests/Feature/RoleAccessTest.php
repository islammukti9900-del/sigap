<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_login_dari_area_terbatas(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/portal/milikku')->assertRedirect('/login');
        $this->get('/insiden')->assertRedirect('/login');
    }

    public function test_pelapor_tidak_bisa_masuk_panel_petugas(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $this->actingAs($pelapor)->get('/dashboard')->assertForbidden();
        $this->actingAs($pelapor)->get('/insiden')->assertForbidden();
        $this->actingAs($pelapor)->get('/aset')->assertForbidden();
        $this->actingAs($pelapor)->get('/pengguna')->assertForbidden();
    }

    public function test_petugas_bisa_panel_tapi_tidak_kelola_pengguna(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->get('/dashboard')->assertOk();
        $this->actingAs($petugas)->get('/insiden')->assertOk();
        $this->actingAs($petugas)->get('/aset')->assertOk();
        $this->actingAs($petugas)->get('/pengguna')->assertForbidden();
    }

    public function test_admin_bisa_semua_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/pengguna')->assertOk();
        $this->actingAs($admin)->get('/kategori')->assertOk();
        $this->actingAs($admin)->get('/aset')->assertOk();
    }

    public function test_api_dashboard_ditolak_untuk_pelapor(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $token = $pelapor->createToken('t')->plainTextToken;

        $this->withToken($token)->getJson('/api/dashboard')->assertForbidden();
    }

    public function test_api_dashboard_terbuka_untuk_petugas(): void
    {
        $petugas = User::factory()->petugas()->create();
        $tokenStaff = $petugas->createToken('t')->plainTextToken;

        $this->withToken($tokenStaff)->getJson('/api/dashboard')->assertOk();
    }

    public function test_api_tanpa_token_ditolak_untuk_endpoint_privat(): void
    {
        $this->getJson('/api/notifications')->assertUnauthorized();
        $this->postJson('/api/incidents', [])->assertUnauthorized();
    }
}
