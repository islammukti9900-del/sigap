@props(['incident'])
@if($incident->isEscalated())
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-red-600 text-white shadow animate-pulse">
    @include('components.icon', ['name' => 'zap', 'class' => 'w-3 h-3']) Eskalasi
</span>
@endif
