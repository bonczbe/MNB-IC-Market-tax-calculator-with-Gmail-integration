<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@a.a',
            'password' => bcrypt('password'),
            'role' => UserRoleEnum::ADMIN,
        ]);

        $this->call([
            BrokerAccountsSeeder::class,
        ]);

    }
}
