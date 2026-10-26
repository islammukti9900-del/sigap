@extends('layouts.portal')
@section('title', 'Beranda')

@section('content')
<!-- ============ HERO ============ -->
<section class="mesh-bg text-white relative overflow-hidden">
    <div class="absolute inset-0 grid-overlay"></div>
    <div class="blob w-80 h-80 bg-brand-500 -top-16 -left-16"></div>
    <div class="blob w-72 h-72 bg-cyan-400 top-24 right-[8%]" style="animation-delay:-5s"></div>
    <div class="blob w-60 h-60 bg-cyan-600 bottom-0 left-[38%]" style="animation-delay:-9s"></div>

    <div class="relative max-w-6xl mx-auto px-4 pt-16 pb-24 lg:pt-24 lg:pb-32 grid lg:grid-cols-[1.2fr_1fr] gap-14 items-center">
        <div class="reveal visible">
            <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass text-[11px] font-extrabold uppercase tracking-[0.14em] text-cyan-200 mb-7">
                @include('components.icon', ['name' => 'shield-check', 'class' => 'w-4 h-4'])
                Sistem Informasi Penanganan Insiden Siber Satuan
            </p>
            <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-extrabold leading-[1.08] tracking-tight mb-6">
                Laporkan Insiden Siber.<br>
                <span class="grad-text">Pantau Penanganannya.</span>
            </h1>
            <p class="text-white/65 text-base lg:text-lg leading-relaxed max-w-xl mb-9">
                SIGAP mencatat setiap laporan insiden keamanan siber di satuan Anda — phishing, malware, akses ilegal, hingga kebocoran data — lalu menugaskannya ke penanggung jawab dan memantau statusnya sampai tuntas.
            </p>
            <div class="flex flex-wrap gap-3.5">
                <a href="{{ route('portal.buat') }}" class="btn-primary">
                    @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Buat Laporan Insiden
                </a>
                <a href="{{ route('portal.jelajahi') }}" class="btn-ghost-light">
                    @include('components.icon', ['name' => 'search', 'class' => 'w-4 h-4']) Jelajahi Insiden
                </a>
            </div>
            <div class="flex items-center gap-6 mt-10 text-[13px] font-semibold text-white/55">
                <span class="inline-flex items-center gap-2">@include('components.icon', ['name' => 'check-circle', 'class' => 'w-4 h-4 text-emerald-400']) Tercatat &amp; terdokumentasi</span>
                <span class="inline-flex items-center gap-2">@include('components.icon', ['name' => 'clock', 'class' => 'w-4 h-4 text-cyan-300']) Pantauan SLA 24 jam</span>
                <span class="inline-flex items-center gap-2">@include('components.icon', ['name' => 'bell', 'class' => 'w-4 h-4 text-amber-300']) Notifikasi otomatis</span>
            </div>
        </div>

        <div class="relative hidden lg:block">
            <div class="glass rounded-3xl p-6 float-slow">
                <div class="flex items-center justify-between mb-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-white/50">Situasi Keamanan</p>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-300"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Live</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-white/[0.06] border border-white/10 p-4">
                        <p class="text-3xl font-extrabold" data-count="{{ $stats['total'] }}">0</p>
                        <p class="text-xs text-white/50 font-semibold mt-1">Total Insiden</p>
                    </div>
                    <div class="rounded-2xl bg-white/[0.06] border border-white/10 p-4">
                        <p class="text-3xl font-extrabold text-emerald-300" data-count="{{ $stats['selesai'] }}">0</p>
                        <p class="text-xs text-white/50 font-semibold mt-1">Selesai Ditangani</p>
                    </div>
                    <div class="rounded-2xl bg-white/[0.06] border border-white/10 p-4">
                        <p class="text-3xl font-extrabold text-sky-300" data-count="{{ $stats['diproses'] }}">0</p>
                        <p class="text-xs text-white/50 font-semibold mt-1">Sedang Diproses</p>
                    </div>
                    <div class="rounded-2xl bg-white/[0.06] border border-white/10 p-4">
                        <p class="text-3xl font-extrabold text-red-300" data-count="{{ $stats['darurat'] }}">0</p>
                        <p class="text-xs text-white/50 font-semibold mt-1">Darurat Aktif</p>
                    </div>
                </div>
            </div>
            <div class="glass rounded-2xl px-5 py-4 mt-5 float-slower flex items-center gap-3.5">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-500 flex items-center justify-center shrink-0">
                    @include('components.icon', ['name' => 'activity', 'class' => 'w-5 h-5'])
                </span>
                <div class="leading-tight">
                    <p class="font-extrabold text-sm">Respons Terukur</p>
                    <p class="text-xs text-white/55">Insiden darurat &amp; laporan &gt; 24 jam otomatis tereskalasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ TICKER INSIDEN TERBARU ============ -->
@if($terbaru->count())
<div class="bg-ink-950 text-white overflow-hidden py-3 border-y border-white/10">
    <div class="ticker-track">
        @foreach(collect($terbaru->items())->merge($terbaru->items()) as $r)
        <a href="{{ route('portal.insiden.show', $r->id) }}" class="flex items-center gap-2.5 text-[13px] font-semibold text-white/70 hover:text-white transition shrink-0">
            @include('components.icon', ['name' => 'zap', 'class' => 'w-3.5 h-3.5 text-cyan-400'])
            <span class="max-w-[380px] truncate">{{ $r->judul }}</span>
            <span class="text-white/35">&bull; {{ $r->satuan }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- ============ KATEGORI ============ -->
<section class="max-w-6xl mx-auto px-4 pt-16">
    <div class="flex items-end justify-between gap-4 mb-8 reveal">
        <div>
            <p class="eyebrow mb-2">Kategori Insiden</p>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Jenis Ancaman yang Ditangani</h2>
        </div>
        <a href="{{ route('portal.jelajahi') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-bold text-brand-700 hover:text-brand-800 transition shrink-0">Lihat semua @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-4 h-4'])</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($categories as $c)
        <a href="{{ route('portal.jelajahi', ['category_id' => $c->id]) }}" class="reveal card-lift bg-white rounded-3xl border border-slate-200/80 shadow-card p-5 group" style="--d: {{ $loop->index * 0.05 }}s">
            @include('components.category-icon', ['category' => $c, 'class' => 'w-11 h-11 mb-4'])
            <p class="font-extrabold text-slate-900 group-hover:text-brand-700 transition">{{ $c->nama }}</p>
            <p class="text-xs text-slate-400 font-medium mt-1 line-clamp-2">{{ $c->deskripsi }}</p>
            <p class="text-xs font-extrabold text-brand-600 mt-3">{{ $c->incidents_count }} insiden</p>
        </a>
        @endforeach
    </div>
</section>

<!-- ============ CARA KERJA ============ -->
<section class="max-w-6xl mx-auto px-4 pt-20">
    <div class="text-center max-w-xl mx-auto mb-10 reveal">
        <p class="eyebrow mb-2 justify-center">Alur Penanganan</p>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Tiga Langkah, Insiden Tertangani</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-5">
        @foreach([
            ['01', 'Laporkan', 'Isi formulir insiden: kategori, tingkat keparahan, satuan, lokasi, aset terkait, dan bukti foto. Draft tersimpan otomatis.', 'file'],
            ['02', 'Ditugaskan & Diproses', 'Petugas menindaklanjuti laporan dan menunjuk penanggung jawab. Anda menerima notifikasi setiap ada perubahan status.', 'users'],
            ['03', 'Selesai & Dinilai', 'Insiden ditutup setelah teratasi. Beri penilaian kepuasan 1–5 bintang untuk membantu evaluasi layanan.', 'star'],
        ] as [$no, $judul, $isi, $icon])
        <div class="reveal relative bg-white rounded-3xl border border-slate-200/80 shadow-card p-7 overflow-hidden" style="--d: {{ $loop->index * 0.08 }}s">
            <span class="absolute -top-3 right-4 text-[76px] font-extrabold text-brand-50 select-none leading-none">{{ $no }}</span>
            <span class="relative w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white flex items-center justify-center shadow-glow mb-5">
                @include('components.icon', ['name' => $icon, 'class' => 'w-6 h-6'])
            </span>
            <h3 class="relative font-extrabold text-lg text-slate-900 mb-2">{{ $judul }}</h3>
            <p class="relative text-sm text-slate-500 leading-relaxed">{{ $isi }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- ============ DARURAT + SEBARAN SATUAN ============ -->
<section class="max-w-6xl mx-auto px-4 pt-20 grid lg:grid-cols-5 gap-6 items-start">
    <div class="lg:col-span-3 reveal">
        <div class="bg-ink-950 text-white rounded-[28px] p-7 sm:p-8 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-red-500/20 blur-[80px]"></div>
            <div class="relative flex items-center gap-3 mb-6">
                <span class="w-10 h-10 rounded-xl bg-red-500/20 text-red-300 flex items-center justify-center">@include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5'])</span>
                <div>
                    <h3 class="font-extrabold text-lg leading-tight">Prioritas Darurat</h3>
                    <p class="text-xs text-white/45 font-medium">Insiden berkategori darurat yang tercatat dalam sistem</p>
                </div>
            </div>
            <div class="relative space-y-3">
                @forelse($darurat as $r)
                <a href="{{ route('portal.insiden.show', $r->id) }}" class="flex items-center gap-4 rounded-2xl bg-white/[0.06] border border-white/10 hover:bg-white/[0.1] transition p-4 group">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-sm truncate group-hover:text-cyan-200 transition">{{ $r->judul }}</p>
                        <p class="text-xs text-white/45 mt-1 flex items-center gap-2">
                            <span>{{ $r->category->nama ?? 'Insiden' }}</span>&bull;<span>{{ $r->satuan }}</span>&bull;<span>{{ $r->created_at->diffForHumans() }}</span>
                        </p>
                    </div>
                    @include('components.status-badge', ['status' => $r->status])
                </a>
                @empty
                <p class="text-sm text-white/50 py-4">Tidak ada insiden darurat saat ini. Situasi aman.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="reveal bg-white rounded-[28px] border border-slate-200/80 shadow-card p-7">
            <h3 class="font-extrabold text-slate-900 mb-1">Sebaran per Satuan</h3>
            <p class="text-xs text-slate-400 font-medium mb-5">Satuan dengan laporan insiden terbanyak</p>
            <div class="space-y-4">
                @php $maxSatuan = max($perSatuan->max('total') ?? 1, 1); @endphp
                @forelse($perSatuan as $s)
                <div>
                    <div class="flex justify-between text-[13px] font-bold mb-1.5">
                        <span class="text-slate-700 truncate mr-3">{{ $s->satuan }}</span>
                        <span class="text-brand-700">{{ $s->total }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-cyan-500" style="width: {{ round($s->total / $maxSatuan * 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        <div class="reveal bg-white rounded-[28px] border border-slate-200/80 shadow-card p-7">
            <h3 class="font-extrabold text-slate-900 mb-1">Pelapor Teraktif</h3>
            <p class="text-xs text-slate-400 font-medium mb-5">Apresiasi untuk pelapor paling sigap</p>
            <div class="space-y-3.5">
                @forelse($topPelapor as $p)
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white flex items-center justify-center text-xs font-extrabold shrink-0">{{ strtoupper(substr($p->name, 0, 1)) }}</span>
                    <p class="flex-1 font-bold text-sm text-slate-700 truncate">{{ $p->name }}</p>
                    <span class="text-xs font-extrabold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-full">{{ $p->incidents_count }} laporan</span>
                </div>
                @empty
                <p class="text-sm text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- ============ INSIDEN TERBARU ============ -->
<section class="max-w-6xl mx-auto px-4 pt-20">
    <div class="flex items-end justify-between gap-4 mb-8 reveal">
        <div>
            <p class="eyebrow mb-2">Baru Masuk</p>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Insiden Terbaru</h2>
        </div>
        <a href="{{ route('portal.jelajahi') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-bold text-brand-700 hover:text-brand-800 transition shrink-0">Jelajahi semua @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-4 h-4'])</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($terbaru as $r)
            @include('portal.partials.card', ['r' => $r])
        @endforeach
    </div>
</section>

<!-- ============ CTA ============ -->
<section class="max-w-6xl mx-auto px-4 pt-20">
    <div class="reveal relative overflow-hidden rounded-[28px] bg-gradient-to-br from-brand-700 via-brand-800 to-ink-950 text-white p-8 sm:p-12 text-center">
        <div class="absolute -top-24 left-1/3 w-80 h-80 rounded-full bg-cyan-400/20 blur-[90px]"></div>
        <div class="relative max-w-2xl mx-auto">
            <span class="inline-flex w-14 h-14 rounded-2xl glass items-center justify-center mb-6">
                @include('components.icon', ['name' => 'shield-check', 'class' => 'w-7 h-7'])
            </span>
            <h2 class="text-2xl sm:text-[34px] font-extrabold leading-tight tracking-tight mb-4">Menemukan aktivitas mencurigakan di satuan Anda?</h2>
            <p class="text-white/60 leading-relaxed mb-8">Jangan menunggu sampai menjadi krisis. Laporkan sekarang — setiap laporan tercatat dan ditindaklanjuti sesuai tingkat keparahannya.</p>
            <a href="{{ route('portal.buat') }}" class="btn-white">@include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Buat Laporan Insiden</a>
        </div>
    </div>
</section>
@endsection
