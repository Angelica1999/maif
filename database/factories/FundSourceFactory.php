<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FundSource>
 */
class FundSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'saa' => fake()->ean13(),
            'proponent' => fake()->postcode, 
            'code_proponent' => fake()->ean8, 
            'alocated_funds' => fake()->randomNumber(5),
            'remember_token' => Str::random(10)
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
