<?php

namespace App\Policies;

use App\Models\User;

final class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.products.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.products.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.products.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.products.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.products.destroy');
    }
}
