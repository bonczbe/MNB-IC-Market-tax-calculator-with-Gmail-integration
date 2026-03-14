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
            'base_currency' => $this->faker->currencyCode(),
            'unit' => $this->faker->numberBetween(1,100),
            'rate' => $this->faker->randomFloat(2,1,500),
            'for_currency' => $this->faker->currencyCode(),
            'date' => $this->faker->date(),
        ];
    }
}
