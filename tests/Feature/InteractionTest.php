<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelapor_berkomentar_di_detail_insiden(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create();

        $this->actingAs($pelapor)
            ->post('/portal/insiden/' . $incident->id . '/komentar', ['isi' => 'Sudah saya amankan perangkatnya.'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'incident_id' => $incident->id,
            'user_id' => $pelapor->id,
            'isi' => 'Sudah saya amankan perangkatnya.',
        ]);
    }

    public function test_komentar_petugas_memberi_notifikasi_ke_pemilik(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pemilik = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($petugas)
            ->post('/insiden/' . $incident->id . '/komentar', ['isi' => 'Sedang kami telusuri sumbernya.'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', ['incident_id' => $incident->id, 'user_id' => $petugas->id]);
        $this->assertTrue(Notification::where('user_id', $pemilik->id)->exists());
    }

    public function test_petugas_menghapus_komentar(): void
    {
        $petugas = User::factory()->petugas()->create();
        $incident = Incident::factory()->create();
        $comment = Comment::create([
            'incident_id' => $incident->id,
            'user_id' => User::factory()->pelapor()->create()->id,
            'isi' => 'Komentar yang perlu dimoderasi.',
        ]);

        $this->actingAs($petugas)
            ->delete('/komentar/' . $comment->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_ikuti_dan_berhenti_mengikuti_insiden(): void
    {
        $pengikut = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create();

        $this->actingAs($pengikut)
            ->post('/portal/insiden/' . $incident->id . '/ikuti')
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('follows', ['incident_id' => $incident->id, 'user_id' => $pengikut->id]);

        $this->actingAs($pengikut)
            ->post('/portal/insiden/' . $incident->id . '/ikuti')
            ->assertRedirect();

        $this->assertDatabaseMissing('follows', ['incident_id' => $incident->id, 'user_id' => $pengikut->id]);
    }

    public function test_pengikut_menerima_notifikasi_saat_status_berubah(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pengikut = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['status' => 'menunggu']);
        $incident->follows()->create(['user_id' => $pengikut->id]);

        $this->actingAs($petugas)
            ->post('/insiden/' . $incident->id . '/status', ['status' => 'selesai'])
            ->assertRedirect();

        $this->assertTrue(
            Notification::where('user_id', $pengikut->id)
                ->where('link', 'like', '%/portal/insiden/' . $incident->id)
                ->exists()
        );
    }

    public function test_pemilik_memberi_rating_setelah_selesai(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $incident = Incident::factory()->selesai()->create(['user_id' => $pemilik->id]);

        $this->actingAs($pemilik)
            ->post('/portal/insiden/' . $incident->id . '/nilai', ['rating' => 5])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('incidents', ['id' => $incident->id, 'rating' => 5]);
        $this->assertNotNull($incident->fresh()->rated_at);
    }

    public function test_rating_ditolak_jika_belum_selesai(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['user_id' => $pemilik->id, 'status' => 'diproses']);

        $this->actingAs($pemilik)
            ->post('/portal/insiden/' . $incident->id . '/nilai', ['rating' => 4])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNull($incident->fresh()->rating);
    }

    public function test_rating_ditolak_untuk_bukan_pemilik(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $orangLain = User::factory()->pelapor()->create();
        $incident = Incident::factory()->selesai()->create(['user_id' => $pemilik->id]);

        $this->actingAs($orangLain)
            ->post('/portal/insiden/' . $incident->id . '/nilai', ['rating' => 4])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNull($incident->fresh()->rating);
    }

    public function test_rating_di_luar_rentang_ditolak(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $incident = Incident::factory()->selesai()->create(['user_id' => $pemilik->id]);

        $this->actingAs($pemilik)
            ->post('/portal/insiden/' . $incident->id . '/nilai', ['rating' => 9])
            ->assertSessionHasErrors('rating');

        $this->assertNull($incident->fresh()->rating);
    }

    public function test_indeks_kepuasan_dihitung_dari_rating(): void
    {
        Incident::factory()->selesai()->create(['rating' => 5]);
        Incident::factory()->selesai()->create(['rating' => 3]);
        Incident::factory()->selesai()->create(['rating' => null]);

        $ikm = app(\App\Services\IncidentService::class)->satisfactionIndex();

        $this->assertSame(4.0, (float) $ikm['rata_rata']);
        $this->assertSame(2, (int) $ikm['jumlah_penilai']);
    }

    public function test_notifikasi_web_tampil_dan_bisa_ditandai_dibaca(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $notif = Notification::create([
            'user_id' => $pelapor->id,
            'judul' => 'Status insiden berubah: Diproses',
            'pesan' => 'Insiden Anda sedang ditangani.',
            'link' => '/portal/insiden/1',
            'icon' => 'activity',
        ]);

        $this->actingAs($pelapor)->get('/portal/notifikasi')->assertOk()->assertSee('Status insiden berubah');

        $this->actingAs($pelapor)
            ->get('/portal/notifikasi/' . $notif->id . '/baca')
            ->assertRedirect('/portal/insiden/1');

        $this->assertTrue((bool) $notif->fresh()->dibaca);
    }

    public function test_baca_semua_notifikasi(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        Notification::create(['user_id' => $pelapor->id, 'judul' => 'A', 'pesan' => 'a']);
        Notification::create(['user_id' => $pelapor->id, 'judul' => 'B', 'pesan' => 'b']);

        $this->actingAs($pelapor)->post('/portal/notifikasi/baca-semua')->assertRedirect();

        $this->assertSame(0, Notification::where('user_id', $pelapor->id)->where('dibaca', false)->count());
    }
}
