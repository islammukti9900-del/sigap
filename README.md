# SIGAP — Sistem Informasi Penanganan Insiden Siber Satuan

SIGAP adalah platform pelaporan dan penanganan insiden keamanan siber untuk satuan militer. Anggota satuan dapat melaporkan insiden (phishing, malware, akses ilegal, kebocoran data, deface, gangguan layanan) lengkap dengan bukti foto dan aset TI terkait, sementara petugas menindaklanjuti melalui panel kendali dengan alur status, penugasan penanggung jawab, peringatan SLA, dan notifikasi otomatis — semua terpantau transparan sampai selesai.

Dibangun untuk **Kartika Cyber Hackathon 2026** (PUSSIBERAD TNI AD × CODEPOLITAN), kategori **Dukungan Teknologi (Core Infra)**, oleh **Islam Mukti** — SMKN 3 Tuban, Jurusan Rekayasa Perangkat Lunak.

## Fitur Utama

**Portal Pelapor**
- Beranda dengan statistik langsung, kategori insiden, daftar insiden darurat, dan sebaran per satuan
- Jelajahi insiden: pencarian + filter status / kategori / tingkat keparahan
- Formulir lapor insiden dengan unggah foto bukti dan **draft otomatis** (localStorage)
- Halaman "Laporanku" dengan 5 kartu statistik
- Detail insiden: linimasa status, komentar, tombol Ikuti, bagikan (WhatsApp/Telegram/X/salin tautan)
- Penilaian kepuasan 1–5 bintang (khusus pemilik, hanya setelah status Selesai)
- Profil (ubah nama/email/kata sandi + statistik saya) dan halaman Statistik Publik

**Panel Petugas (Admin & Petugas)**
- Dashboard: kartu statistik animasi count-up, 3 grafik Chart.js (tren bulanan, per kategori, status), widget peringatan SLA (insiden darurat aktif + laporan menunggu > 24 jam), Indeks Kepuasan, daftar insiden terbaru
- Manajemen insiden: ubah status (dengan linimasa + notifikasi), ubah prioritas, **tugaskan penanggung jawab**, hapus, moderasi komentar
- **Export CSV** (mengikuti filter aktif, UTF-8 BOM untuk Excel), cetak rekap, **bulk ubah status**
- Manajemen **Aset TI**: CRUD server/workstation/jaringan/aplikasi dengan IP, lokasi, status (aktif/rusak/pemeliharaan) dan jumlah insiden terkait
- Manajemen kategori insiden & pengguna (khusus admin)
- Palet perintah **Ctrl+K** dan pencarian cepat insiden

**Sistem & Keamanan**
- Notifikasi otomatis (lonceng + badge + dropdown): perubahan status, penugasan, komentar petugas — web & API
- REST API lengkap dengan Laravel Sanctum (token)
- Rate limiter bernama: login 5/menit per email+IP, register/reset 3/menit per IP, API 60/menit
- Security headers, detail error API disembunyikan saat `APP_DEBUG=false`, validasi unggahan ketat (mime, ukuran, dimensi)
- PWA (manifest + ikon), tag SEO/Open Graph dinamis, halaman 404 kustom

## Teknologi

- PHP 8.3 · Laravel 13 · Laravel Sanctum
- Blade + Tailwind CSS (CDN) · Chart.js · Vanilla JS
- SQLite (pengembangan/tes) · kompatibel MySQL/MariaDB untuk produksi
- PHPUnit (101 tes fitur, 497 assertions — semua lulus)

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka **http://127.0.0.1:8000** — Anda akan diarahkan ke portal pelapor di `/portal`. Panel petugas di `/dashboard` (login melalui `/login`).

> Untuk produksi dengan MySQL, sesuaikan `DB_*` di `.env` lalu jalankan ulang `php artisan migrate --seed`.

## Akun Demo (hanya untuk pengujian lokal)

| Peran   | Email              | Kata Sandi |
|---------|--------------------|------------|
| Admin   | admin@sigap.test   | password   |
| Petugas | petugas@sigap.test | password   |
| Pelapor | pelapor@sigap.test | password   |

Akun-akun ini dibuat oleh seeder dan **tidak** ditampilkan di halaman login. Ganti/hapus sebelum dipakai di lingkungan nyata.

Data contoh yang ikut ter-seed: 7 kategori insiden siber, 8 aset TI, dan 12 insiden contoh dari satuan-satuan di Jawa Timur dengan berbagai status dan tingkat keparahan.

## Menjalankan Tes

```bash
php artisan test
```

## Changelog v2 — "Operasi"

Sepuluh kemampuan operasional baru di atas v1:

1. **Basis Data Indikator (IoC)** — kelola Indicators of Compromise (IP/domain/URL/hash SHA-256) lengkap dengan kategori ancaman (malware/phishing/C2/botnet/lainnya), sumber, dan catatan. Nilai dinormalisasi (huruf kecil, tanpa protokol) dan unik per tipe. Panel staff (petugas boleh menambah, admin mengelola penuh) + API.
2. **Cek Indikator** — halaman pemeriksa di portal pelapor: tempel IP/domain/URL/hash → kartu hasil "ditemukan" (tipe, ancaman, pertama dicatat, sumber, jumlah insiden terkait) atau pesan aman "tidak ditemukan". Tersedia juga via API.
3. **Auto-match indikator ke insiden** — deskripsi insiden dipindai otomatis saat dibuat/diubah; indikator yang cocok tertaut dan tampil sebagai chip "Indikator Terkait" di detail insiden (dan sebaliknya di detail indikator).
4. **Hash bukti forensik** — SHA-256 berkas bukti dihitung saat unggah dan disimpan (`evidence_hash`), ditampilkan terpotong monospace; bila cocok dengan IoC hash, muncul banner merah "Bukti digital cocok dengan indikator berbahaya" dan indikator tertaut otomatis.
5. **Peta Risiko Satuan** — skor risiko per satuan dari insiden terbuka (Darurat 10, Penting 5, Biasa 2, +5 bila ada pelanggaran SLA), halaman peringkat dengan bilah skor & kategori dominan, plus widget "Satuan Berisiko Tertinggi" (3 teratas) di dashboard.
6. **Eskalasi otomatis** — insiden Darurat yang *menunggu > 4 jam* atau *diproses tanpa perkembangan > 24 jam* (berdasarkan `updated_at`) ditandai tereskalasi: badge ESKALASI berkedip, filter khusus di daftar staff, dan blok peringatan khusus di dashboard. Aturan ini terkomputasi (tanpa kolom baru) sehingga selalu konsisten dengan data.
7. **Ruang Situasi** (`/situasi`) — papan pantau gelap layar penuh khusus staff: penghitung besar langsung, daftar darurat aktif + tereskalasi, 5 insiden terbaru, aset bermasalah; memperbarui diri tiap 30 detik via polling JSON (`/situasi/data`) dan tetap tampil benar tanpa JavaScript.
8. **SITREP** (`/sitrep`) — laporan situasi siap cetak per periode (hari ini / 7 / 30 hari): ringkasan status, rincian kategori & keparahan, satuan berisiko, insiden darurat & tereskalasi, aset bermasalah, IKM, dan MTTR, dengan CSS cetak bersih.
9. **Audit log aktivitas** (`activity_logs`) — jejak aksi penting: insiden dibuat/status/penugasan, komentar dihapus, aset dibuat/diubah/dihapus, indikator dibuat/dihapus. Halaman admin `/aktivitas` dengan paginasi + filter per aksi.
10. **Kode QR insiden** — QR menuju halaman insiden pada detail staff & portal (`simplesoftwareio/simple-qrcode`).

### Fitur tambahan

- **Komentar internal petugas** — komentar ber-visibilitas `publik`/`internal`; catatan internal hanya terlihat staff (badge "Internal"), tidak pernah tampil di portal pelapor maupun API publik; pelapor selalu dipaksa publik.
- **Metrik penanganan (MTTR)** — rata-rata waktu respons (dibuat → pertama diproses) dan resolusi (dibuat → selesai) dari linimasa status, keseluruhan dan per tingkat keparahan; tampil di dashboard & SITREP.
- **Heatmap aktivitas** — kalender gaya GitHub (15 minggu terakhir) jumlah insiden per hari, dirender server-side; tampil di dashboard staff & halaman Statistik Publik.

### Paket keamanan (anti-bobol)

- **2FA TOTP untuk staff** (`pragmarx/google2fa`) — aktif/nonaktif dari menu "Keamanan 2FA": secret terenkripsi, QR penyiapan, konfirmasi kode, 8 kode pemulihan sekali pakai (tersimpan ter-hash). Setelah login password, staff wajib lolos tantangan TOTP (gerbang sesi) sebelum membuka halaman panel; kode pemulihan menjadi cadangan dan hangus setelah dipakai. Pelapor tidak diwajibkan. *Catatan: token API (Sanctum) tidak digerbang 2FA — 2FA berlaku untuk sesi web panel.*
- **Lockout login progresif** — 5 kegagalan login per akun+IP mengunci login selama 15 menit dengan pesan yang jelas (di atas rate limiter bernama yang sudah ada).
- **Captcha aritmatika buatan sendiri** — soal hitungan sederhana di form login & registrasi web, jawaban disimpan di sesi dan berganti tiap render; dapat dimatikan lewat `CAPTCHA_ENABLED=false` (otomatis nonaktif saat pengujian).
- **Catatan keamanan di audit log** — login berhasil/gagal, penguncian akun, 2FA diaktifkan/dinonaktifkan, dan perubahan password tercatat di `activity_logs`; halaman `/aktivitas` punya filter "Keamanan".
- **Header keamanan diperkuat** — Content-Security-Policy yang disesuaikan dengan CDN yang benar-benar dipakai (Tailwind, Chart.js, Google Fonts), Strict-Transport-Security, X-Frame-Options: DENY, X-Content-Type-Options: nosniff, Referrer-Policy, dan Permissions-Policy pada semua respons.
- **Kebijakan password staff** — akun staff wajib ≥ 10 karakter dengan huruf besar, huruf kecil, dan angka (pembuatan akun & ganti password); pelapor tetap ≥ 8 karakter.
- **Kekerasan unggahan** — berkas bukti divalidasi dari isi sebenarnya (mime konten), disimpan dengan nama hash acak di storage, dan ekstensi ganda/terselubung (mis. PHP menyamar PNG) ditolak — terverifikasi oleh tes khusus.

## Pengumpulan Hackathon

Status: aplikasi selesai dan seluruh tes lulus. Yang masih harus disiapkan Islam sebelum batas pengumpulan **15 November 2026**:

- [ ] **Tautan proyek yang sudah di-deploy** (hosting publik agar juri bisa mencoba langsung)
- [ ] **Video presentasi/demo** aplikasi (alur pelapor + panel petugas)
- [ ] **Slide presentasi** (masalah, solusi SIGAP, fitur, teknologi, dampak)
- [ ] Pastikan akun demo di README ini siap dipakai juri pada instans yang di-deploy

## Lisensi

Proyek ini dibuat untuk keperluan lomba dan pembelajaran.
