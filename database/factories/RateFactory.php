<?php

namespace Database\Factories;

use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'base_currency' => 'USD',
            'for_currency' => 'HUF',
            'date' => $this->faker->date(),
            'unit' => 1,
            'rate' => $this->faker->randomFloat(2, 300, 420),
        ];
    }
}
