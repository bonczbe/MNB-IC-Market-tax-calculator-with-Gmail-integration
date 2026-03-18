<?php

namespace App\Policies;

use App\Models\Holyday;
use App\Models\User;

class HolydayPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Holyday $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Holyday $model): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Holyday $model): bool
    {
        return $user->role === 'admin';
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}
