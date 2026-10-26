<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LockoutCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_lockout_setelah_5_kegagalan_selama_15_menit(): void
    {
        $petugas = User::factory()->petugas()->create(['email' => 'kunci@sigap.test']);

        // Kegagalan diberi jarak > 1 menit agar limiter per-menit tidak menutupi
        // mekanisme lockout progresif yang sedang diuji.
        for ($i = 1; $i <= 5; $i++) {
            $this->post('/login', ['email' => 'kunci@sigap.test', 'password' => 'salah-' . $i]);
            $this->travel(70)->seconds();
        }

        // Percobaan ke-5 memicu penguncian & tercatat di log keamanan.
        $this->assertDatabaseHas('activity_logs', ['action' => 'keamanan.lockout']);
        $this->assertSame(5, \App\Models\ActivityLog::where('action', 'keamanan.login_gagal')->count());

        // Password yang benar pun ditolak selama masa kunci, dengan pesan jelas.
        $response = $this->post('/login', ['email' => 'kunci@sigap.test', 'password' => 'password']);
        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('dikunci', session('errors')->first('email'));
        $this->assertGuest();

        // Setelah masa kunci (15 menit sejak kegagalan pertama) berakhir, login bisa lagi.
        $this->travel(16)->minutes();
        $this->post('/login', ['email' => 'kunci@sigap.test', 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($petugas);
    }

    public function test_captcha_salah_ditolak_dan_benar_lolos(): void
    {
        config(['captcha.enabled' => true]);
        User::factory()->petugas()->create(['email' => 'captcha@sigap.test']);

        // Halaman login menampilkan soal & menyimpan jawaban di sesi.
        $this->get('/login')->assertOk()->assertSee('Verifikasi: berapa hasil');
        $jawaban = session('captcha_answer');
        $this->assertNotNull($jawaban);

        // Jawaban salah -> ditolak.
        $this->post('/login', [
            'email' => 'captcha@sigap.test', 'password' => 'password', 'captcha' => $jawaban + 100,
        ])->assertSessionHasErrors('captcha');
        $this->assertGuest();

        // Soal diganti setiap render; ambil jawaban terbaru lalu kirim benar.
        $this->get('/login');
        $jawabanBaru = session('captcha_answer');
        $this->post('/login', [
            'email' => 'captcha@sigap.test', 'password' => 'password', 'captcha' => $jawabanBaru,
        ])->assertRedirect('/dashboard');
    }
}
