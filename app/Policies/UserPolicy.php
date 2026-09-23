<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the admin accounts directory.
     */
    public function viewAnyAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can view the account details.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can create accounts.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can reset the password for another account.
     */
    public function resetPassword(User $user, User $model): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can delete an account (forbidden by design).
     */
    public function delete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can deactivate an account (forbidden by design).
     */
    public function deactivate(User $user, User $model): bool
    {
        return false;
    }
}
