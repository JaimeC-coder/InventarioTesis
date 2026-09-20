<?php

namespace App\Policies;

use App\Models\User;

final class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.categories.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.categories.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.categories.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.categories.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.categories.destroy');
    }
}
