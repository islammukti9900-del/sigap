@extends('layouts.app')
@section('title', 'Kategori Insiden')

@section('content')
@section('subtitle', 'Atur kategori insiden siber')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <p class="text-slate-500 text-sm">Kelola kategori insiden <span class="font-bold text-slate-700">({{ $categories->count() }} kategori)</span></p>
    <button onclick="openModal('modal-create')" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-5 py-2.5 rounded-lg transition">
        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
        Tambah Kategori
    </button>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($categories as $kat)
    <div class="reveal bg-white rounded-2xl border border-slate-200/80 shadow-card p-5 hover:shadow-lift hover:-translate-y-0.5 transition-all">
        <div class="flex items-start justify-between mb-3">
            @include('components.category-icon', ['category' => $kat, 'class' => 'w-12 h-12'])
            <div class="flex gap-1">
                <button onclick="openModal('modal-edit-{{ $kat->id }}')" title="Ubah" class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                </button>
                <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" onsubmit="event.preventDefault(); askDelete(this, 'Hapus kategori {{ $kat->nama }}?')">
                    @csrf @method('DELETE')
                    <button title="Hapus" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </button>
                </form>
            </div>
        </div>
        <h3 class="font-extrabold text-slate-900">{{ $kat->nama }}</h3>
        <p class="text-xs text-slate-500 mt-1 mb-3">{{ $kat->deskripsi ?? '—' }}</p>
        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">{{ $kat->incidents_count }} insiden</span>
    </div>

    <!-- Modal edit -->
    <div id="modal-edit-{{ $kat->id }}" class="fixed inset-0 z-40 hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-stone-950/50 backdrop-blur-sm" onclick="closeModal('modal-edit-{{ $kat->id }}')"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl p-6 sm:p-8 w-full max-w-md cmdk-pop">
            <h3 class="font-extrabold text-slate-900 text-lg mb-5">Ubah Kategori</h3>
            <form action="{{ route('kategori.update', $kat->id) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama</label>
                    <input type="text" name="nama" value="{{ $kat->nama }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-stone-50 focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Ikon</label>
                        <select name="icon" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-stone-50 focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500">
                            @foreach(['shield','shield-check','mail','alert','lock','file','globe','activity','flag','zap','star','users','grid','layers','chart','eye','send','message','chat','bell','search','home','camera','tool','check','info','help'] as $ic)
                            <option value="{{ $ic }}" {{ $kat->icon === $ic ? 'selected' : '' }}>{{ $ic }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Warna</label>
                        <input type="color" name="warna" value="{{ $kat->warna }}" class="w-full h-12 rounded-xl border border-slate-200 bg-stone-50 cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-stone-50 focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500">{{ $kat->deskripsi }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modal-edit-{{ $kat->id }}')" class="flex-1 px-4 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition">Batal</button>
                    <button class="flex-1 px-4 py-3 rounded-xl font-bold text-white bg-brand-700 hover:bg-brand-800 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal create -->
<div id="modal-create" class="fixed inset-0 z-40 hidden items-center justify-center px-4">
    <div class="absolute inset-0 bg-stone-950/50 backdrop-blur-sm" onclick="closeModal('modal-create')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl p-6 sm:p-8 w-full max-w-md cmdk-pop">
        <h3 class="font-extrabold text-slate-900 text-lg mb-5">Tambah Kategori</h3>
        <form action="{{ route('kategori.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="cth: Rekayasa Sosial" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-stone-50 focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Ikon</label>
                    <select name="icon" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-stone-50 focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500">
                        @foreach(['shield','shield-check','mail','alert','lock','file','globe','activity','flag','zap','star','users','grid','layers','chart','eye','send','message','chat','bell','search','home','camera','tool','check','info','help'] as $ic)
                        <option value="{{ $ic }}">{{ $ic }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Warna</label>
                    <input type="color" name="warna" value="#0A739E" class="w-full h-12 rounded-xl border border-slate-200 bg-stone-50 cursor-pointer">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-stone-50 focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modal-create')" class="flex-1 px-4 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition">Batal</button>
                <button class="flex-1 px-4 py-3 rounded-xl font-bold text-white bg-brand-700 hover:bg-brand-800 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { const m = document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeModal(id) { const m = document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('[id^="modal-"]').forEach(m => { m.classList.add('hidden'); m.classList.remove('flex'); }); });
</script>
@endsection
