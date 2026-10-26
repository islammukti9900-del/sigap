@php
$meta = \App\Models\Incident::PRIORITIES[$prioritas] ?? \App\Models\Incident::PRIORITIES['biasa'];
$styles = [
    'biasa' => 'bg-slate-100 text-slate-500 ring-slate-200',
    'penting' => 'bg-orange-50 text-orange-700 ring-orange-200',
    'darurat' => 'bg-red-50 text-red-700 ring-red-300',
];
@endphp
@if($prioritas !== 'biasa')
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold ring-1 ring-inset {{ $styles[$prioritas] ?? $styles['biasa'] }} {{ $prioritas === 'darurat' ? 'animate-pulse' : '' }}">
    @include('components.icon', ['name' => 'alert', 'class' => 'w-3 h-3'])
    {{ $meta['label'] }}
</span>
@endif
