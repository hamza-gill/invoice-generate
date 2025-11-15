<?php

namespace App\Policies;

use App\Models\User;

class SystemPolicy
{
    public function viewRecords(User $user)
    {
        return $user->can('view-records');
    }

    public function manageRecords(User $user)
    {
        return $user->can('manage-records');
    }

    public function manageSystem(User $user)
    {
        return $user->can('manage-system');
    }
}
