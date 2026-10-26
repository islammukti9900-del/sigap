<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dan_register_tampil(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/portal/masuk')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_staff_login_berhasil_ke_dashboard(): void
    {
        $petugas = User::factory()->petugas()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $petugas->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($petugas);
    }

    public function test_pelapor_login_lewat_form_portal_ke_beranda(): void
    {
        $pelapor = User::factory()->pelapor()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email' => $pelapor->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/portal');
        $this->assertAuthenticatedAs($pelapor);
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'bukan-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_register_portal_membuat_akun_pelapor(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pelapor Baru',
            'email' => 'baru@sigap.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/portal');
        $this->assertDatabaseHas('users', [
            'email' => 'baru@sigap.test',
            'role' => 'pelapor',
        ]);
        $this->assertAuthenticated();
    }

    public function test_logout(): void
    {
        $user = User::factory()->petugas()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_api_register_tanpa_token_lalu_login_dapat_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'API User',
            'email' => 'api@sigap.test',
            'password' => 'password123',
        ])->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonMissingPath('data.token');

        $this->assertDatabaseHas('users', ['email' => 'api@sigap.test', 'role' => 'pelapor']);

        $this->postJson('/api/login', [
            'email' => 'api@sigap.test',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_api_login_gagal_password_salah(): void
    {
        User::factory()->create([
            'email' => 'salah@sigap.test',
            'password' => bcrypt('password'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'salah@sigap.test',
            'password' => 'tebakan-salah',
        ])->assertUnauthorized()
            ->assertJsonPath('success', false);
    }
}
