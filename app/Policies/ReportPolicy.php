<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    /**
     * Determine if the user can view reports.
     */
    public function view(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can export reports.
     */
    public function export(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can view financial reports.
     */
    public function viewFinancial(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can view analytics.
     */
    public function viewAnalytics(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }
}
