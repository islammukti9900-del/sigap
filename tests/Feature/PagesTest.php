<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_publik_portal_tampil(): void
    {
        Incident::factory()->count(2)->create();

        $this->get('/')->assertRedirect('/portal');
        $this->get('/portal')->assertOk()->assertSee('SIGAP');
        $this->get('/portal/jelajahi')->assertOk();
        $this->get('/portal/statistik')->assertOk();
        $this->get('/portal/bantuan')->assertOk();
    }

    public function test_detail_insiden_publik_tampil(): void
    {
        $incident = Incident::factory()->create(['judul' => 'Website satuan mengalami deface']);

        $this->get('/portal/insiden/' . $incident->id)
            ->assertOk()
            ->assertSee('Website satuan mengalami deface');
    }

    public function test_halaman_404_kustom(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('404');
    }

    public function test_halaman_pelapor_setelah_login(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        Incident::factory()->create(['user_id' => $pelapor->id]);

        $this->actingAs($pelapor)->get('/portal/buat')->assertOk();
        $this->actingAs($pelapor)->get('/portal/milikku')->assertOk();
        $this->actingAs($pelapor)->get('/portal/profil')->assertOk()->assertSee($pelapor->name);
        $this->actingAs($pelapor)->get('/portal/notifikasi')->assertOk();
    }

    public function test_halaman_staff_setelah_login(): void
    {
        $petugas = User::factory()->petugas()->create();
        $incident = Incident::factory()->create();

        $this->actingAs($petugas)->get('/dashboard')->assertOk();
        $this->actingAs($petugas)->get('/insiden')->assertOk();
        $this->actingAs($petugas)->get('/insiden/' . $incident->id)->assertOk();
        $this->actingAs($petugas)->get('/insiden/cetak')->assertOk();
        $this->actingAs($petugas)->get('/aset/create')->assertOk();
    }

    public function test_jelajahi_bisa_dicari_dan_difilter(): void
    {
        Incident::factory()->create(['judul' => 'Serangan ransomware di server', 'status' => 'diproses']);
        Incident::factory()->create(['judul' => 'Email biasa saja', 'status' => 'menunggu']);

        $this->get('/portal/jelajahi?search=ransomware')
            ->assertOk()
            ->assertSee('Serangan ransomware di server')
            ->assertDontSee('Email biasa saja');

        $this->get('/portal/jelajahi?status=menunggu')
            ->assertOk()
            ->assertSee('Email biasa saja')
            ->assertDontSee('Serangan ransomware di server');
    }

    public function test_profil_bisa_diperbarui(): void
    {
        $pelapor = User::factory()->pelapor()->create(['name' => 'Nama Lama']);

        $this->actingAs($pelapor)
            ->put('/portal/profil', [
                'name' => 'Nama Baru',
                'email' => $pelapor->email,
            ])
            ->assertRedirect();

        $this->assertSame('Nama Baru', $pelapor->fresh()->name);
    }
}
