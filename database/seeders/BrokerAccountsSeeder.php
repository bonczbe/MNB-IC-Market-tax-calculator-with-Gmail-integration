<?php

namespace Database\Seeders;

use App\Models\BrokerAccount;
use Illuminate\Database\Seeder;

class BrokerAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BrokerAccount::create([
            'broker_name' => 'IC Markets',
            'user_id' => 1,
            'email' => 'support@icmarkets.eu',
            'email_subject' => 'Daily Confirmation',
            'account_number' => env('IC_MARKET_ACCOUNT_NUMBER', '1234'),
            'starting_balance' => '3000.00',
            'filter_number' => '//b[text()="'.env('IC_MARKET_ACCOUNT_NUMBER', '1234').'"]',
            'filter_balance' => '//tr[td[normalize-space()="Balance:"]]/td[@class="mspt"][last()]',
            'broker_currency' => config('tax.base_broker_currency'),
            'filter_date' => '//tr[@align="left"][1]/td[@align="right"]/b',
        ]);
    }
}
