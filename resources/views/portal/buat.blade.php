@extends('layouts.portal')
@section('title', 'Buat Laporan Insiden')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-8 reveal visible">
        <p class="eyebrow mb-2">Formulir Laporan Insiden</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Buat <span class="grad-text-dark">Laporan Insiden</span></h1>
        <p class="text-slate-500 text-sm mt-2 leading-relaxed">Ceritakan insiden keamanan siber yang Anda temukan sedetail mungkin. Laporan diteruskan ke petugas dan ditugaskan ke penanggung jawab. Draft formulir tersimpan otomatis di perangkat ini.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
        <p class="font-extrabold mb-1.5">Periksa kembali formulir:</p>
        <ul class="list-disc ml-5 space-y-0.5 font-medium">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form id="form-insiden" action="{{ route('portal.buat.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-[28px] border border-slate-200/80 shadow-card p-6 sm:p-8 space-y-6">
        @csrf

        <div>
            <label for="judul" class="block text-sm font-extrabold text-slate-800 mb-2">Judul Insiden <span class="text-red-500">*</span></label>
            <input id="judul" name="judul" type="text" value="{{ old('judul') }}" maxlength="255" required placeholder="cth: Email phishing mengatasnamakan bank diterima staf"
                class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="category_id" class="block text-sm font-extrabold text-slate-800 mb-2">Kategori <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" required class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
                    <option value="">— Pilih kategori —</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="prioritas" class="block text-sm font-extrabold text-slate-800 mb-2">Tingkat Keparahan</label>
                <select id="prioritas" name="prioritas" class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
                    <option value="biasa" @selected(old('prioritas', 'biasa') === 'biasa')>Biasa — tidak mengganggu operasional</option>
                    <option value="penting" @selected(old('prioritas') === 'penting')>Penting — berpotensi mengganggu</option>
                    <option value="darurat" @selected(old('prioritas') === 'darurat')>Darurat — sedang berlangsung / berdampak luas</option>
                </select>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="satuan" class="block text-sm font-extrabold text-slate-800 mb-2">Satuan <span class="text-red-500">*</span></label>
                <input id="satuan" name="satuan" type="text" value="{{ old('satuan') }}" maxlength="255" required placeholder="cth: Kodim 0824/Jember"
                    class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
            </div>
            <div>
                <label for="lokasi_teks" class="block text-sm font-extrabold text-slate-800 mb-2">Lokasi Kejadian <span class="text-red-500">*</span></label>
                <input id="lokasi_teks" name="lokasi_teks" type="text" value="{{ old('lokasi_teks') }}" maxlength="255" required placeholder="cth: Gedung Administrasi, Lt. 1"
                    class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
            </div>
        </div>

        <div>
            <label for="asset_id" class="block text-sm font-extrabold text-slate-800 mb-2">Aset TI Terkait <span class="text-slate-400 font-semibold">(opsional)</span></label>
            <select id="asset_id" name="asset_id" class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
                <option value="">— Tidak terkait aset tertentu —</option>
                @foreach($assets as $a)
                <option value="{{ $a->id }}" @selected(old('asset_id') == $a->id)>{{ $a->nama }}{{ $a->ip_address ? ' (' . $a->ip_address . ')' : '' }} — {{ $a->lokasi }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-extrabold text-slate-800 mb-2">Deskripsi Insiden <span class="text-red-500">*</span></label>
            <textarea id="deskripsi" name="deskripsi" rows="6" required maxlength="5000" placeholder="Jelaskan kronologi: kapan ditemukan, perangkat/akun yang terdampak, dan langkah yang sudah Anda lakukan."
                class="w-full rounded-xl border-slate-200 bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition resize-y">{{ old('deskripsi') }}</textarea>
            <p class="text-[11px] text-slate-400 font-medium mt-1.5">Jangan sertakan kata sandi atau data rahasia lain di dalam deskripsi.</p>
        </div>

        <div>
            <label class="block text-sm font-extrabold text-slate-800 mb-2">Foto Bukti <span class="text-slate-400 font-semibold">(opsional, maks 2MB)</span></label>
            <label for="foto" class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 hover:border-brand-300 bg-slate-50/60 hover:bg-brand-50/50 transition px-6 py-8 cursor-pointer text-center">
                @include('components.icon', ['name' => 'camera', 'class' => 'w-8 h-8 text-slate-300'])
                <span id="foto-label" class="text-sm font-bold text-slate-500">Klik untuk memilih foto bukti (JPG/PNG/WebP)</span>
                <img id="foto-preview" class="hidden max-h-48 rounded-xl shadow" alt="Pratinjau foto bukti">
            </label>
            <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
            <button type="submit" class="btn-primary justify-center flex-1 sm:flex-none">
                @include('components.icon', ['name' => 'send', 'class' => 'w-4 h-4']) Kirim Laporan Insiden
            </button>
            <p id="draft-status" class="text-xs text-slate-400 font-medium sm:ml-auto">Draft tersimpan otomatis.</p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const KEY = 'sigap_draft_insiden';
    const form = document.getElementById('form-insiden');
    const fields = ['judul', 'category_id', 'prioritas', 'satuan', 'lokasi_teks', 'asset_id', 'deskripsi'];
    const status = document.getElementById('draft-status');

    // Pulihkan draft (hanya jika tidak ada old input dari server).
    try {
        const draft = JSON.parse(localStorage.getItem(KEY) || 'null');
        if (draft) {
            fields.forEach((id) => {
                const el = document.getElementById(id);
                if (el && !el.value && draft[id]) el.value = draft[id];
            });
            if (status) status.textContent = 'Draft sebelumnya dipulihkan.';
        }
    } catch (e) {}

    let timer = null;
    form.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const data = {};
            fields.forEach((id) => {
                const el = document.getElementById(id);
                if (el) data[id] = el.value;
            });
            try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (e) {}
            if (status) status.textContent = 'Draft tersimpan ' + new Date().toLocaleTimeString('id-ID') + '.';
        }, 400);
    });

    form.addEventListener('submit', () => {
        try { localStorage.removeItem(KEY); } catch (e) {}
    });

    // Pratinjau foto
    const foto = document.getElementById('foto');
    const preview = document.getElementById('foto-preview');
    const label = document.getElementById('foto-label');
    foto.addEventListener('change', () => {
        const file = foto.files[0];
        if (!file) return;
        label.textContent = file.name;
        const reader = new FileReader();
        reader.onload = (e) => { preview.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush
