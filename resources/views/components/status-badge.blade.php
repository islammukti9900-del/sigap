@php
$meta = \App\Models\Incident::STATUSES[$status] ?? \App\Models\Incident::STATUSES['menunggu'];
$styles = [
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
    'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
    'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'red' => 'bg-red-50 text-red-700 ring-red-200',
];
$dot = [
    'amber' => 'bg-amber-500',
    'sky' => 'bg-sky-500',
    'emerald' => 'bg-emerald-500',
    'red' => 'bg-red-500',
];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold ring-1 ring-inset {{ $styles[$meta['color']] ?? $styles['amber'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $dot[$meta['color']] ?? $dot['amber'] }}"></span>
    {{ $meta['label'] }}
</span>
