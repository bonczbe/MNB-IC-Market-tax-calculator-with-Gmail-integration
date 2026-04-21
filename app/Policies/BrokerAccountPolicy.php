<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\BrokerAccount;
use App\Models\User;

class BrokerAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BrokerAccount $brokerAccount): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $brokerAccount->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BrokerAccount $brokerAccount): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $brokerAccount->user_id === $user->id;
    }

    public function delete(User $user, BrokerAccount $brokerAccount): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $brokerAccount->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
