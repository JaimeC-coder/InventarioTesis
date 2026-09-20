<?php

namespace App\Policies;

use App\Models\User;

final class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.customers.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.customers.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.customers.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.customers.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.customers.destroy');
    }
}
