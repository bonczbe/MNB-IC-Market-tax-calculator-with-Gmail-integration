<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Models\YearlyTaxCalculation;

class YearlyTaxCalculationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, YearlyTaxCalculation $yearlyTaxCalculation): bool
    {
        return $user->role === UserRoleEnum::ADMIN || $yearlyTaxCalculation->broker->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function update(User $user, YearlyTaxCalculation $yearlyTaxCalculation): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function delete(User $user, YearlyTaxCalculation $yearlyTaxCalculation): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
