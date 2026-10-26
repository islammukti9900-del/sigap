<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Incident;
use App\Models\Indicator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_aksi_kunci_tercatat_di_log(): void
    {
        $admin = User::factory()->admin()->create();
        $pelapor = User::factory()->pelapor()->create();

        // Insiden dibuat lewat portal.
        $this->actingAs($pelapor)->post('/portal/buat', [
            'judul' => 'Insiden untuk audit',
            'deskripsi' => 'Deskripsi audit.',
            'satuan' => 'Kodim 0824/Jember',
            'lokasi_teks' => 'Ruang staf',
            'prioritas' => 'penting',
            'category_id' => \App\Models\Category::factory()->create()->id,
        ])->assertRedirect();
        $incident = Incident::first();
        $this->assertDatabaseHas('activity_logs', ['action' => 'insiden.dibuat', 'subject_id' => $incident->id]);

        // Perubahan status + penugasan oleh admin.
        $petugas = User::factory()->petugas()->create();
        $this->actingAs($admin)->post('/insiden/' . $incident->id . '/status', ['status' => 'diproses']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'insiden.status', 'subject_id' => $incident->id]);

        $this->actingAs($admin)->post('/insiden/' . $incident->id . '/tugaskan', ['assigned_to' => $petugas->id]);
        $this->assertDatabaseHas('activity_logs', ['action' => 'insiden.ditugaskan', 'subject_id' => $incident->id]);

        // Komentar dibuat lalu dihapus staff.
        $this->actingAs($admin)->post('/insiden/' . $incident->id . '/komentar', ['isi' => 'Komentar audit']);
        $komentar = $incident->comments()->latest('id')->first();
        $this->actingAs($admin)->delete('/komentar/' . $komentar->id);
        $this->assertDatabaseHas('activity_logs', ['action' => 'komentar.dihapus']);

        // Aset dibuat & dihapus.
        $this->actingAs($admin)->post('/aset', [
            'nama' => 'Server Audit', 'tipe' => 'server', 'ip_address' => '10.10.9.9',
            'lokasi' => 'Ruang server', 'status' => 'aktif',
        ]);
        $aset = Asset::where('nama', 'Server Audit')->first();
        $this->assertDatabaseHas('activity_logs', ['action' => 'aset.dibuat', 'subject_id' => $aset->id]);
        $this->actingAs($admin)->delete('/aset/' . $aset->id);
        $this->assertDatabaseHas('activity_logs', ['action' => 'aset.dihapus', 'subject_id' => $aset->id]);

        // Indikator dibuat & dihapus.
        $this->actingAs($admin)->post('/indikator', ['type' => 'ip', 'value' => '7.7.7.7', 'threat' => 'c2']);
        $ind = Indicator::first();
        $this->assertDatabaseHas('activity_logs', ['action' => 'indikator.dibuat', 'subject_id' => $ind->id]);
        $this->actingAs($admin)->delete('/indikator/' . $ind->id);
        $this->assertDatabaseHas('activity_logs', ['action' => 'indikator.dihapus', 'subject_id' => $ind->id]);

        // Setiap entri menyimpan IP pelaku.
        $this->assertNotNull(ActivityLog::first()->ip_address);
    }

    public function test_halaman_aktivitas_khusus_admin_dan_filter(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'insiden.dibuat', 'description' => 'Membuat insiden uji', 'ip_address' => '127.0.0.1']);
        ActivityLog::create(['user_id' => $admin->id, 'action' => 'keamanan.login', 'description' => 'Login berhasil', 'ip_address' => '127.0.0.1']);

        $this->actingAs($admin)->get('/aktivitas')->assertOk()->assertSee('Membuat insiden uji');
        $this->actingAs($admin)->get('/aktivitas?filter=keamanan')
            ->assertOk()->assertSee('Login berhasil')->assertDontSee('Membuat insiden uji');
        $this->actingAs($petugas)->get('/aktivitas')->assertForbidden();
    }

    public function test_login_berhasil_dan_gagal_tercatat_sebagai_keamanan(): void
    {
        $petugas = User::factory()->petugas()->create(['email' => 'jaga@sigap.test']);

        $this->post('/login', ['email' => 'jaga@sigap.test', 'password' => 'salah-total']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'keamanan.login_gagal']);

        $this->post('/login', ['email' => 'jaga@sigap.test', 'password' => 'password']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'keamanan.login', 'user_id' => $petugas->id]);
    }
}
