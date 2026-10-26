<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IncidentWebTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'judul' => 'Email phishing mengatasnamakan komandan satuan',
            'deskripsi' => 'Ditemukan email mencurigakan meminta data personel.',
            'satuan' => 'Kodim 0824/Jember',
            'lokasi_teks' => 'Ruang Staf Intel',
            'prioritas' => 'penting',
        ], $override);
    }

    public function test_pelapor_membuat_insiden_dengan_kategori_dan_aset(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $kategori = Category::factory()->create(['nama' => 'Phishing']);
        $aset = Asset::factory()->create();

        $response = $this->actingAs($pelapor)->post('/portal/buat', $this->payload([
            'category_id' => $kategori->id,
            'asset_id' => $aset->id,
        ]));

        $response->assertRedirect('/portal/insiden/' . Incident::first()->id);
        $this->assertDatabaseHas('incidents', [
            'judul' => 'Email phishing mengatasnamakan komandan satuan',
            'user_id' => $pelapor->id,
            'category_id' => $kategori->id,
            'asset_id' => $aset->id,
            'status' => 'menunggu',
            'prioritas' => 'penting',
            'satuan' => 'Kodim 0824/Jember',
        ]);
    }

    public function test_buat_insiden_validasi_wajib_isi(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)->from('/portal/buat')->post('/portal/buat', []);

        $response->assertRedirect('/portal/buat');
        $response->assertSessionHasErrors(['judul', 'deskripsi', 'satuan', 'lokasi_teks']);
        $this->assertDatabaseCount('incidents', 0);
    }

    public function test_buat_insiden_dengan_foto_bukti(): void
    {
        Storage::fake('public');
        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)->post('/portal/buat', $this->payload([
            'category_id' => Category::factory()->create()->id,
            'foto' => UploadedFile::fake()->image('bukti.jpg', 800, 600),
        ]));

        $incident = Incident::first();
        $response->assertRedirect('/portal/insiden/' . $incident->id);
        $this->assertNotNull($incident->foto);
        Storage::disk('public')->assertExists($incident->foto);
    }

    public function test_foto_dengan_format_salah_ditolak(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)->from('/portal/buat')->post('/portal/buat', $this->payload([
            'category_id' => Category::factory()->create()->id,
            'foto' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ]));

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseCount('incidents', 0);
    }

    public function test_pemilik_menghapus_insiden_sendiri_yang_menunggu(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pelapor->id, 'status' => 'menunggu']);

        $this->actingAs($pelapor)
            ->delete('/portal/insiden/' . $incident->id)
            ->assertRedirect('/portal/milikku');

        $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    }

    public function test_pemilik_tidak_bisa_hapus_insiden_yang_sudah_diproses(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pelapor->id, 'status' => 'diproses']);

        $this->actingAs($pelapor)
            ->delete('/portal/insiden/' . $incident->id)
            ->assertStatus(422);

        $this->assertDatabaseHas('incidents', ['id' => $incident->id]);
    }

    public function test_orang_lain_tidak_bisa_hapus_insiden(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $orangLain = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($orangLain)
            ->delete('/portal/insiden/' . $incident->id)
            ->assertForbidden();
    }

    public function test_staff_ubah_status_membuat_timeline_dan_notifikasi_pemilik(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pemilik = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pemilik->id, 'status' => 'menunggu']);

        $this->actingAs($petugas)
            ->post('/insiden/' . $incident->id . '/status', ['status' => 'diproses'])
            ->assertRedirect();

        $this->assertDatabaseHas('incidents', ['id' => $incident->id, 'status' => 'diproses']);
        // Komentar sistem menjadi penanda linimasa perubahan status.
        $this->assertTrue(
            \App\Models\Comment::where('incident_id', $incident->id)
                ->where('user_id', $petugas->id)
                ->where('isi', 'like', 'Status insiden diubah%')
                ->exists()
        );
        // Pemilik menerima notifikasi berisi tautan ke detail insiden.
        $this->assertTrue(
            \App\Models\Notification::where('user_id', $pemilik->id)
                ->where('link', 'like', '%/portal/insiden/' . $incident->id)
                ->exists()
        );
    }

    public function test_staff_menugaskan_petugas_dan_petugas_diberi_notifikasi(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        $incident = Incident::factory()->create();

        $this->actingAs($admin)
            ->post('/insiden/' . $incident->id . '/tugaskan', ['assigned_to' => $petugas->id])
            ->assertRedirect();

        $this->assertDatabaseHas('incidents', ['id' => $incident->id, 'assigned_to' => $petugas->id]);
        $this->assertTrue(
            \App\Models\Notification::where('user_id', $petugas->id)
                ->where('link', 'like', '%/insiden/' . $incident->id)
                ->exists()
        );
    }

    public function test_menugaskan_ke_bukan_petugas_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create();

        $this->actingAs($admin)
            ->from('/insiden/' . $incident->id)
            ->post('/insiden/' . $incident->id . '/tugaskan', ['assigned_to' => $pelapor->id])
            ->assertRedirect('/insiden/' . $incident->id)
            ->assertSessionHas('error');

        $this->assertDatabaseHas('incidents', ['id' => $incident->id, 'assigned_to' => null]);
    }

    public function test_bulk_ubah_status(): void
    {
        $petugas = User::factory()->petugas()->create();
        $a = Incident::factory()->create(['status' => 'menunggu']);
        $b = Incident::factory()->create(['status' => 'menunggu']);

        $this->actingAs($petugas)
            ->post('/insiden/bulk-status', [
                'ids' => [$a->id, $b->id],
                'status' => 'diproses',
            ])
            ->assertRedirect();

        $this->assertSame('diproses', $a->fresh()->status);
        $this->assertSame('diproses', $b->fresh()->status);
    }

    public function test_export_csv_menghormati_filter_dan_ada_bom(): void
    {
        $petugas = User::factory()->petugas()->create();
        Incident::factory()->create(['judul' => 'Insiden Darurat Alpha', 'prioritas' => 'darurat']);
        Incident::factory()->create(['judul' => 'Insiden Biasa Beta', 'prioritas' => 'biasa']);

        $response = $this->actingAs($petugas)->get('/insiden/export?prioritas=darurat');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Insiden Darurat Alpha', $content);
        $this->assertStringNotContainsString('Insiden Biasa Beta', $content);
    }
}
