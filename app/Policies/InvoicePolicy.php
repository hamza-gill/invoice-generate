<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;

class InvoicePolicy
{
    public function view(User $user, ?Invoice $invoice = null)
    {
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    public function update(User $user, ?Invoice $invoice = null)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    public function delete(User $user, ?Invoice $invoice = null)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    public function download(User $user, ?Invoice $invoice = null)
    {
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }

    public function send(User $user, ?Invoice $invoice = null)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    public function void(User $user, ?Invoice $invoice = null)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    public function reports(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }
}
