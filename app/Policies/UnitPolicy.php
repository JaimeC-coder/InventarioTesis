<?php

namespace App\Policies;

use App\Models\User;

final class UnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.units.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.units.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.units.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.units.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.units.destroy');
    }
}
