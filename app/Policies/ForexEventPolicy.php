<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\ForexEvent;
use App\Models\User;

class ForexEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForexEvent $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function update(User $user, ForexEvent $model): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function delete(User $user, ForexEvent $model): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
