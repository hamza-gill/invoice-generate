<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users (list users).
     */
    public function viewAny(User $user)
    {
        // Only admin and developer can manage users
        return in_array(strtolower($user->role), ['admin', 'developer']);
    }

    /**
     * Determine if the user can view a specific user.
     */
    public function view(User $user, User $model)
    {
        return in_array(strtolower($user->role), ['admin', 'developer']);
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user)
    {
        // Convert role to lowercase to match stored roles
        return in_array(strtolower($user->role), ['admin', 'developer']);
    }


    /**
     * Determine if the user can update a user.
     */
    public function update(User $user, User $model)
    {
        return in_array(strtolower($user->role), ['admin', 'developer']);
    }

    /**
     * Determine if the user can delete a user.
     */
    public function delete(User $user, User $model)
    {
        // Prevent self-deletion
        if ($user->id === $model->id) {
            return false;
        }
        return in_array(strtolower($user->role), ['admin', 'developer']);
    }

    /**
     * Determine if the user can restore a deleted user.
     */
    public function restore(User $user, User $model)
    {
        return in_array(strtolower($user->role), ['admin', 'developer']);
    }

    /**
     * Determine if the user can permanently delete a user.
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->role === 'admin';
    }
}
