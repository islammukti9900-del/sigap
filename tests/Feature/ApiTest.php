<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }

    public function test_daftar_insiden_publik_bisa_diakses_tanpa_login(): void
    {
        Incident::factory()->create(['judul' => 'Insiden Publik Satu']);

        // Index insiden publik dikembalikan sebagai koleksi resource.
        $this->getJson('/api/incidents')
            ->assertOk()
            ->assertJsonFragment(['judul' => 'Insiden Publik Satu']);
    }

    public function test_filter_insiden_api_berdasarkan_prioritas(): void
    {
        Incident::factory()->create(['judul' => 'Yang Darurat', 'prioritas' => 'darurat']);
        Incident::factory()->create(['judul' => 'Yang Biasa', 'prioritas' => 'biasa']);

        $this->getJson('/api/incidents?prioritas=darurat')
            ->assertOk()
            ->assertJsonFragment(['judul' => 'Yang Darurat'])
            ->assertJsonMissing(['judul' => 'Yang Biasa']);
    }

    public function test_pelapor_membuat_insiden_via_api(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $kategori = Category::factory()->create();
        $aset = Asset::factory()->create();

        $this->withToken($this->tokenFor($pelapor))
            ->postJson('/api/incidents', [
                'judul' => 'Malware di workstation staf',
                'deskripsi' => 'Antivirus mendeteksi trojan.',
                'satuan' => 'Korem 081/Dhirotsaha Jaya',
                'lokasi_teks' => 'Ruang Staf Ops',
                'category_id' => $kategori->id,
                'asset_id' => $aset->id,
                'prioritas' => 'darurat',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('incidents', [
            'judul' => 'Malware di workstation staf',
            'user_id' => $pelapor->id,
            'asset_id' => $aset->id,
            'prioritas' => 'darurat',
        ]);
    }

    public function test_pelapor_dilarang_ubah_status_via_api(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create(['status' => 'menunggu']);

        $this->withToken($this->tokenFor($pelapor))
            ->postJson('/api/incidents/' . $incident->id . '/status', ['status' => 'diproses'])
            ->assertForbidden();

        $this->assertSame('menunggu', $incident->fresh()->status);
    }

    public function test_staff_ubah_status_via_api(): void
    {
        $petugas = User::factory()->petugas()->create();
        $incident = Incident::factory()->create(['status' => 'menunggu']);

        $this->withToken($this->tokenFor($petugas))
            ->postJson('/api/incidents/' . $incident->id . '/status', ['status' => 'diproses'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('diproses', $incident->fresh()->status);
    }

    public function test_assign_via_api_ke_petugas_dan_gagal_ke_pelapor(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create();

        $this->withToken($this->tokenFor($admin))
            ->postJson('/api/incidents/' . $incident->id . '/assign', ['assigned_to' => $petugas->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame($petugas->id, $incident->fresh()->assigned_to);
        $this->assertTrue(Notification::where('user_id', $petugas->id)->exists());

        $this->withToken($this->tokenFor($admin))
            ->postJson('/api/incidents/' . $incident->id . '/assign', ['assigned_to' => $pelapor->id])
            ->assertStatus(422);
    }

    public function test_komentar_dan_ikuti_via_api(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $incident = Incident::factory()->create();
        $token = $this->tokenFor($pelapor);

        $this->withToken($token)
            ->postJson('/api/incidents/' . $incident->id . '/comments', ['isi' => 'Baik, ditunggu tindak lanjutnya.'])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('comments', ['incident_id' => $incident->id, 'user_id' => $pelapor->id]);

        $this->withToken($token)
            ->postJson('/api/incidents/' . $incident->id . '/follow')
            ->assertOk()
            ->assertJsonPath('data.followed', true);

        $this->assertDatabaseHas('follows', ['incident_id' => $incident->id, 'user_id' => $pelapor->id]);
    }

    public function test_rating_via_api_mematuhi_aturan(): void
    {
        $pemilik = User::factory()->pelapor()->create();
        $token = $this->tokenFor($pemilik);

        $belumSelesai = Incident::factory()->create(['user_id' => $pemilik->id, 'status' => 'diproses']);
        $this->withToken($token)
            ->postJson('/api/incidents/' . $belumSelesai->id . '/rate', ['rating' => 5])
            ->assertStatus(422);

        $selesai = Incident::factory()->selesai()->create(['user_id' => $pemilik->id]);
        $this->withToken($token)
            ->postJson('/api/incidents/' . $selesai->id . '/rate', ['rating' => 4])
            ->assertOk()
            ->assertJsonPath('data.rating', 4);

        $this->assertSame(4, $selesai->fresh()->rating);
    }

    public function test_notifikasi_api_daftar_dan_tandai_dibaca(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $token = $this->tokenFor($pelapor);
        $notif = Notification::create([
            'user_id' => $pelapor->id,
            'judul' => 'Ada perkembangan',
            'pesan' => 'Status insiden berubah.',
        ]);

        $this->withToken($token)
            ->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonFragment(['judul' => 'Ada perkembangan']);

        $this->withToken($token)
            ->postJson('/api/notifications/' . $notif->id . '/read')
            ->assertOk();

        $this->assertTrue((bool) $notif->fresh()->dibaca);

        Notification::create(['user_id' => $pelapor->id, 'judul' => 'Lainnya', 'pesan' => 'x']);
        $this->withToken($token)->postJson('/api/notifications/read-all')->assertOk();
        $this->assertSame(0, Notification::where('user_id', $pelapor->id)->where('dibaca', false)->count());
    }

    public function test_kategori_api_publik_dan_crud_staff(): void
    {
        Category::factory()->create(['nama' => 'Kebocoran Data']);
        $this->getJson('/api/categories')->assertOk()->assertJsonFragment(['nama' => 'Kebocoran Data']);

        $admin = User::factory()->admin()->create();
        $token = $this->tokenFor($admin);

        $this->withToken($token)
            ->postJson('/api/categories', ['nama' => 'Deface', 'icon' => 'globe', 'warna' => '#2563EB'])
            ->assertCreated();

        $this->assertDatabaseHas('categories', ['nama' => 'Deface']);
    }

    public function test_detail_insiden_tidak_ditemukan_api(): void
    {
        $this->getJson('/api/incidents/99999')->assertStatus(404);
    }
}
