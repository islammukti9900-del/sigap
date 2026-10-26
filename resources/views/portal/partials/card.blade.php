<a href="{{ route('portal.insiden.show', $r->id) }}" class="reveal card-lift group bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-card flex flex-col">
    <div class="relative h-44 bg-slate-100 overflow-hidden">
        @if($r->foto)
        <img src="{{ asset('storage/' . $r->foto) }}" alt="{{ $r->judul }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.06] transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950/45 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        @else
        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-50 via-cyan-50 to-brand-100 text-brand-300">
            @include('components.icon', ['name' => 'file', 'class' => 'w-10 h-10'])
        </div>
        @endif
        <div class="absolute top-3 left-3 flex gap-1.5">
            @include('components.priority-badge', ['prioritas' => $r->prioritas])
            @include('components.status-badge', ['status' => $r->status])
        </div>
        <span class="absolute bottom-3 right-3 w-9 h-9 rounded-full bg-white/95 backdrop-blur flex items-center justify-center text-brand-700 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 shadow">
            @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-4 h-4'])
        </span>
    </div>
    <div class="p-5 flex flex-col flex-1">
        <p class="flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-[0.1em] text-brand-600 mb-2">
            @include('components.category-icon', ['category' => $r->category, 'class' => 'w-5 h-5'])
            {{ $r->category->nama ?? 'Insiden' }}
        </p>
        <h3 class="font-extrabold text-slate-900 text-[15px] leading-snug line-clamp-2 group-hover:text-brand-700 transition mb-2">{{ $r->judul }}</h3>
        <div class="mb-2">@include('components.eskalasi-badge', ['incident' => $r])</div>
        <p class="flex items-center gap-1.5 text-xs text-slate-400 mb-4 font-medium">
            @include('components.icon', ['name' => 'map-pin', 'class' => 'w-3.5 h-3.5 shrink-0'])
            <span class="truncate">{{ Str::limit($r->lokasi_teks, 42) }}</span>
        </p>
        <div class="mt-auto flex items-center justify-between pt-4 border-t border-slate-100 text-xs text-slate-500">
            <span class="inline-flex items-center gap-2">
                <span class="w-7 h-7 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white flex items-center justify-center text-[10px] font-extrabold">{{ strtoupper(substr($r->user->name ?? '?', 0, 1)) }}</span>
                <span class="font-bold truncate max-w-[90px]">{{ $r->user->name ?? '-' }}</span>
            </span>
            <span class="inline-flex items-center gap-2.5 font-bold">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-brand-50 text-brand-700 max-w-[130px]">@include('components.icon', ['name' => 'shield', 'class' => 'w-3.5 h-3.5 shrink-0']) <span class="truncate">{{ Str::limit($r->satuan, 16) }}</span></span>
                <span class="inline-flex items-center gap-1 text-slate-400">@include('components.icon', ['name' => 'chat', 'class' => 'w-3.5 h-3.5']) {{ $r->comments_count ?? $r->comments()->count() }}</span>
            </span>
        </div>
    </div>
</a>
