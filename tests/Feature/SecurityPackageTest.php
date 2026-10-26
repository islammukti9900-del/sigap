<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityPackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_keamanan_lengkap_dan_halaman_tetap_200(): void
    {
        $response = $this->get('/portal');
        $response->assertOk();

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertStringContainsString('cdn.tailwindcss.com', $csp);
        $this->assertStringContainsString('cdn.jsdelivr.net', $csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNotNull($response->headers->get('Permissions-Policy'));

        // Halaman kunci lain tetap dapat dirender dengan CSP aktif.
        $this->get('/login')->assertOk();
        $this->get('/portal/statistik')->assertOk();
        $petugas = User::factory()->petugas()->create();
        $this->actingAs($petugas)->get('/dashboard')->assertOk();
    }

    public function test_kebijakan_password_staff_ketat_pelapor_tetap_8(): void
    {
        $admin = User::factory()->admin()->create();

        // Password lemah untuk akun staff ditolak.
        $this->actingAs($admin)->from('/pengguna/buat')->post('/pengguna', [
            'name' => 'Staff Lemah', 'email' => 'lemah@sigap.test', 'role' => 'petugas',
            'password' => 'password', 'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'lemah@sigap.test']);

        // Memenuhi syarat: >= 10 karakter, huruf besar+kecil+angka.
        $this->actingAs($admin)->post('/pengguna', [
            'name' => 'Staff Kuat', 'email' => 'kuat@sigap.test', 'role' => 'petugas',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
        ])->assertRedirect('/pengguna');
        $this->assertDatabaseHas('users', ['email' => 'kuat@sigap.test']);

        // Pelapor tetap boleh 8 karakter (daftar sebagai tamu).
        auth()->logout();
        $this->post('/register', [
            'name' => 'Pelapor Baru', 'email' => 'baru@sigap.test',
            'password' => 'rahasia8', 'password_confirmation' => 'rahasia8',
        ])->assertRedirect('/portal');
        $this->assertDatabaseHas('users', ['email' => 'baru@sigap.test']);
    }

    public function test_unggahan_berkas_terselubung_ditolak(): void
    {
        Storage::fake('public');
        $pelapor = User::factory()->pelapor()->create();

        // Isi PHP menyamar sebagai PNG.
        $jahat = UploadedFile::fake()->createWithContent('bukti.png', '<?php system($_GET["c"]); ?>');

        $response = $this->actingAs($pelapor)->from('/portal/buat')->post('/portal/buat', [
            'judul' => 'Coba unggah jahat',
            'deskripsi' => 'Menguji penolakan berkas terselubung.',
            'satuan' => 'Kodim 0824/Jember',
            'lokasi_teks' => 'Ruang staf',
            'prioritas' => 'biasa',
            'category_id' => \App\Models\Category::factory()->create()->id,
            'foto' => $jahat,
        ]);

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseCount('incidents', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles('insiden'));
    }

    public function test_unggahan_valid_memakai_nama_acak_tanpa_ekstensi_php(): void
    {
        Storage::fake('public');
        $pelapor = User::factory()->pelapor()->create();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('foto kejadian.png', $png);

        $this->actingAs($pelapor)->post('/portal/buat', [
            'judul' => 'Unggahan valid',
            'deskripsi' => 'Bukti foto asli.',
            'satuan' => 'Kodim 0824/Jember',
            'lokasi_teks' => 'Ruang staf',
            'prioritas' => 'biasa',
            'category_id' => \App\Models\Category::factory()->create()->id,
            'foto' => $file,
        ])->assertRedirect();

        $incident = \App\Models\Incident::first();
        $this->assertNotNull($incident->foto);
        $this->assertStringNotContainsString('.php', $incident->foto);
        $this->assertStringNotContainsString('foto kejadian', $incident->foto);
        Storage::disk('public')->assertExists($incident->foto);
    }
}
