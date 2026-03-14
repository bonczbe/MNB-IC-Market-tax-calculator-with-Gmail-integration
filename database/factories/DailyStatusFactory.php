<?php

namespace Database\Factories;

use App\Models\BrokerAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'balance' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => 'USD',
            'broker_account_id' => BrokerAccount::factory(),
        ];
    }
}
