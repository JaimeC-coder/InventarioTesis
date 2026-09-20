<?php

namespace App\Policies;

use App\Models\User;

final class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.sales.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.sales.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.sales.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.sales.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.sales.destroy');
    }
}
