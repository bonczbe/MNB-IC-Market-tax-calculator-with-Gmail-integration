<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === UserRoleEnum::ADMIN && $user->id !== $model->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
