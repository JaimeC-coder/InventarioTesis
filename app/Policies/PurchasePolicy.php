<?php

namespace App\Policies;

use App\Models\User;

final class PurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.purchases.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.purchases.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.purchases.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.purchases.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.purchases.destroy');
    }
}
