<?php

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.users.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.users.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.users.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.users.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.users.destroy');
    }
}
