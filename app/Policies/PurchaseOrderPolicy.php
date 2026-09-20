<?php

namespace App\Policies;

use App\Models\User;

final class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.purchases-orders.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.purchases-orders.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.purchases-orders.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.purchases-orders.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.purchases-orders.destroy');
    }
}
