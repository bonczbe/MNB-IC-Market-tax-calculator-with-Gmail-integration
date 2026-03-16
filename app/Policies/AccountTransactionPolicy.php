<?php

namespace App\Policies;

use App\Models\AccountTransaction;
use App\Models\User;

class AccountTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AccountTransaction $accountTransaction): bool
    {
        return $user->role === 'admin' || $accountTransaction->broker->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AccountTransaction $accountTransaction): bool
    {
        return $user->role === 'admin' || $accountTransaction->broker->user_id === $user->id;
    }

    public function delete(User $user, AccountTransaction $accountTransaction): bool
    {
        return $user->role === 'admin' || $accountTransaction->broker->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}
