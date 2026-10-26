@extends('layouts.app')
@section('title', 'Detail Insiden')
@section('subtitle', 'Insiden #' . str_pad($incident->id, 5, '0', STR_PAD_LEFT))

@section('content')
<a href="{{ route('insiden.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-brand-800 mb-5 transition">
    @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) Kembali ke daftar insiden
</a>

<div class="grid lg:grid-cols-3 gap-6 items-start">
    <div class="lg:col-span-2 space-y-6">
        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
            @if($incident->foto)
            <img src="{{ asset('storage/' . $incident->foto) }}" alt="" class="w-full max-h-96 object-cover bg-slate-100">
            @endif
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @include('components.priority-badge', ['prioritas' => $incident->prioritas])
                    @include('components.status-badge', ['status' => $incident->status])
                    @include('components.eskalasi-badge', ['incident' => $incident])
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 ml-1">
                        @include('components.category-icon', ['category' => $incident->category, 'class' => 'w-6 h-6'])
                        {{ $incident->category->nama ?? 'Tanpa kategori' }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mb-2">{{ $incident->judul }}</h1>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-[13px] text-slate-400 mb-4">
                    <span>Pelapor: <strong class="text-slate-600">{{ $incident->user->name }}</strong></span>
                    <span>{{ $incident->created_at->format('d M Y, H:i') }}</span>
                    <span class="inline-flex items-center gap-1">@include('components.icon', ['name' => 'chat', 'class' => 'w-3.5 h-3.5']) {{ $incident->comments->count() }} tanggapan</span>
                    <span class="inline-flex items-center gap-1">@include('components.icon', ['name' => 'bell', 'class' => 'w-3.5 h-3.5']) {{ $incident->follows()->count() }} pengikut</span>
                </div>
                <div class="flex flex-wrap gap-x-5 gap-y-1.5 text-[13px] text-slate-400 mb-3">
                    <span class="inline-flex items-center gap-1.5">@include('components.icon', ['name' => 'shield', 'class' => 'w-3.5 h-3.5']) Satuan: <strong class="text-slate-600">{{ $incident->satuan }}</strong></span>
                    <span class="inline-flex items-center gap-1.5">@include('components.icon', ['name' => 'map-pin', 'class' => 'w-3.5 h-3.5']) {{ $incident->lokasi_teks }}</span>
                    <span class="inline-flex items-center gap-1.5">@include('components.icon', ['name' => 'user', 'class' => 'w-3.5 h-3.5']) Penanggung jawab: <strong class="text-slate-600">{{ $incident->assignee->name ?? 'Belum ditugaskan' }}</strong></span>
                    @if($incident->asset)
                    <span class="inline-flex items-center gap-1.5">@include('components.icon', ['name' => 'layers', 'class' => 'w-3.5 h-3.5']) Aset: <strong class="text-slate-600">{{ $incident->asset->nama }}{{ $incident->asset->ip_address ? ' (' . $incident->asset->ip_address . ')' : '' }}</strong></span>
                    @endif
                </div>
                <p class="text-slate-600 leading-relaxed whitespace-pre-line text-[15px]">{{ $incident->deskripsi }}</p>

                @if($hashIndicator = $incident->matchedHashIndicator())
                <div class="mt-5 flex items-start gap-3 rounded-2xl border-2 border-red-300 bg-red-50 px-4 py-3.5">
                    <span class="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0">@include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5'])</span>
                    <div>
                        <p class="font-extrabold text-red-800 text-sm">Bukti digital cocok dengan indikator berbahaya</p>
                        <p class="text-[13px] text-red-700/80 mt-0.5">Hash SHA-256 berkas bukti identik dengan indikator <span class="font-semibold">{{ $hashIndicator->threatLabel() }}</span> yang tercatat di Basis Data Indikator. Tangani berkas dengan prosedur forensik.</p>
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
                        <a href="{{ route('indikator.show', $ind->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-bold hover:bg-red-100 transition">
                            @include('components.icon', ['name' => 'target', 'class' => 'w-3.5 h-3.5'])
                            <span class="font-mono">{{ Str::limit($ind->value, 28) }}</span>
                            <span class="text-red-400 font-semibold">({{ $ind->threatLabel() }})</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100"><h2 class="font-bold text-slate-900">Tanggapan <span class="text-slate-400 font-semibold">({{ $incident->comments->count() }})</span></h2></div>
            <div class="p-5 space-y-4">
                @forelse($incident->comments as $c)
                <div class="flex gap-3">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-[13px] font-extrabold shrink-0 {{ $c->user->isStaff() ? 'bg-gradient-to-br from-brand-500 to-cyan-600' : 'bg-slate-400' }}">
                        {{ strtoupper(substr($c->user->name, 0, 1)) }}
                    </span>
                    <div class="flex-1 bg-slate-50 rounded-2xl rounded-tl-md px-4 py-3 min-w-0 border border-slate-100">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-700">
                            {{ $c->user->name }}
                            @if($c->user->isStaff())
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-brand-100 text-brand-800 uppercase tracking-wide">Petugas</span>
                            @endif
                            @if($c->isInternal())
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 uppercase tracking-wide">Internal</span>
                            @endif
                            <span class="font-medium text-slate-400">{{ $c->created_at->diffForHumans() }}</span>
                            <form action="{{ route('komentar.destroy', $c->id) }}" method="POST" class="ml-auto" onsubmit="event.preventDefault(); askDelete(this, 'Hapus komentar ini?')">
                                @csrf @method('DELETE')
                                <button title="Hapus komentar" class="text-slate-300 hover:text-red-600 transition flex">
                                    @include('components.icon', ['name' => 'trash', 'class' => 'w-3.5 h-3.5'])
                                </button>
                            </form>
                        </div>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $c->isi }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-6">Belum ada tanggapan.</p>
                @endforelse

                <form action="{{ route('insiden.komentar', $incident->id) }}" method="POST" class="flex flex-wrap gap-2 pt-2">
                    @csrf
                    <input type="text" name="isi" required maxlength="1000" placeholder="Tulis tanggapan resmi atau catatan internal..."
                        class="flex-1 min-w-[200px] rounded-xl border-slate-200 border px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
                    <select name="visibility" title="Visibilitas komentar" class="rounded-xl border-slate-200 border px-3 py-2.5 text-sm font-bold bg-white focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
                        <option value="publik">Publik</option>
                        <option value="internal">Internal</option>
                    </select>
                    <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-glow">Kirim</button>
                </form>
                <p class="text-[11px] text-slate-400">Komentar <strong>Internal</strong> hanya terlihat oleh petugas — pelapor tidak dapat melihatnya.</p>
            </div>
        </div>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-4">Kelola Insiden</h2>
            <form action="{{ route('insiden.status', $incident->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">Status Penanganan</label>
                    <select name="status" class="w-full rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
                        @foreach(\App\Models\Incident::STATUSES as $val => $meta)
                        <option value="{{ $val }}" {{ $incident->status === $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="w-full bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white font-bold py-2.5 rounded-xl text-sm transition shadow-glow">Simpan Status</button>
            </form>
            <form action="{{ route('insiden.prioritas', $incident->id) }}" method="POST" class="mt-4 pt-4 border-t border-slate-100">
                @csrf
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">Tingkat Prioritas</label>
                <div class="flex gap-2">
                    <select name="prioritas" class="flex-1 rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
                        @foreach(\App\Models\Incident::PRIORITIES as $val => $meta)
                        <option value="{{ $val }}" {{ $incident->prioritas === $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <button class="bg-ink-900 hover:bg-ink-950 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition">OK</button>
                </div>
            </form>
            <form action="{{ route('insiden.assign', $incident->id) }}" method="POST" class="mt-4 pt-4 border-t border-slate-100">
                @csrf
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">Penanggung Jawab</label>
                <div class="flex gap-2">
                    <select name="assigned_to" class="flex-1 rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
                        <option value="">— Belum ditugaskan —</option>
                        @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ $incident->assigned_to === $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <button class="bg-ink-900 hover:bg-ink-950 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition">Atur</button>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Petugas yang ditugaskan menerima notifikasi otomatis.</p>
            </form>
            <form action="{{ route('insiden.destroy', $incident->id) }}" method="POST" class="mt-3" onsubmit="event.preventDefault(); askDelete(this, 'Hapus insiden ini secara permanen?')">
                @csrf @method('DELETE')
                <button class="w-full inline-flex items-center justify-center gap-1.5 text-red-600 hover:bg-red-50 font-bold py-2.5 rounded-lg text-sm transition">
                    @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) Hapus Insiden
                </button>
            </form>
        </div>

        @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 text-center">
            <h2 class="font-bold text-slate-900 mb-1">Kode QR Insiden</h2>
            <p class="text-xs text-slate-400 mb-4">Pindai untuk membuka halaman insiden ini.</p>
            <div class="inline-block bg-white p-3 rounded-2xl border border-slate-200">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->generate(route('portal.insiden.show', $incident->id)) !!}
            </div>
            <p class="font-mono text-[11px] text-slate-400 mt-3 break-all">{{ route('portal.insiden.show', $incident->id) }}</p>
        </div>
        @endif

        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-3">Bagikan</h2>
            @php $shareText = urlencode($incident->judul . ' — SIGAP ' . route('portal.insiden.show', $incident->id)); @endphp
            <div class="flex gap-2">
                <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg text-sm transition">
                    @include('components.icon', ['name' => 'share', 'class' => 'w-4 h-4']) WhatsApp
                </a>
                <a href="{{ route('portal.insiden.show', $incident->id) }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-lg text-sm transition">
                    @include('components.icon', ['name' => 'eye', 'class' => 'w-4 h-4']) Pratinjau
                </a>
            </div>
        </div>
    </aside>
</div>
@endsection
