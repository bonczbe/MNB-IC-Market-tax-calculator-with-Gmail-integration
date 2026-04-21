<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\DailyStatus;
use App\Models\User;

class DailyStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DailyStatus $dailyStatus): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $dailyStatus->broker->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, DailyStatus $dailyStatus): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $dailyStatus->broker->user_id === $user->id;
    }

    public function delete(User $user, DailyStatus $dailyStatus): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $dailyStatus->broker->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
