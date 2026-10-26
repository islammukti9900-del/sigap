<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private function aktifkan2fa(User $user): array
    {
        $this->actingAs($user)->post('/keamanan-2fa/mulai')->assertRedirect();
        $secret = session('2fa_setup_secret');
        $this->assertNotEmpty($secret);

        $kode = (new Google2FA())->getCurrentOtp($secret);
        $response = $this->actingAs($user)->post('/keamanan-2fa/konfirmasi', ['kode' => $kode]);
        $response->assertRedirect('/keamanan-2fa');

        $codes = session('recovery_codes_baru');
        $this->assertIsArray($codes);
        $this->assertCount(8, $codes);

        return [$secret, $codes];
    }

    public function test_alur_aktivasi_dan_status(): void
    {
        $petugas = User::factory()->petugas()->create();
        [$secret] = $this->aktifkan2fa($petugas);

        $petugas->refresh();
        $this->assertTrue($petugas->hasTwoFactorEnabled());
        // Secret tersimpan terenkripsi: nilai mentah di kolom != secret asli.
        $mentah = \DB::table('users')->where('id', $petugas->id)->value('two_factor_secret');
        $this->assertNotSame($secret, $mentah);
    }

    public function test_tantangan_wajib_setelah_login_dan_kode_benar_lolos(): void
    {
        $petugas = User::factory()->petugas()->create(['email' => 'totp@sigap.test']);
        [$secret] = $this->aktifkan2fa($petugas);

        // Logout penuh: sesi dibersihkan seperti alur browser (POST /logout).
        auth()->logout();
        session()->flush();
        $this->post('/login', ['email' => 'totp@sigap.test', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        // Halaman staff terblokir sebelum tantangan lolos.
        $this->get('/dashboard')->assertRedirect('/2fa/tantangan');
        $this->get('/2fa/tantangan')->assertOk();

        // Kode salah ditolak.
        $this->post('/2fa/tantangan', ['kode' => '000000'])->assertSessionHasErrors('kode');
        $this->get('/dashboard')->assertRedirect('/2fa/tantangan');

        // Kode benar meloloskan.
        $kode = (new Google2FA())->getCurrentOtp($secret);
        $this->post('/2fa/tantangan', ['kode' => $kode])->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk();
    }

    public function test_kode_pemulihan_sekali_pakai(): void
    {
        $petugas = User::factory()->petugas()->create(['email' => 'pulih@sigap.test']);
        [, $codes] = $this->aktifkan2fa($petugas);

        auth()->logout();
        session()->flush();
        $this->post('/login', ['email' => 'pulih@sigap.test', 'password' => 'password']);
        $this->post('/2fa/tantangan', ['kode' => $codes[0]])->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk();

        // Login lagi: kode pemulihan yang sama sudah hangus.
        auth()->logout();
        session()->flush();
        $this->post('/login', ['email' => 'pulih@sigap.test', 'password' => 'password']);
        $this->post('/2fa/tantangan', ['kode' => $codes[0]])->assertSessionHasErrors('kode');
    }

    public function test_nonaktifkan_membutuhkan_password(): void
    {
        $petugas = User::factory()->petugas()->create();
        $this->aktifkan2fa($petugas);

        // Sesi ini dianggap sudah lolos tantangan 2FA.
        $this->withSession(['2fa_passed' => true]);

        $this->actingAs($petugas)->post('/keamanan-2fa/nonaktif', ['password' => 'bukan-password'])
            ->assertSessionHasErrors('password');
        $this->assertTrue($petugas->fresh()->hasTwoFactorEnabled());

        $this->actingAs($petugas)->post('/keamanan-2fa/nonaktif', ['password' => 'password'])
            ->assertRedirect('/keamanan-2fa');
        $this->assertFalse($petugas->fresh()->hasTwoFactorEnabled());
    }

    public function test_pelapor_tidak_wajib_2fa(): void
    {
        $pelapor = User::factory()->pelapor()->create(['email' => 'biasa@sigap.test']);
        $this->post('/login', ['email' => 'biasa@sigap.test', 'password' => 'password'])
            ->assertRedirect('/portal');
        $this->get('/portal')->assertOk();
    }
}
