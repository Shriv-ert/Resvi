<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    /**
     * Determine whether the user can view the facilities list in admin.
     */
    public function viewAnyAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can view the facility detail in admin.
     */
    public function view(User $user, Facility $facility): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can create a facility.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can update the facility master data.
     */
    public function update(User $user, Facility $facility): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can deactivate the facility.
     */
    public function deactivate(User $user, Facility $facility): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can reactivate the facility.
     */
    public function reactivate(User $user, Facility $facility): bool
    {
        return $user->role === UserRole::Admin;
    }
}
