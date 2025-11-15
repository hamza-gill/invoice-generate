<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Determine if the user can view products (list, fetch, search).
     */
    public function viewAny(User $user)
    {
        // Employees and above can view
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }

    /**
     * Determine if the user can view a specific product.
     */
    public function view(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }

    /**
     * Determine if the user can create/import products.
     */
    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can update products.
     */
    public function update(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can delete products.
     */
    public function delete(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'developer']);
    }

    /**
     * Determine if the user can toggle product active/inactive.
     */
    public function toggleStatus(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can import products via AJAX.
     */
    public function import(User $user)
    {
        return in_array($user->role, ['admin', 'developer', 'manager']);
    }

    /**
     * Determine if the user can perform AJAX searches/fetches.
     */
    public function fetch(User $user)
    {
        // Employees can fetch/search products
        return in_array($user->role, ['admin', 'developer', 'manager', 'employee']);
    }
}
