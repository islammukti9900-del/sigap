{{-- Kolom captcha aritmatika (hanya tampil bila diaktifkan) --}}
@isset($captchaSoal)
<div>
    <label class="block text-sm font-bold text-slate-700 mb-1.5" for="captcha">Verifikasi: berapa hasil {{ $captchaSoal }}?</label>
    <input id="captcha" name="captcha" type="number" inputmode="numeric" required placeholder="Jawaban hitungan"
        class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    @error('captcha')
    <p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>
    @enderror
</div>
@endisset
