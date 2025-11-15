<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebhookSetting;

class WebhookSettingPolicy
{
    public function view(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    public function updateWebhook(User $user)
    {
        return in_array($user->role, ['admin', 'developer']);
    }
}
