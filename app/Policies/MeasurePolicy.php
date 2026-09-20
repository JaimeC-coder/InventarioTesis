<?php

namespace App\Policies;

use App\Models\User;

final class MeasurePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.measures.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.measures.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.measures.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.measures.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.measures.destroy');
    }
}
