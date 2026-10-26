<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Incident;
use App\Models\Indicator;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Akun demo (didokumentasikan hanya di README) ===
        $admin = User::create([
            'name' => 'Admin SIGAP',
            'email' => 'admin@sigap.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $petugas1 = User::create([
            'name' => 'Rizky Pratama',
            'email' => 'petugas@sigap.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PETUGAS,
        ]);

        $petugas2 = User::create([
            'name' => 'Dewi Anggraini',
            'email' => 'petugas2@sigap.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PETUGAS,
        ]);

        $pelapor1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'pelapor@sigap.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PELAPOR,
        ]);

        $pelapor2 = User::create([
            'name' => 'Sari Wulandari',
            'email' => 'sari@sigap.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PELAPOR,
        ]);

        $pelapor3 = User::create([
            'name' => 'Andi Kurniawan',
            'email' => 'andi@sigap.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PELAPOR,
        ]);

        // === Kategori insiden ===
        $kategori = [];
        foreach ([
            ['Phishing', 'mail', '#0E7490', 'Penipuan berkedok email/pesan untuk mencuri kredensial.'],
            ['Malware', 'alert', '#DC2626', 'Perangkat lunak berbahaya: virus, ransomware, trojan.'],
            ['Akses Ilegal', 'lock', '#B45309', 'Percobaan masuk tanpa hak ke sistem atau akun.'],
            ['Kebocoran Data', 'file', '#0F766E', 'Data internal satuan bocor atau terekspos ke pihak luar.'],
            ['Deface', 'globe', '#2563EB', 'Perubahan tampilan situs/aplikasi oleh pihak tidak berwenang.'],
            ['Gangguan Layanan', 'activity', '#EA580C', 'Layanan TI satuan terganggu, lambat, atau tidak dapat diakses.'],
            ['Lainnya', 'shield', '#64748B', 'Insiden keamanan lain di luar kategori di atas.'],
        ] as [$nama, $icon, $warna, $deskripsi]) {
            $kategori[$nama] = Category::create(compact('nama', 'icon', 'warna', 'deskripsi'));
        }

        // === Aset TI ===
        $aset = [];
        foreach ([
            ['nama' => 'Server Aplikasi SIGAP-01', 'tipe' => 'server', 'ip_address' => '10.10.1.10', 'lokasi' => 'Ruang Server Makodam V/Brawijaya', 'status' => 'aktif', 'deskripsi' => 'Server utama aplikasi internal satuan.'],
            ['nama' => 'Server Basis Data DB-01', 'tipe' => 'server', 'ip_address' => '10.10.1.11', 'lokasi' => 'Ruang Server Makodam V/Brawijaya', 'status' => 'aktif', 'deskripsi' => 'Server basis data terpusat.'],
            ['nama' => 'Firewall Perimeter FW-EDGE-01', 'tipe' => 'jaringan', 'ip_address' => '10.10.0.1', 'lokasi' => 'Ruang NOC, Gedung Utama', 'status' => 'aktif', 'deskripsi' => 'Firewall gerbang utama jaringan satuan.'],
            ['nama' => 'Router Gateway Kodim 0824', 'tipe' => 'jaringan', 'ip_address' => '10.24.0.1', 'lokasi' => 'Makodim 0824/Jember', 'status' => 'pemeliharaan', 'deskripsi' => 'Router penghubung jaringan Kodim ke pusat.'],
            ['nama' => 'Workstation Staf Intel WS-INT-04', 'tipe' => 'workstation', 'ip_address' => '10.10.2.24', 'lokasi' => 'Gedung Staf Intel, Lt. 2', 'status' => 'rusak', 'deskripsi' => 'Komputer kerja staf intelijen.'],
            ['nama' => 'Portal Informasi Satuan', 'tipe' => 'aplikasi', 'ip_address' => '10.10.1.20', 'lokasi' => 'Server Aplikasi SIGAP-01', 'status' => 'aktif', 'deskripsi' => 'Situs informasi resmi satuan.'],
            ['nama' => 'Server Surel MAIL-01', 'tipe' => 'server', 'ip_address' => '10.10.1.15', 'lokasi' => 'Ruang Server Makodam V/Brawijaya', 'status' => 'aktif', 'deskripsi' => 'Server email dinas satuan.'],
            ['nama' => 'Workstation Administrasi WS-ADM-02', 'tipe' => 'workstation', 'ip_address' => '10.10.2.31', 'lokasi' => 'Gedung Administrasi, Lt. 1', 'status' => 'aktif', 'deskripsi' => 'Komputer kerja bagian administrasi.'],
        ] as $row) {
            $aset[$row['nama']] = Asset::create($row);
        }

        // === Insiden contoh ===
        $buat = function (array $data) {
            $incident = Incident::create($data['attrs']);
            $incident->forceFill([
                'created_at' => $data['dibuat'],
                'updated_at' => $data['diubah'] ?? $data['dibuat'],
            ])->save();

            return $incident;
        };

        $i1 = $buat([
            'dibuat' => now()->subHours(5),
            'attrs' => [
                'user_id' => $pelapor1->id,
                'category_id' => $kategori['Phishing']->id,
                'asset_id' => $aset['Server Surel MAIL-01']->id,
                'assigned_to' => $petugas1->id,
                'judul' => 'Email phishing mengatasnamakan bank diterima staf administrasi',
                'deskripsi' => 'Beberapa staf menerima email yang mengaku dari bank mitra dan meminta verifikasi kredensial melalui tautan palsu. Satu staf sempat membuka tautan tersebut sebelum menyadari halaman login berbeda dari biasanya. Mohon segera ditindaklanjuti dan kredensial terkait direset.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Gedung Administrasi, Lt. 1',
                'status' => 'diproses',
                'prioritas' => 'darurat',
            ],
        ]);

        $i2 = $buat([
            'dibuat' => now()->subDay(),
            'attrs' => [
                'user_id' => $pelapor2->id,
                'category_id' => $kategori['Malware']->id,
                'asset_id' => $aset['Workstation Staf Intel WS-INT-04']->id,
                'assigned_to' => $petugas2->id,
                'judul' => 'Workstation staf intel terindikasi ransomware',
                'deskripsi' => 'Seluruh berkas pada workstation WS-INT-04 berubah ekstensi dan muncul pesan tebusan di layar. Perangkat sudah dicabut dari jaringan. Perlu analisis forensik dan pemulihan dari cadangan terakhir.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Gedung Staf Intel, Lt. 2',
                'status' => 'diproses',
                'prioritas' => 'darurat',
            ],
        ]);

        $buat([
            'dibuat' => now()->subHours(30),
            'attrs' => [
                'user_id' => $pelapor3->id,
                'category_id' => $kategori['Akses Ilegal']->id,
                'asset_id' => $aset['Server Aplikasi SIGAP-01']->id,
                'judul' => 'Percobaan login berulang ke server aplikasi dari alamat IP asing',
                'deskripsi' => 'Log firewall menunjukkan lebih dari 1.200 percobaan login gagal ke server aplikasi dalam 2 jam dari beberapa alamat IP luar negeri. Belum ada yang berhasil masuk, tetapi pola serangan masih berlangsung.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Ruang Server Makodam V/Brawijaya',
                'status' => 'menunggu',
                'prioritas' => 'penting',
            ],
        ]);

        $buat([
            'dibuat' => now()->subDays(6),
            'diubah' => now()->subDays(5),
            'attrs' => [
                'user_id' => $pelapor1->id,
                'category_id' => $kategori['Kebocoran Data']->id,
                'assigned_to' => $petugas1->id,
                'judul' => 'Dokumen internal satuan ditemukan beredar di forum publik',
                'deskripsi' => 'Dokumen jadwal kegiatan internal ditemukan terunggah di forum publik. Dokumen sudah dilaporkan untuk diturunkan dan akses berbagi internal diperketat.',
                'satuan' => 'Korem 081/Dhirotsaha Jaya',
                'lokasi_teks' => 'Makorrem 081, Madiun',
                'status' => 'selesai',
                'prioritas' => 'darurat',
                'rating' => 5,
                'rated_at' => now()->subDays(5),
            ],
        ]);

        $buat([
            'dibuat' => now()->subDays(4),
            'diubah' => now()->subDays(3),
            'attrs' => [
                'user_id' => $pelapor2->id,
                'category_id' => $kategori['Deface']->id,
                'asset_id' => $aset['Portal Informasi Satuan']->id,
                'assigned_to' => $petugas2->id,
                'judul' => 'Tampilan portal informasi satuan diubah pihak tidak berwenang',
                'deskripsi' => 'Halaman depan portal informasi satuan berubah tampilan dengan pesan dari peretas. Portal sudah dipulihkan dari cadangan dan kredensial admin diganti.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Server Aplikasi SIGAP-01',
                'status' => 'selesai',
                'prioritas' => 'penting',
                'rating' => 4,
                'rated_at' => now()->subDays(3),
            ],
        ]);

        $buat([
            'dibuat' => now()->subHours(9),
            'attrs' => [
                'user_id' => $pelapor3->id,
                'category_id' => $kategori['Gangguan Layanan']->id,
                'asset_id' => $aset['Server Basis Data DB-01']->id,
                'assigned_to' => $petugas1->id,
                'judul' => 'Server basis data lambat, aplikasi internal nyaris tidak dapat diakses',
                'deskripsi' => 'Sejak pagi aplikasi internal sangat lambat. Pemeriksaan awal menunjukkan pemakaian disk server basis data mencapai 98%. Perlu pembersihan log dan penambahan kapasitas.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Ruang Server Makodam V/Brawijaya',
                'status' => 'diproses',
                'prioritas' => 'penting',
            ],
        ]);

        $buat([
            'dibuat' => now()->subHours(2),
            'attrs' => [
                'user_id' => $pelapor2->id,
                'category_id' => $kategori['Phishing']->id,
                'judul' => 'Pesan WhatsApp palsu mengaku sebagai komandan satuan',
                'deskripsi' => 'Beredar pesan WhatsApp dari nomor tidak dikenal yang mengaku sebagai komandan satuan dan meminta data personel. Sudah diimbau ke seluruh anggota untuk tidak menanggapi.',
                'satuan' => 'Kodim 0824/Jember',
                'lokasi_teks' => 'Makodim 0824/Jember',
                'status' => 'menunggu',
                'prioritas' => 'biasa',
            ],
        ]);

        $buat([
            'dibuat' => now()->subHours(3),
            'attrs' => [
                'user_id' => $pelapor1->id,
                'category_id' => $kategori['Malware']->id,
                'asset_id' => $aset['Workstation Administrasi WS-ADM-02']->id,
                'judul' => 'Flashdisk tidak dikenal ditemukan tertancap di workstation administrasi',
                'deskripsi' => 'Petugas piket menemukan flashdisk tanpa label tertancap di WS-ADM-02. Flashdisk sudah diamankan dan workstation dipindai antivirus, menunggu pemeriksaan lanjutan.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Gedung Administrasi, Lt. 1',
                'status' => 'menunggu',
                'prioritas' => 'penting',
            ],
        ]);

        $buat([
            'dibuat' => now()->subDays(7),
            'diubah' => now()->subDays(6),
            'attrs' => [
                'user_id' => $pelapor3->id,
                'category_id' => $kategori['Akses Ilegal']->id,
                'judul' => 'Akun VPN anggota terdeteksi aktif di luar jam dinas',
                'deskripsi' => 'Sistem mencatat akun VPN salah satu anggota aktif pukul 02.00 dini hari. Setelah dikonfirmasi, ternyata akses dilakukan sendiri untuk mengambil berkas dinas. Sudah diberikan pembinaan.',
                'satuan' => 'Kodim 0809/Kediri',
                'lokasi_teks' => 'Makodim 0809/Kediri',
                'status' => 'selesai',
                'prioritas' => 'biasa',
                'rating' => 5,
                'rated_at' => now()->subDays(6),
            ],
        ]);

        $buat([
            'dibuat' => now()->subDays(5),
            'diubah' => now()->subDays(5),
            'attrs' => [
                'user_id' => $pelapor2->id,
                'category_id' => $kategori['Gangguan Layanan']->id,
                'asset_id' => $aset['Router Gateway Kodim 0824']->id,
                'judul' => 'Koneksi jaringan antar gedung terputus total',
                'deskripsi' => 'Laporan duplikat dari insiden gangguan router gateway yang sama. Ditutup sebagai duplikat; penanganan dilanjutkan pada laporan utama.',
                'satuan' => 'Kodim 0824/Jember',
                'lokasi_teks' => 'Makodim 0824/Jember',
                'status' => 'ditolak',
                'prioritas' => 'biasa',
            ],
        ]);

        $buat([
            'dibuat' => now()->subHours(14),
            'attrs' => [
                'user_id' => $pelapor1->id,
                'category_id' => $kategori['Kebocoran Data']->id,
                'assigned_to' => $petugas2->id,
                'judul' => 'Daftar kontak internal tersebar ke grup percakapan publik',
                'deskripsi' => 'Tangkapan layar daftar kontak internal satuan beredar di grup percakapan publik. Sedang ditelusuri sumber penyebarannya dan daftar kontak diperbarui dengan format terbatas.',
                'satuan' => 'Korem 081/Dhirotsaha Jaya',
                'lokasi_teks' => 'Makorrem 081, Madiun',
                'status' => 'diproses',
                'prioritas' => 'penting',
            ],
        ]);

        $buat([
            'dibuat' => now()->subDays(10),
            'attrs' => [
                'user_id' => $pelapor3->id,
                'category_id' => $kategori['Lainnya']->id,
                'judul' => 'Permintaan audit keamanan berkala untuk ruang server',
                'deskripsi' => 'Mengusulkan audit keamanan berkala untuk ruang server, termasuk daftar akses fisik dan peninjauan ulang hak akses akun administrator.',
                'satuan' => 'Kodam V/Brawijaya',
                'lokasi_teks' => 'Ruang Server Makodam V/Brawijaya',
                'status' => 'menunggu',
                'prioritas' => 'biasa',
            ],
        ]);

        // === Basis Data Indikator (IoC) ===
        $indikator = [];
        foreach ([
            ['ip', '185.220.101.47', 'c2', 'AlienVault OTX', 'Server command & control botnet yang aktif menargetkan Asia Tenggara.'],
            ['ip', '103.147.8.23', 'malware', 'AbuseIPDB', 'IP distributor malware trojan perbankan.'],
            ['ip', '45.155.204.111', 'botnet', 'Feodo Tracker', 'Node botnet Emotet yang masih aktif.'],
            ['ip', '193.32.162.18', 'c2', 'URLhaus', 'Infrastruktur C2 keluarga Cobalt Strike.'],
            ['domain', 'brawijaya-login-palsu.xyz', 'phishing', 'Laporan satuan', 'Domain phishing yang meniru halaman login internal satuan.'],
            ['domain', 'update-kredensial-amanah.com', 'phishing', 'PhishTank', 'Meniru halaman verifikasi bank untuk mencuri kredensial.'],
            ['domain', 'cdn-telemetry-update.net', 'c2', 'URLhaus', 'Domain distribusi payload tahap kedua.'],
            ['domain', 'mail-verifikasi-satuan.my.id', 'phishing', 'Laporan satuan', 'Meniru pemberitahuan email dinas satuan.'],
            ['url', 'brawijaya-login-palsu.xyz/verifikasi', 'phishing', 'Laporan satuan', 'Halaman panen kredensial pada domain phishing satuan.'],
            ['hash', hash('sha256', 'sigap-sample-ransomware-wannacry-variant'), 'malware', 'MalwareBazaar', 'Sampel ransomware varian WannaCry yang dimodifikasi.'],
            ['hash', hash('sha256', 'sigap-sample-trojan-banking-emotet'), 'malware', 'MalwareBazaar', 'Dropper trojan perbankan keluarga Emotet.'],
            ['hash', hash('sha256', 'sigap-sample-keygen-berbahaya'), 'lainnya', 'Analisis internal', 'Hash utilitas bajakan yang disisipi pencuri data.'],
        ] as [$type, $value, $threat, $source, $notes]) {
            $indikator[$value] = Indicator::create([
                'type' => $type,
                'value' => $value,
                'threat' => $threat,
                'source' => $source,
                'notes' => $notes,
                'added_by' => $petugas1->id,
            ]);
        }

        // Tautkan sebagian indikator ke insiden contoh agar relasinya terlihat.
        $i1->indicators()->syncWithoutDetaching([$indikator['brawijaya-login-palsu.xyz']->id]);
        $i2->indicators()->syncWithoutDetaching([$indikator[hash('sha256', 'sigap-sample-ransomware-wannacry-variant')]->id]);

        // === Komentar contoh ===
        Comment::create([
            'incident_id' => $i1->id,
            'user_id' => $petugas1->id,
            'isi' => 'Sudah kami tindaklanjuti. Tautan phishing diblokir di firewall dan seluruh kredensial email staf administrasi direset. Mohon staf yang sempat membuka tautan segera mengganti kata sandi.',
        ]);
        Comment::create([
            'incident_id' => $i1->id,
            'user_id' => $pelapor1->id,
            'isi' => 'Baik, terima kasih. Kata sandi saya sudah diganti dan autentikasi dua faktor sudah diaktifkan.',
        ]);
        Comment::create([
            'incident_id' => $i2->id,
            'user_id' => $petugas2->id,
            'isi' => 'Workstation sudah diisolasi. Tim sedang melakukan pencitraan disk untuk analisis forensik sebelum pemulihan dari cadangan.',
        ]);

        // === Pengikut ===
        Follow::create(['user_id' => $pelapor2->id, 'incident_id' => $i1->id]);
        Follow::create(['user_id' => $petugas1->id, 'incident_id' => $i2->id]);

        // === Notifikasi contoh ===
        Notification::create([
            'user_id' => $pelapor1->id,
            'judul' => 'Status insiden berubah: Diproses',
            'pesan' => 'Insiden "Email phishing mengatasnamakan bank diterima staf administrasi" sekarang berstatus Diproses.',
            'link' => '/portal/insiden/' . $i1->id,
            'icon' => 'activity',
            'dibaca' => false,
        ]);
        Notification::create([
            'user_id' => $petugas1->id,
            'judul' => 'Anda ditugaskan menangani insiden',
            'pesan' => 'Insiden "Email phishing mengatasnamakan bank diterima staf administrasi" (Kodam V/Brawijaya) ditugaskan kepada Anda.',
            'link' => '/insiden/' . $i1->id,
            'icon' => 'user',
            'dibaca' => false,
        ]);
    }
}
