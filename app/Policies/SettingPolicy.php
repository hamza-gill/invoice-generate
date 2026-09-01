<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    /**
     * Determine if the user can view settings.
     */
    public function view(User $user)
    {
        // Only admin, developer, manager can view settings
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can update organization settings.
     */
    public function updateOrganization(User $user)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    /**
     * Determine if the user can update integration settings.
     */
    public function updateIntegration(User $user)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    /**
     * Determine if the user can update invoice settings.
     */
    public function updateInvoice(User $user)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    /**
     * Determine if the user can update reminder settings.
     */
    public function updateReminder(User $user)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    /**
     * Determine if the user can update password (users can always update their own password)
     */
    public function updatePassword(User $user)
    {
        return true;
    }
}
