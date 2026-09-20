<?php

namespace App\Policies;

use App\Models\User;

final class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.suppliers.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.suppliers.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.suppliers.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.suppliers.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.suppliers.destroy');
    }
}
