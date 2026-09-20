<?php

namespace App\Policies;

use App\Models\User;

final class TransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.transfers.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.transfers.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.transfers.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.transfers.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.transfers.destroy');
    }
}
