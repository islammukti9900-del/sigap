<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'nama' => 'Server Aplikasi SIGAP-01',
            'tipe' => 'server',
            'ip_address' => '10.10.1.10',
            'lokasi' => 'Ruang Server Makodam',
            'status' => 'aktif',
            'deskripsi' => 'Server utama aplikasi internal.',
        ], $override);
    }

    public function test_petugas_crud_aset_via_web(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)
            ->post('/aset', $this->payload())
            ->assertRedirect('/aset');

        $aset = Asset::first();
        $this->assertNotNull($aset);
        $this->assertSame('Server Aplikasi SIGAP-01', $aset->nama);

        $this->actingAs($petugas)
            ->put('/aset/' . $aset->id, $this->payload(['status' => 'pemeliharaan', 'nama' => 'Server Aplikasi SIGAP-01 (Upgrade)']))
            ->assertRedirect('/aset');

        $this->assertDatabaseHas('assets', ['id' => $aset->id, 'status' => 'pemeliharaan']);

        $this->actingAs($petugas)
            ->delete('/aset/' . $aset->id)
            ->assertRedirect('/aset');

        $this->assertDatabaseMissing('assets', ['id' => $aset->id]);
    }

    public function test_aset_validasi_tipe_dan_status(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)
            ->from('/aset/create')
            ->post('/aset', $this->payload(['tipe' => 'roket', 'status' => 'terbang']))
            ->assertSessionHasErrors(['tipe', 'status']);

        $this->assertDatabaseCount('assets', 0);
    }

    public function test_daftar_aset_menampilkan_jumlah_insiden_terkait(): void
    {
        $petugas = User::factory()->petugas()->create();
        $aset = Asset::factory()->create(['nama' => 'Firewall Perimeter FW-EDGE-01']);
        Incident::factory()->count(2)->create(['asset_id' => $aset->id]);

        $this->actingAs($petugas)
            ->get('/aset')
            ->assertOk()
            ->assertSee('Firewall Perimeter FW-EDGE-01')
            ->assertSee('2 insiden terkait');
    }

    public function test_filter_aset_berdasarkan_status(): void
    {
        $petugas = User::factory()->petugas()->create();
        Asset::factory()->create(['nama' => 'Aset Rusak Satu', 'status' => 'rusak']);
        Asset::factory()->create(['nama' => 'Aset Aktif Satu', 'status' => 'aktif']);

        $this->actingAs($petugas)
            ->get('/aset?status=rusak')
            ->assertOk()
            ->assertSee('Aset Rusak Satu')
            ->assertDontSee('Aset Aktif Satu');
    }

    public function test_api_crud_aset_untuk_staff(): void
    {
        $petugas = User::factory()->petugas()->create();
        $token = $petugas->createToken('t')->plainTextToken;

        $created = $this->withToken($token)
            ->postJson('/api/assets', $this->payload())
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->json('data');

        $id = $created['id'] ?? $created['asset']['id'] ?? null;
        $this->assertNotNull($id, 'Response store aset harus memuat id aset.');

        $this->withToken($token)
            ->putJson('/api/assets/' . $id, $this->payload(['status' => 'rusak']))
            ->assertOk();

        $this->assertDatabaseHas('assets', ['id' => $id, 'status' => 'rusak']);

        $this->withToken($token)
            ->deleteJson('/api/assets/' . $id)
            ->assertOk();

        $this->assertDatabaseMissing('assets', ['id' => $id]);
    }

    public function test_api_aset_ditolak_untuk_pelapor(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $token = $pelapor->createToken('t')->plainTextToken;

        $this->withToken($token)->getJson('/api/assets')->assertForbidden();
        $this->withToken($token)->postJson('/api/assets', $this->payload())->assertForbidden();
    }
}
