<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EscalationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cabang_menunggu_lebih_dari_4_jam(): void
    {
        $lama = Incident::factory()->darurat()->create([
            'status' => 'menunggu',
            'created_at' => now()->subHours(5), 'updated_at' => now()->subHours(5),
        ]);
        $baru = Incident::factory()->darurat()->create([
            'status' => 'menunggu',
            'created_at' => now()->subHours(2), 'updated_at' => now()->subHours(2),
        ]);

        $this->assertTrue($lama->isEscalated());
        $this->assertFalse($baru->isEscalated());
    }

    public function test_cabang_diproses_menganggur_lebih_dari_24_jam(): void
    {
        $macet = Incident::factory()->darurat()->create([
            'status' => 'diproses',
            'created_at' => now()->subDays(3), 'updated_at' => now()->subHours(25),
        ]);
        $aktif = Incident::factory()->darurat()->create([
            'status' => 'diproses',
            'created_at' => now()->subDays(3), 'updated_at' => now()->subHours(3),
        ]);
        $nonDarurat = Incident::factory()->create([
            'prioritas' => 'penting', 'status' => 'menunggu',
            'created_at' => now()->subDays(5), 'updated_at' => now()->subDays(5),
        ]);

        $this->assertTrue($macet->isEscalated());
        $this->assertFalse($aktif->isEscalated());
        $this->assertFalse($nonDarurat->isEscalated(), 'Hanya prioritas darurat yang tereskalasi');
    }

    public function test_filter_eskalasi_di_daftar_staff_dan_badge(): void
    {
        $petugas = User::factory()->petugas()->create();
        $lama = Incident::factory()->darurat()->create([
            'judul' => 'Insiden Kritis Macet', 'status' => 'menunggu',
            'created_at' => now()->subHours(6), 'updated_at' => now()->subHours(6),
        ]);
        Incident::factory()->darurat()->create([
            'judul' => 'Insiden Kritis Baru', 'status' => 'menunggu',
        ]);

        $this->actingAs($petugas)->get('/insiden?eskalasi=1')
            ->assertOk()
            ->assertSee('Insiden Kritis Macet')
            ->assertDontSee('Insiden Kritis Baru')
            ->assertSee('Eskalasi');

        // Badge juga tampil di detail.
        $this->actingAs($petugas)->get('/insiden/' . $lama->id)->assertOk()->assertSee('Eskalasi');
    }
}
