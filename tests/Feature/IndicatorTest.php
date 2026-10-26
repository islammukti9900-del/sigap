<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Incident;
use App\Models\Indicator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IndicatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dapat_melihat_daftar_dan_pelapor_ditolak(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->pelapor()->create();
        Indicator::factory()->create(['value' => 'jahat.example.com']);

        $this->actingAs($petugas)->get('/indikator')->assertOk()->assertSee('jahat.example.com');
        $this->actingAs($pelapor)->get('/indikator')->assertForbidden();
    }

    private function payloadInsiden(array $override = []): array
    {
        return array_merge([
            'judul' => 'Insiden uji indikator',
            'deskripsi' => 'Deskripsi insiden uji.',
            'satuan' => 'Kodim 0824/Jember',
            'lokasi_teks' => 'Ruang staf',
            'prioritas' => 'penting',
            'category_id' => Category::factory()->create()->id,
        ], $override);
    }

    public function test_petugas_boleh_menambah_tapi_tidak_menghapus(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->post('/indikator', [
            'type' => 'domain',
            'value' => 'https://Phishing-Baru.xyz/',
            'threat' => 'phishing',
            'source' => 'Laporan satuan',
        ])->assertRedirect();

        // Nilai dinormalisasi: huruf kecil, tanpa protokol & garis miring akhir.
        $ind = Indicator::first();
        $this->assertSame('phishing-baru.xyz', $ind->value);
        $this->assertSame($petugas->id, $ind->added_by);

        $this->actingAs($petugas)->delete('/indikator/' . $ind->id)->assertForbidden();
        $this->assertDatabaseCount('indicators', 1);
    }

    public function test_admin_kelola_penuh_dan_validasi_unik_per_tipe(): void
    {
        $admin = User::factory()->admin()->create();
        $ind = Indicator::factory()->create(['type' => 'ip', 'value' => '1.2.3.4']);

        // Duplikat tipe+nilai ditolak.
        $this->actingAs($admin)->from('/indikator/buat')->post('/indikator', [
            'type' => 'ip', 'value' => '1.2.3.4', 'threat' => 'c2',
        ])->assertSessionHasErrors('value');

        // Nilai sama pada tipe berbeda diperbolehkan.
        $this->actingAs($admin)->post('/indikator', [
            'type' => 'domain', 'value' => '1.2.3.4', 'threat' => 'c2',
        ])->assertRedirect();

        $this->actingAs($admin)->put('/indikator/' . $ind->id, [
            'type' => 'ip', 'value' => '1.2.3.4', 'threat' => 'botnet', 'source' => 'Feodo',
        ])->assertRedirect();
        $this->assertSame('botnet', $ind->fresh()->threat);

        $this->actingAs($admin)->delete('/indikator/' . $ind->id)->assertRedirect();
        $this->assertDatabaseMissing('indicators', ['id' => $ind->id]);
    }

    public function test_api_indikator_dengan_peran(): void
    {
        $petugas = User::factory()->petugas()->create();
        Indicator::factory()->create(['value' => 'api-jahat.example.com']);
        $tokenPetugas = $petugas->createToken('t')->plainTextToken;

        $this->withToken($tokenPetugas)->getJson('/api/indicators')->assertOk()->assertJsonFragment(['value' => 'api-jahat.example.com']);

        $this->withToken($tokenPetugas)->postJson('/api/indicators', [
            'type' => 'ip', 'value' => '9.9.9.9', 'threat' => 'malware',
        ])->assertCreated();
        $this->assertDatabaseHas('indicators', ['value' => '9.9.9.9']);
    }

    public function test_api_indikator_ditolak_untuk_pelapor(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        Indicator::factory()->create(['value' => 'api-jahat.example.com']);
        $tokenPelapor = $pelapor->createToken('t')->plainTextToken;

        $this->withToken($tokenPelapor)->getJson('/api/indicators')->assertForbidden();
        $this->withToken($tokenPelapor)->postJson('/api/indicators', [
            'type' => 'ip', 'value' => '9.9.9.9', 'threat' => 'malware',
        ])->assertForbidden();
    }

    public function test_cek_indikator_ditemukan_dan_tidak_ditemukan(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        Indicator::factory()->create(['type' => 'domain', 'value' => 'brawijaya-login-palsu.xyz', 'threat' => 'phishing']);

        // Input berupa URL penuh tetap cocok dengan indikator domain.
        $this->actingAs($pelapor)->post('/portal/cek-indikator', [
            'indikator' => 'https://brawijaya-login-palsu.xyz/verifikasi',
        ])->assertOk()->assertSee('Indikator Berbahaya Ditemukan');

        $this->actingAs($pelapor)->post('/portal/cek-indikator', [
            'indikator' => 'domain-aman.example.com',
        ])->assertOk()->assertSee('Tidak Ditemukan di Basis Data');

        // Tamu tidak boleh memakai pemeriksa.
        auth()->logout();
        $this->get('/portal/cek-indikator')->assertRedirect('/login');
    }

    public function test_cek_indikator_via_api(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        Indicator::factory()->create(['type' => 'ip', 'value' => '185.220.101.47', 'threat' => 'c2']);
        $token = $pelapor->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/cek-indikator', ['indikator' => '185.220.101.47'])
            ->assertOk()
            ->assertJsonPath('data.ditemukan', true)
            ->assertJsonPath('data.indikator.threat', 'c2');

        $this->withToken($token)->postJson('/api/cek-indikator', ['indikator' => '8.8.8.8'])
            ->assertOk()
            ->assertJsonPath('data.ditemukan', false);
    }

    public function test_auto_link_indikator_dari_deskripsi_saat_insiden_dibuat(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $ind = Indicator::factory()->create(['type' => 'domain', 'value' => 'brawijaya-login-palsu.xyz']);

        $this->actingAs($pelapor)->post('/portal/buat', $this->payloadInsiden([
            'judul' => 'Email phishing satuan',
            'deskripsi' => 'Penerima diarahkan membuka brawijaya-login-palsu.xyz untuk login palsu.',
        ]))->assertRedirect();

        $incident = Incident::first();
        $this->assertTrue($incident->indicators()->where('indicators.id', $ind->id)->exists());

        // Detail petugas menampilkan chip indikator terkait.
        $petugas = User::factory()->petugas()->create();
        $this->actingAs($petugas)->get('/insiden/' . $incident->id)
            ->assertOk()->assertSee('Indikator Terkait')->assertSee('brawijaya-login-palsu.xyz');

        // Detail indikator menampilkan insiden terkait.
        $this->actingAs($petugas)->get('/indikator/' . $ind->id)
            ->assertOk()->assertSee('Email phishing satuan');
    }

    public function test_hash_bukti_tersimpan_dan_banner_kecocokan_ioc(): void
    {
        Storage::fake('public');
        $pelapor = User::factory()->pelapor()->create();

        $konten = 'konten-berkas-bukti-digital';
        $hash = hash('sha256', $konten);
        $ind = Indicator::factory()->create(['type' => 'hash', 'value' => $hash, 'threat' => 'malware']);

        // Berkas gambar valid (header PNG asli) dengan isi yang hash-nya dikenal.
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('bukti.png', $png);
        $hashFile = hash('sha256', $png);
        $ind2 = Indicator::factory()->create(['type' => 'hash', 'value' => $hashFile, 'threat' => 'malware']);

        $this->actingAs($pelapor)->post('/portal/buat', $this->payloadInsiden([
            'judul' => 'Berkas mencurigakan diterima',
            'deskripsi' => 'Lampiran diterima lewat email.',
            'prioritas' => 'darurat',
            'foto' => $file,
        ]))->assertRedirect();

        $incident = Incident::first();
        $this->assertSame($hashFile, $incident->evidence_hash);
        $this->assertTrue($incident->indicators()->where('indicators.id', $ind2->id)->exists());

        // Banner peringatan tampil di portal & panel petugas.
        $this->actingAs($pelapor)->get('/portal/insiden/' . $incident->id)
            ->assertOk()->assertSee('Bukti digital cocok dengan indikator berbahaya');

        $petugas = User::factory()->petugas()->create();
        $this->actingAs($petugas)->get('/insiden/' . $incident->id)
            ->assertOk()->assertSee('Bukti digital cocok dengan indikator berbahaya')
            ->assertSee(substr($hashFile, 0, 16));
    }
}
