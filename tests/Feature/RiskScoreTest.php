<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use App\Services\RiskScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiskScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_peringkat_risiko_dan_bonus_sla(): void
    {
        // Satuan A: 2 penting diproses = 10 poin, tanpa pelanggaran SLA
        // (tidak ada darurat terbuka, tidak ada menunggu basi).
        Incident::factory()->create(['satuan' => 'Satuan A', 'prioritas' => 'penting', 'status' => 'diproses']);
        Incident::factory()->create(['satuan' => 'Satuan A', 'prioritas' => 'penting', 'status' => 'diproses']);

        // Satuan B: 2 penting menunggu = 10 poin + satu menunggu lama (>24 jam) = +5 bonus SLA.
        Incident::factory()->create(['satuan' => 'Satuan B', 'prioritas' => 'penting', 'status' => 'menunggu']);
        Incident::factory()->create([
            'satuan' => 'Satuan B', 'prioritas' => 'penting', 'status' => 'menunggu',
            'created_at' => now()->subHours(30), 'updated_at' => now()->subHours(30),
        ]);

        // Satuan C: hanya insiden selesai -> tidak masuk peringkat.
        Incident::factory()->selesai()->create(['satuan' => 'Satuan C']);

        $ranking = app(RiskScoreService::class)->ranking();

        $this->assertSame('Satuan B', $ranking[0]['satuan']);
        $this->assertSame(15, $ranking[0]['skor']);
        $this->assertTrue($ranking[0]['pelanggaran_sla']);
        $this->assertSame('Satuan A', $ranking[1]['satuan']);
        $this->assertSame(10, $ranking[1]['skor']);
        $this->assertFalse($ranking[1]['pelanggaran_sla']);
        $this->assertCount(2, $ranking);
    }

    public function test_halaman_peta_risiko_untuk_staff(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->pelapor()->create();
        Incident::factory()->darurat()->create(['satuan' => 'Kodam Uji', 'status' => 'menunggu']);

        $this->actingAs($petugas)->get('/risiko')->assertOk()->assertSee('Kodam Uji')->assertSee('Skor Risiko');
        $this->actingAs($pelapor)->get('/risiko')->assertForbidden();
    }
}
