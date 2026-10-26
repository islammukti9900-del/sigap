<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => 'Server ' . fake()->unique()->bothify('SRV-##'),
            'tipe' => fake()->randomElement(['server', 'workstation', 'jaringan', 'aplikasi', 'lainnya']),
            'ip_address' => fake()->localIpv4(),
            'lokasi' => 'Ruang Server Makodam',
            'status' => 'aktif',
            'deskripsi' => fake()->sentence(),
        ];
    }
}
