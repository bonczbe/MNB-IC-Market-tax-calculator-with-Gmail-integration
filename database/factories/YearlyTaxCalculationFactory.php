<?php

namespace Database\Factories;

use App\Models\BrokerAccount;
use App\Models\YearlyTaxCalculation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<YearlyTaxCalculation>
 */
class YearlyTaxCalculationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'broker_account_id' => BrokerAccount::factory(),
            'tax_year' => $this->faker->year('now'),
            'gross_profit' => $this->faker->numberBetween(-100000,100000),
            'loss_carried_forward' => $this->faker->numberBetween(0,100000),
            'taxable_income' => $this->faker->numberBetween(0,100000),
            'tax_amount' => $this->faker->randomFloat(2,0,1000),
            'unused_loss' => $this->faker->numberBetween(-100000,100000),
        ];
    }
}
