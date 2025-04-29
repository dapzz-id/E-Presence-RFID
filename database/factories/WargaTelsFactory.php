<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WargaTels>
 */
class WargaTelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis' => $this->faker->unique()->numerify('#########'),
            'name' => fake("id")->name(),
            'kelas' => $this->faker->randomElement([
                'X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4',
                'XI RPL 1', 'XI RPL 2', 'XI RPL 3', ' XI RPL 4',
                'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4',
                'X DKV 1', 'X DKV 2', 'X DKV 3',
                'XI DKV 1', 'XI DKV 2', 'XI DKV 3',
                'XII DKV 1', 'XII DKV 2', 'XII DKV 3',
                'X TKJ 1', 'X TKJ 2', 'X TKJ 3',
                'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3',
                'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3',
                'X TRANSMISI', 'XI TRANSMISI', 'XI TRANSMISI'
            ]),
            'alamat' => fake("id")->address(),
        ];
    }
}
