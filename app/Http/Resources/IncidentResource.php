<?php

namespace App\Http\Resources;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'foto_url' => $this->foto ? url('storage/' . $this->foto) : null,
            'satuan' => $this->satuan,
            'lokasi_teks' => $this->lokasi_teks,
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'prioritas' => $this->prioritas,
            'prioritas_label' => Incident::PRIORITIES[$this->prioritas]['label'] ?? $this->prioritas,
            'comments_count' => $this->comments_count ?? $this->comments()->count(),
            'follows_count' => $this->follows()->count(),
            'rating' => $this->rating,
            'evidence_hash' => $this->evidence_hash,
            'eskalasi' => $this->isEscalated(),
            'indikator' => $this->whenLoaded('indicators', fn () => $this->indicators->map(fn ($i) => [
                'id' => $i->id,
                'type' => $i->type,
                'value' => $i->value,
                'threat' => $i->threat,
            ])),
            'kategori' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'nama' => $this->category->nama,
                'icon' => $this->category->icon,
                'warna' => $this->category->warna,
            ] : null),
            'aset' => $this->whenLoaded('asset', fn () => $this->asset ? [
                'id' => $this->asset->id,
                'nama' => $this->asset->nama,
                'tipe' => $this->asset->tipe,
                'ip_address' => $this->asset->ip_address,
                'lokasi' => $this->asset->lokasi,
                'status' => $this->asset->status,
            ] : null),
            'pelapor' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'penanggung_jawab' => $this->whenLoaded('assignee', fn () => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
            ] : null),
            'komentar' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
