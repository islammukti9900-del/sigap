<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->pelapor(),
            'category_id' => Category::factory(),
            'judul' => fake()->sentence(6),
            'deskripsi' => fake()->paragraph(),
            'satuan' => 'Kodam V/Brawijaya',
            'lokasi_teks' => 'Gedung Administrasi, Lt. 1',
            'status' => 'menunggu',
            'prioritas' => 'biasa',
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'selesai']);
    }

    public function darurat(): static
    {
        return $this->state(fn (array $attributes) => ['prioritas' => 'darurat']);
    }
}
