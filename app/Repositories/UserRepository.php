<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct() {}

    public function getAllUser()
    {
        return User::query()->get();
    }
}
