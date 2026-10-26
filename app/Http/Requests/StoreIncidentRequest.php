<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'satuan' => ['required', 'string', 'max:255'],
            'lokasi_teks' => ['required', 'string', 'max:255'],
            'prioritas' => ['sometimes', 'in:biasa,penting,darurat'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.max' => 'Ukuran foto bukti maksimal 2MB.',
            'foto.image' => 'File bukti harus berupa gambar.',
            'satuan.required' => 'Nama satuan wajib diisi.',
            'lokasi_teks.required' => 'Lokasi kejadian wajib diisi.',
        ];
    }
}
