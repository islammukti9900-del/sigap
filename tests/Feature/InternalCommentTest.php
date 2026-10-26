<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_komentar_internal_tersembunyi_dari_pelapor_di_semua_saluran(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $petugas = User::factory()->petugas()->create();
        $incident = Incident::factory()->create(['user_id' => $pemilik->id]);

        // Petugas menulis catatan internal + komentar publik.
        $this->actingAs($petugas)->post('/insiden/' . $incident->id . '/komentar', [
            'isi' => 'Catatan rahasia analis: indikasi APT.', 'visibility' => 'internal',
        ])->assertRedirect();
        $this->actingAs($petugas)->post('/insiden/' . $incident->id . '/komentar', [
            'isi' => 'Laporan Anda sedang kami periksa.', 'visibility' => 'publik',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', ['isi' => 'Catatan rahasia analis: indikasi APT.', 'visibility' => 'internal']);

        // Panel petugas: terlihat + badge Internal.
        $this->actingAs($petugas)->get('/insiden/' . $incident->id)
            ->assertOk()->assertSee('Catatan rahasia analis')->assertSee('Internal');

        // Portal pemilik insiden: internal TIDAK terlihat, publik terlihat.
        $this->actingAs($pemilik)->get('/portal/insiden/' . $incident->id)
            ->assertOk()
            ->assertSee('Laporan Anda sedang kami periksa')
            ->assertDontSee('Catatan rahasia analis');

        // API publik: internal tidak bocor.
        $this->getJson('/api/incidents/' . $incident->id)
            ->assertOk()
            ->assertDontSee('Catatan rahasia analis')
            ->assertSee('Laporan Anda sedang kami periksa');
    }

    public function test_pelapor_tidak_bisa_memposting_komentar_internal(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($pemilik)->post('/portal/insiden/' . $incident->id . '/komentar', [
            'isi' => 'Saya coba menyisipkan internal', 'visibility' => 'internal',
        ]);

        // Dipaksa menjadi publik.
        $this->assertDatabaseHas('comments', ['isi' => 'Saya coba menyisipkan internal', 'visibility' => 'publik']);
    }
}
