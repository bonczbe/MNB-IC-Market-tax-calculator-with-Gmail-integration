<?php

namespace Database\Factories;

use App\Models\BrokerAccount;
use App\Models\EmailExtract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailExtract>
 */
class EmailExtractFactory extends Factory
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
            'content' => $this->faker->randomHtml(),
            'broker_account_id' => BrokerAccount::factory(),
        ];
    }
}
