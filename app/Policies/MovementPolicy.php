<?php

namespace App\Policies;

use App\Models\User;

final class MovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.movements.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.movements.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.movements.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.movements.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.movements.destroy');
    }
}
