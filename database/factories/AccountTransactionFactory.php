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
            'amount' => $this->faker->numberBetween(100,1000),
            'note' => $this->faker->sentence(3),
            'date' => $this->faker->date(),
            'date' => $this->faker->randomElement(['withdrawal','deposit']),
            'broker_account_id' => BrokerAccount::factory(),
        ];
    }
}
