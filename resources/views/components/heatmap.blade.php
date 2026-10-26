@props(['heatmap'])
@php
$levelClass = [
    0 => 'bg-slate-100',
    1 => 'bg-cyan-200',
    2 => 'bg-cyan-400',
    3 => 'bg-brand-500',
    4 => 'bg-brand-800',
];
@endphp
<div class="heatmap overflow-x-auto pb-1">
    <div class="flex gap-[3px] min-w-max">
        @foreach($heatmap['minggu'] as $kolom)
        <div class="flex flex-col gap-[3px]">
            @foreach($kolom as $sel)
            <span title="{{ $sel['label'] }}: {{ $sel['jumlah'] }} insiden"
                class="w-[11px] h-[11px] rounded-[3px] {{ $levelClass[$sel['level']] }} ring-1 ring-inset ring-slate-900/5"></span>
            @endforeach
        </div>
        @endforeach
    </div>
    <div class="flex items-center gap-1.5 mt-3 text-[11px] font-semibold text-slate-400">
        <span>Sedikit</span>
        @foreach($levelClass as $kelas)
        <span class="w-[11px] h-[11px] rounded-[3px] {{ $kelas }} ring-1 ring-inset ring-slate-900/5"></span>
        @endforeach
        <span>Banyak</span>
        <span class="ml-3">{{ $heatmap['total'] }} insiden dalam {{ count($heatmap['minggu']) }} minggu terakhir</span>
    </div>
</div>
