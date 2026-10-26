<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Indicator>
 */
class IndicatorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'domain',
            'value' => fake()->unique()->domainName(),
            'threat' => 'phishing',
            'source' => 'Laporan satuan',
            'notes' => null,
            'added_by' => User::factory()->petugas(),
        ];
    }

    public function ip(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'ip',
            'value' => fake()->unique()->ipv4(),
        ]);
    }

    public function hash(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'hash',
            'value' => hash('sha256', fake()->unique()->sentence()),
            'threat' => 'malware',
        ]);
    }
}
