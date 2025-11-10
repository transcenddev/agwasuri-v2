<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'account_type' => fake()->randomElement(['user', 'admin']),
            'barangay' => fake()->streetName(),
            'municipality' => fake()->city(),
            'province' => fake()->state(),
            'total_fishpond_area' => fake()->randomFloat(2, 100, 10000),
            'species_cultured' => fake()->randomElement([['Tilapia', 'Bangus'], ['Shrimp'], ['Crab', 'Tilapia'], null]),
            'water_type' => fake()->randomElement(['Freshwater', 'Brackishwater', 'Saltwater']),
            'api_key' => Str::random(60),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
