@extends('layouts.portal')
@section('title', 'Bantuan & FAQ')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-600 mb-2">Pusat Bantuan</p>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Bantuan &amp; FAQ</h1>
    <p class="text-slate-500 text-sm mb-8">Pertanyaan yang sering ditanyakan seputar layanan SIGAP.</p>

    <div class="space-y-3">
        @foreach([
            ['Apa itu SIGAP?', 'SIGAP (Sistem Informasi Penanganan Insiden Siber Satuan) adalah platform pelaporan dan penanganan insiden keamanan siber di lingkungan satuan. Anggota satuan dapat melaporkan phishing, malware, akses ilegal, kebocoran data, dan insiden siber lainnya, lalu memantau penanganannya hingga selesai.'],
            ['Apakah harus daftar untuk melapor?', 'Ya, Anda perlu mendaftar akun pelapor terlebih dahulu. Pendaftaran hanya membutuhkan nama, email, dan kata sandi.'],
            ['Bagaimana cara membuat laporan insiden yang baik?', 'Pilih kategori yang sesuai, tulis judul yang jelas, jelaskan kronologinya (kapan ditemukan, perangkat atau akun yang terdampak, langkah yang sudah dilakukan), tentukan tingkat keparahan, isi nama satuan dan lokasi kejadian, kaitkan aset TI bila relevan, dan sertakan foto bukti bila ada.'],
            ['Apa arti status insiden?', '<b>Menunggu</b>: laporan diterima dan menunggu tindak lanjut petugas.<br><b>Diproses</b>: insiden sedang ditangani penanggung jawab.<br><b>Selesai</b>: insiden sudah teratasi.<br><b>Ditolak</b>: laporan tidak valid atau merupakan duplikat.'],
            ['Apa itu tingkat keparahan Darurat?', 'Tingkat keparahan menentukan prioritas penanganan. Pilih <b>Darurat</b> untuk insiden yang sedang berlangsung dan berdampak luas, misalnya serangan ransomware aktif atau kebocoran data besar. Insiden darurat menjadi perhatian utama petugas dan dipantau dengan SLA ketat.'],
            ['Apa itu SLA 24 jam?', 'Setiap laporan yang berstatus Menunggu lebih dari 24 jam akan otomatis muncul sebagai peringatan SLA di dashboard petugas, sehingga tidak ada laporan yang terabaikan.'],
            ['Bagaimana saya tahu laporan saya ditanggapi?', 'Anda menerima <b>notifikasi otomatis</b> (ikon lonceng di bagian atas halaman) setiap kali status laporan berubah atau ada tanggapan dari petugas. Anda juga dapat menekan tombol <b>Ikuti</b> pada laporan lain untuk menerima perkembangannya.'],
            ['Bisakah saya menghapus laporan?', 'Bisa, selama statusnya masih <b>Menunggu</b> dan laporan itu milik Anda. Buka halaman <b>Laporanku</b> lalu tekan ikon hapus.'],
            ['Apa itu penilaian kepuasan?', 'Setelah insiden berstatus Selesai, pelapor dapat memberi nilai 1–5 bintang. Nilai ini dirangkum menjadi Indeks Kepuasan yang tampil di halaman Statistik dan dashboard petugas sebagai bahan evaluasi layanan.'],
            ['Apakah data saya aman?', 'Ya. Kata sandi disimpan terenkripsi, akses dibatasi berdasarkan peran, dan sistem dilindungi dari serangan umum seperti brute-force. Jangan pernah membagikan kata sandi Anda kepada siapa pun, dan jangan menuliskan kata sandi di dalam deskripsi laporan.'],
        ] as [$q, $a])
        <details class="reveal group bg-white rounded-xl border border-slate-200 overflow-hidden">
            <summary class="flex items-center justify-between gap-4 px-5 py-4 cursor-pointer font-bold text-slate-800 text-sm hover:bg-slate-50 transition list-none">
                {{ $q }}
                <span class="text-brand-700 text-lg leading-none group-open:rotate-45 transition-transform shrink-0 font-bold">+</span>
            </summary>
            <div class="px-5 pb-5 text-sm text-slate-500 leading-relaxed">{!! $a !!}</div>
        </details>
        @endforeach
    </div>
</div>
@endsection
