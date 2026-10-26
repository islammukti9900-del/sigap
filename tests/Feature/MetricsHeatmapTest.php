<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Incident;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsHeatmapTest extends TestCase
{
    use RefreshDatabase;

    public function test_mttr_dari_linimasa_status(): void
    {
        $petugas = User::factory()->petugas()->create();
        $incident = Incident::factory()->create([
            'status' => 'selesai', 'prioritas' => 'darurat',
            'created_at' => now()->subHours(30), 'updated_at' => now()->subHours(4),
        ]);

        // Penanda linimasa: diproses pada +2 jam, selesai pada +26 jam.
        // forceCreate agar created_at historis tidak tertimpa $fillable.
        Comment::forceCreate([
            'incident_id' => $incident->id, 'user_id' => $petugas->id,
            'isi' => 'Status insiden diubah menjadi "Diproses".',
            'created_at' => now()->subHours(28), 'updated_at' => now()->subHours(28),
        ]);
        Comment::forceCreate([
            'incident_id' => $incident->id, 'user_id' => $petugas->id,
            'isi' => 'Status insiden diubah menjadi "Selesai".',
            'created_at' => now()->subHours(4), 'updated_at' => now()->subHours(4),
        ]);

        $metrics = app(MetricsService::class);
        $this->assertSame(120.0, $metrics->responseMinutes($incident->fresh()));
        $this->assertSame(1560.0, $metrics->resolutionMinutes($incident->fresh()));

        $ringkasan = $metrics->summary();
        $this->assertSame(2.0, $ringkasan['respons_jam']);
        $this->assertSame(26.0, $ringkasan['resolusi_jam']);
        $this->assertSame(2.0, $ringkasan['per_prioritas']['darurat']['respons_jam']);
    }

    public function test_heatmap_pada_statistik_publik_dan_dashboard(): void
    {
        $petugas = User::factory()->petugas()->create();
        Incident::factory()->count(3)->create(['created_at' => now(), 'updated_at' => now()]);
        Incident::factory()->create(['created_at' => now()->subDays(40), 'updated_at' => now()->subDays(40)]);

        $this->get('/portal/statistik')
            ->assertOk()
            ->assertSee('Heatmap Aktivitas Insiden')
            ->assertSee('insiden dalam 15 minggu terakhir');

        $this->actingAs($petugas)->get('/dashboard')
            ->assertOk()
            ->assertSee('Heatmap Aktivitas Insiden')
            ->assertSee('Metrik Penanganan (MTTR)')
            ->assertSee('Satuan Berisiko Tertinggi');
    }
}
