@props(['category' => null, 'class' => 'w-6 h-6'])
@php
$nama = strtolower($category->nama ?? '');
$icon = $category->icon ?? match (true) {
    str_contains($nama, 'phishing') => 'mail',
    str_contains($nama, 'malware') => 'alert',
    str_contains($nama, 'akses') => 'lock',
    str_contains($nama, 'kebocoran') || str_contains($nama, 'data') => 'file',
    str_contains($nama, 'deface') => 'globe',
    str_contains($nama, 'gangguan') || str_contains($nama, 'layanan') => 'activity',
    default => 'shield',
};
$warna = $category->warna ?? '#0A739E';
@endphp
<span class="inline-flex items-center justify-center rounded-lg {{ $class }}" style="background: {{ $warna }}14; color: {{ $warna }};">
    @include('components.icon', ['name' => $icon, 'class' => 'w-1/2 h-1/2'])
</span>
