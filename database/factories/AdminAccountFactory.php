<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminAccount>
 */
class AdminAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => "adminproject",
            'email' => fake()->safeEmail(),
            'name' => fake()->name(),
            'password' => Hash::make('password')
        ];
    }
}
