<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;

class CustomerPolicy
{
    /**
     * Determine if the user can view any customer (index/search).
     */
    public function viewAny(User $user)
    {
        // Employees and above can view
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }

    /**
     * Determine if the user can view a specific customer.
     */
    public function view(User $user, Customer $customer)
    {
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }

    /**
     * Determine if the user can create a customer.
     */
    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can update a customer.
     */
    public function update(User $user, Customer $customer)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can delete a customer.
     */
    public function delete(User $user, Customer $customer)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    /**
     * Determine if the user can import customers via CSV.
     */
    public function import(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can perform search/fetch operations (AJAX).
     */
    public function search(User $user)
    {

        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }
}
