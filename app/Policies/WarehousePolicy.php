<?php

namespace App\Policies;

use App\Models\User;

final class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.warehouses.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.warehouses.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.warehouses.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.warehouses.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.warehouses.destroy');
    }
}
