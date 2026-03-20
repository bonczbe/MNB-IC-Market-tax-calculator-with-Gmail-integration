<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct() {}

    public function getAllUserWithBrokers()
    {
        return User::query()->with(['brokers'])->get();
    }
}
