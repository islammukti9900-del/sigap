<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SituasiSitrepTest extends TestCase
{
    use RefreshDatabase;

    public function test_ruang_situasi_hanya_staff_dan_json_terstruktur(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->pelapor()->create();
        Incident::factory()->darurat()->create(['status' => 'menunggu', 'judul' => 'Krisis Situasi']);

        $this->actingAs($petugas)->get('/situasi')->assertOk()->assertSee('RUANG')->assertSee('Krisis Situasi');
        $this->actingAs($pelapor)->get('/situasi')->assertForbidden();

        $this->actingAs($petugas)->getJson('/situasi/data')
            ->assertOk()
            ->assertJsonStructure(['total', 'menunggu', 'diproses', 'darurat_aktif', 'tereskalasi', 'kritis', 'terbaru', 'aset_bermasalah'])
            ->assertJsonPath('darurat_aktif', 1);
        $this->actingAs($pelapor)->getJson('/situasi/data')->assertForbidden();
    }

    public function test_sitrep_semua_periode_dan_fallback(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->pelapor()->create();
        Incident::factory()->darurat()->create(['judul' => 'Insiden Sitrep', 'status' => 'menunggu']);

        foreach (['hari-ini', '7-hari', '30-hari'] as $periode) {
            $this->actingAs($petugas)->get('/sitrep?periode=' . $periode)
                ->assertOk()->assertSee('SITREP')->assertSee('Insiden Sitrep');
        }

        // Periode tidak dikenal jatuh ke tampilan default tanpa error.
        $this->actingAs($petugas)->get('/sitrep?periode=acak')->assertOk();
        $this->actingAs($pelapor)->get('/sitrep')->assertForbidden();
    }
}
