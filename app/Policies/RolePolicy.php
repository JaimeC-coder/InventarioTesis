<?php

namespace App\Policies;

use App\Models\User;

final class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.roles.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.roles.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.roles.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.roles.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.roles.destroy');
    }
}
