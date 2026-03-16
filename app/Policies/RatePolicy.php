<?php

namespace App\Policies;

use App\Models\Rate;
use App\Models\User;

class RatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Rate $rate): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Rate $rate): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Rate $rate): bool
    {
        return $user->role === 'admin';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}
