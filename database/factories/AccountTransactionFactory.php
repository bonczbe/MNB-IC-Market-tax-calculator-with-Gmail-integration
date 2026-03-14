<?php

namespace Database\Factories;

use App\Models\AccountTransaction;
use App\Models\BrokerAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountTransaction>
 */
class AccountTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'type' => $this->faker->randomElement(['deposit', 'withdrawal']),
            'broker_account_id' => BrokerAccount::factory(),
        ];
    }
}
