<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $param = $this->route('insiden') ?? $this->route('incident') ?? $this->route('id');
        $incident = $param instanceof \App\Models\Incident
            ? $param
            : \App\Models\Incident::find($param);

        if (!$incident) {
            return false;
        }

        // Pemilik (selama masih menunggu) atau staff.
        return $user->isStaff()
            || ($incident->user_id === $user->id && $incident->status === 'menunggu');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'asset_id' => ['nullable', 'integer', 'exists:assets,id'],
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'deskripsi' => ['sometimes', 'required', 'string', 'max:5000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'satuan' => ['sometimes', 'required', 'string', 'max:255'],
            'lokasi_teks' => ['sometimes', 'required', 'string', 'max:255'],
            'prioritas' => ['sometimes', 'in:biasa,penting,darurat'],
        ];
    }
}
