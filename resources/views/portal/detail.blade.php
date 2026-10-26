@extends('layouts.portal')
@section('title', Str::limit($incident->judul, 50))
@section('og_type', 'article')
@section('og_title', $incident->judul . ' — SIGAP')
@section('og_description', Str::limit(strip_tags($incident->deskripsi), 160))
@if($incident->foto)
@section('og_image', asset('storage/' . $incident->foto))
@endif

@section('content')
@php
$diikuti = auth()->check() && $incident->isFollowedBy(auth()->user());
@endphp

<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-400 mb-6">
        <a href="{{ route('portal.beranda') }}" class="hover:text-brand-700 transition">Beranda</a>
        @include('components.icon', ['name' => 'chevron-right', 'class' => 'w-3.5 h-3.5'])
        <a href="{{ route('portal.jelajahi') }}" class="hover:text-brand-700 transition">Jelajahi</a>
        @include('components.icon', ['name' => 'chevron-right', 'class' => 'w-3.5 h-3.5'])
        <span class="text-slate-600 truncate max-w-[220px]">{{ Str::limit($incident->judul, 32) }}</span>
    </nav>

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <!-- Konten utama -->
        <div class="lg:col-span-2 space-y-6">
            <article class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
                @if($incident->foto)
                <img src="{{ asset('storage/' . $incident->foto) }}" alt="{{ $incident->judul }}" class="w-full max-h-[420px] object-cover bg-slate-100">
                @endif
                <div class="p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @include('components.priority-badge', ['prioritas' => $incident->prioritas])
                        @include('components.status-badge', ['status' => $incident->status])
                        @include('components.eskalasi-badge', ['incident' => $incident])
                        <a href="{{ route('portal.jelajahi', ['category_id' => $incident->category_id]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-700 transition ml-1">
                            @include('components.category-icon', ['category' => $incident->category, 'class' => 'w-6 h-6'])
                            {{ $incident->category->nama ?? 'Tanpa kategori' }}
                        </a>
                    </div>

                    <h1 class="text-2xl sm:text-[32px] font-extrabold text-ink-900 tracking-tight leading-tight mb-3">{{ $incident->judul }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[13px] text-slate-400 mb-5">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-6 h-6 rounded-lg bg-gradient-to-br from-brand-500 to-cyan-600 text-white flex items-center justify-center text-[10px] font-extrabold">{{ strtoupper(substr($incident->user->name ?? '?', 0, 1)) }}</span>
                            <span class="font-bold text-slate-600">{{ $incident->user->name }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1">@include('components.icon', ['name' => 'calendar', 'class' => 'w-3.5 h-3.5']) {{ $incident->created_at->format('d M Y, H:i') }}</span>
                        <span class="inline-flex items-center gap-1">@include('components.icon', ['name' => 'map-pin', 'class' => 'w-3.5 h-3.5']) {{ Str::limit($incident->lokasi_teks, 48) }}</span>
                    </div>

                    <div class="prose-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $incident->deskripsi }}</div>

                    @if($hashIndicator = $incident->matchedHashIndicator())
                    <div class="mt-5 flex items-start gap-3 rounded-2xl border-2 border-red-300 bg-red-50 px-4 py-3.5">
                        <span class="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0">@include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5'])</span>
                        <div>
                            <p class="font-extrabold text-red-800 text-sm">Bukti digital cocok dengan indikator berbahaya</p>
                            <p class="text-[13px] text-red-700/80 mt-0.5">Hash berkas bukti identik dengan indikator berbahaya yang tercatat di SIGAP. Jangan membuka atau menyebarkan berkas tersebut.</p>
                        </div>
                    </div>
                    @endif

                    @if($incident->evidence_hash)
                    <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-1">Hash Bukti Digital (SHA-256)</p>
                        <p class="font-mono text-[13px] font-semibold text-slate-700 break-all" title="{{ $incident->evidence_hash }}">{{ Str::limit($incident->evidence_hash, 32, '…') }}</p>
                    </div>
                    @endif

                    @if($incident->indicators->isNotEmpty())
                    <div class="mt-5">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2">Indikator Terkait</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($incident->indicators as $ind)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-bold">
                                @include('components.icon', ['name' => 'target', 'class' => 'w-3.5 h-3.5'])
                                <span class="font-mono">{{ Str::limit($ind->value, 28) }}</span>
                                <span class="text-red-400 font-semibold">({{ $ind->threatLabel() }})</span>
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Aksi -->
                    <div class="mt-7 pt-6 border-t border-slate-100">
                        {{-- Rating kepuasan: hanya pemilik, hanya jika selesai --}}
                        @if($incident->status === 'selesai' && auth()->check() && auth()->id() === $incident->user_id)
                        <div class="mb-5 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 p-5 shadow-card">
                            @if($incident->rating)
                            <p class="text-[13px] font-bold text-amber-900 mb-2">Penilaian Anda:</p>
                            <div class="flex items-center gap-2">
                                @include('components.stars', ['value' => $incident->rating, 'class' => 'w-6 h-6'])
                                <span class="text-sm font-extrabold text-amber-800">{{ $incident->rating }}/5</span>
                            </div>
                            <p class="text-xs text-amber-700/70 mt-1.5">Terima kasih! Anda bisa mengubah penilaian kapan saja.</p>
                            @else
                            <p class="text-[13px] font-bold text-amber-900 mb-1.5">Insiden selesai! Puas dengan penanganannya?</p>
                            <p class="text-xs text-amber-700/70 mb-3">Beri nilai 1–5 bintang sebagai Indeks Kepuasan penanganan insiden.</p>
                            @endif
                            <form action="{{ route('portal.nilai', $incident->id) }}" method="POST" class="flex items-center gap-1.5" id="form-rating">
                                @csrf
                                @for($i = 1; $i <= 5; $i++)
                                <button type="submit" name="rating" value="{{ $i }}" title="{{ $i }} bintang"
                                    class="star-btn p-0.5 transition-transform hover:scale-125 {{ $incident->rating && $i <= $incident->rating ? 'text-amber-400' : 'text-amber-200 hover:text-amber-300' }}">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.94 6.36 6.96.8-5.16 4.73 1.4 6.86L12 17.27 5.86 20.75l1.4-6.86-5.16-4.73 6.96-.8L12 2z"/></svg>
                                </button>
                                @endfor
                            </form>
                        </div>
                        @elseif($incident->rating)
                        <div class="mb-5 flex items-center gap-2.5 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                            <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Kepuasan pelapor:</span>
                            @include('components.stars', ['value' => $incident->rating, 'class' => 'w-5 h-5'])
                            <span class="text-sm font-extrabold text-slate-700">{{ $incident->rating }}/5</span>
                        </div>
                        @endif
                        <div class="flex flex-wrap items-center gap-3">
                            @auth
                            <form action="{{ route('portal.ikuti', $incident->id) }}" method="POST">
                                @csrf
                                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition {{ $diikuti ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    @include('components.icon', ['name' => 'bell', 'class' => 'w-4 h-4'])
                                    {{ $diikuti ? 'Mengikuti' : 'Ikuti' }} &middot; {{ $incident->follows()->count() }}
                                </button>
                            </form>
                            @else
                            <a href="{{ route('portal.masuk') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                                @include('components.icon', ['name' => 'bell', 'class' => 'w-4 h-4']) Ikuti perkembangan
                            </a>
                            @endauth
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @php
                            $shareText = urlencode($incident->judul . ' — SIGAP ' . url()->current());
                            $shareUrl = urlencode(url()->current());
                            @endphp
                            <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                                @include('components.icon', ['name' => 'share', 'class' => 'w-3.5 h-3.5']) WhatsApp
                            </a>
                            <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 transition">
                                @include('components.icon', ['name' => 'send', 'class' => 'w-3.5 h-3.5']) Telegram
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ $shareText }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                @include('components.icon', ['name' => 'share', 'class' => 'w-3.5 h-3.5']) X
                            </a>
                            <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); this.querySelector('span').textContent='Tersalin!'; setTimeout(()=>this.querySelector('span').textContent='Salin tautan',1600)" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                @include('components.icon', ['name' => 'file', 'class' => 'w-3.5 h-3.5']) <span>Salin tautan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Diskusi -->
            <section class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-50 to-cyan-50 border border-brand-100 text-brand-600 flex items-center justify-center">@include('components.icon', ['name' => 'chat', 'class' => 'w-4 h-4'])</span>
                    <h2 class="font-bold text-slate-900">Diskusi <span class="text-slate-400 font-semibold">({{ $incident->comments->count() }})</span></h2>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($incident->comments as $c)
                    <div class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-[13px] font-extrabold shrink-0 {{ $c->user->isStaff() ? 'bg-gradient-to-br from-brand-500 to-cyan-600' : 'bg-slate-400' }}">
                            {{ strtoupper(substr($c->user->name, 0, 1)) }}
                        </span>
                        <div class="flex-1 bg-slate-50 rounded-2xl rounded-tl-md px-4 py-3 min-w-0 border border-slate-100">
                            <p class="text-xs font-bold text-slate-700 flex flex-wrap items-center gap-2">
                                {{ $c->user->name }}
                                @if($c->user->isStaff())
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-brand-100 text-brand-800 uppercase tracking-wide">Petugas</span>
                                @endif
                                <span class="font-medium text-slate-400">{{ $c->created_at->diffForHumans() }}</span>
                            </p>
                            <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $c->isi }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 text-center py-6">Belum ada diskusi. Jadilah yang pertama berkomentar.</p>
                    @endforelse

                    @auth
                    <form action="{{ route('portal.komentar', $incident->id) }}" method="POST" class="flex gap-2 pt-2">
                        @csrf
                        <input type="text" name="isi" required maxlength="1000" placeholder="Tulis komentar yang sopan&hellip;"
                            class="flex-1 rounded-xl border-slate-200 border px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-slate-50 focus:bg-white transition">
                        <button class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-glow">
                            @include('components.icon', ['name' => 'send', 'class' => 'w-4 h-4']) Kirim
                        </button>
                    </form>
                    @else
                    <p class="text-sm text-slate-400 text-center pt-2"><a href="{{ route('portal.masuk') }}" class="font-bold text-brand-700">Masuk</a> untuk ikut berdiskusi.</p>
                    @endauth
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6 lg:sticky lg:top-24">
            @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
            <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 text-center">
                <h2 class="font-bold text-slate-900 mb-1">Kode QR Insiden</h2>
                <p class="text-xs text-slate-400 mb-4">Pindai untuk membuka halaman ini.</p>
                <div class="inline-block bg-white p-3 rounded-2xl border border-slate-200">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->margin(1)->generate(route('portal.insiden.show', $incident->id)) !!}
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
                <h2 class="font-bold text-slate-900 mb-4">Perjalanan Insiden</h2>
                @php
                $steps = [
                    ['key' => 'menunggu', 'label' => 'Insiden diterima', 'desc' => 'Menunggu verifikasi petugas'],
                    ['key' => 'diproses', 'label' => 'Sedang ditangani', 'desc' => 'Petugas menindaklanjuti'],
                    ['key' => 'selesai', 'label' => 'Selesai', 'desc' => 'Masalah telah ditangani'],
                ];
                $order = ['menunggu' => 0, 'diproses' => 1, 'selesai' => 2];
                $current = $order[$incident->status] ?? 0;
                @endphp
                <div>
                    @foreach($steps as $i => $step)
                    <div class="flex gap-3.5">
                        <div class="flex flex-col items-center">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $current >= $i ? 'bg-gradient-to-br from-brand-500 to-cyan-600 text-white shadow-glow' : 'bg-slate-100 text-slate-400' }}">
                                @if($current >= $i)
                                @include('components.icon', ['name' => 'check', 'class' => 'w-4 h-4'])
                                @else
                                <span class="text-xs font-bold">{{ $i + 1 }}</span>
                                @endif
                            </span>
                            @if(!$loop->last)<span class="w-0.5 flex-1 min-h-[28px] {{ $current > $i ? 'bg-gradient-to-b from-brand-500 to-cyan-500' : 'bg-slate-100' }}"></span>@endif
                        </div>
                        <div class="pb-6">
                            <p class="text-sm font-bold {{ $current >= $i ? 'text-slate-900' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($incident->status === 'ditolak')
                <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-[13px] text-red-700 font-medium">Insiden ini ditolak oleh petugas karena tidak memenuhi kriteria.</div>
                @endif
            </div>

            <!-- Info -->
            <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
                <h2 class="font-bold text-slate-900 mb-4">Informasi Insiden</h2>
                <dl class="space-y-3 text-[13px]">
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Nomor insiden</dt><dd class="font-bold text-slate-800">#{{ str_pad($incident->id, 5, '0', STR_PAD_LEFT) }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Dilaporkan</dt><dd class="font-semibold text-slate-700 text-right">{{ $incident->created_at->format('d M Y') }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Terakhir update</dt><dd class="font-semibold text-slate-700 text-right">{{ $incident->updated_at->diffForHumans() }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Satuan</dt><dd class="font-semibold text-slate-700 text-right">{{ $incident->satuan }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Penanggung jawab</dt><dd class="font-semibold text-slate-700 text-right">{{ $incident->assignee->name ?? 'Belum ditugaskan' }}</dd></div>
                    @if($incident->asset)
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Aset terkait</dt><dd class="font-semibold text-slate-700 text-right">{{ $incident->asset->nama }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Pengikut</dt><dd class="font-semibold text-slate-700">{{ $incident->follows()->count() }} pelapor</dd></div>
                    @if($incident->rating)
                    <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Kepuasan</dt><dd>@include('components.stars', ['value' => $incident->rating])</dd></div>
                    @endif
                </dl>
            </div>

            @if($terkait->isNotEmpty())
            <div class="reveal">
                <h2 class="font-bold text-slate-900 mb-3">Insiden Terkait</h2>
                <div class="space-y-3">
                    @foreach($terkait as $t)
                    <a href="{{ route('portal.insiden.show', $t->id) }}" class="card-lift block bg-white rounded-2xl border border-slate-200/80 shadow-card p-4">
                        <p class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug">{{ $t->judul }}</p>
                        <div class="flex items-center justify-between mt-2.5">
                            @include('components.status-badge', ['status' => $t->status])
                            <span class="inline-flex items-center gap-1 text-xs text-slate-400">@include('components.icon', ['name' => 'chat', 'class' => 'w-3.5 h-3.5']) {{ $t->comments_count ?? $t->comments()->count() }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </aside>
    </div>
</div>
@endsection
