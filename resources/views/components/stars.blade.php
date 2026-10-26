@props(['value' => 0, 'class' => 'w-4 h-4'])
@php $value = (float) $value; @endphp
<span class="inline-flex items-center gap-0.5" title="{{ number_format($value, 1) }} dari 5">
    @for($i = 1; $i <= 5; $i++)
    <svg class="{{ $class }} {{ $i <= round($value) ? 'text-amber-400' : 'text-slate-200' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 2l2.94 6.36 6.96.8-5.16 4.73 1.4 6.86L12 17.27 5.86 20.75l1.4-6.86-5.16-4.73 6.96-.8L12 2z"/>
    </svg>
    @endfor
</span>
