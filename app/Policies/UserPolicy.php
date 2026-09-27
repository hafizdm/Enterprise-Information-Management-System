<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('user.view');
    }

       /**
     * Determine whether the user can create a user.
     */
    public function create(User $user): bool
    {
        return $user->can('user.create');
    }   

    public function view(User $user, User $model): bool
    {
        return $user->can('user.view');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('user.update');
    }
}