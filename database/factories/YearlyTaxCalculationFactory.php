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
            'tax_year' => $this->faker->year(),
            'gross_profit' => $this->faker->randomFloat(2, -5000, 20000),
            'loss_carried_forward' => 0,
            'taxable_income' => $this->faker->randomFloat(2, -5000, 20000),
            'tax_amount' => $this->faker->randomFloat(2, 0, 3000),
            'unused_loss' => 0,
        ];
    }
}
