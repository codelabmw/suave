<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerificationCode>
 */
class VerificationCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'verifiable_type' => User::class,
            'verifiable_id' => User::all()->random()->id,
            'code' => $this->faker->numberBetween(100000, 999999),
            'expires_at' => now()->addMinutes(5),
        ];
    }
}
