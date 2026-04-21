<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\EmailExtract;
use App\Models\User;

class EmailExtractPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EmailExtract $emailExtract): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $emailExtract->broker->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, EmailExtract $emailExtract): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $emailExtract->broker->user_id === $user->id;
    }

    public function delete(User $user, EmailExtract $emailExtract): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $emailExtract->broker->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
