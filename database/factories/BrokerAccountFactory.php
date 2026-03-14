<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrokerAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'broker_name' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'email_subject' => $this->faker->sentence(3),
            'account_number' => $this->faker->numerify('########'),
            'starting_balance' => $this->faker->randomFloat(2, 500, 5000),
            'filter_number' => '//td[@class="account-number"]',
            'filter_balance' => '//td[@class="balance"]',
            'broker_currency' => 'USD',
            'user_id' => User::factory(),
        ];
    }
}
