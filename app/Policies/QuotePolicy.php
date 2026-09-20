<?php

namespace App\Policies;

use App\Models\User;

final class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('admin.quotes.index');
    }

    public function view(User $user): bool
    {
        return $user->can('admin.quotes.show');
    }

    public function create(User $user): bool
    {
        return $user->can('admin.quotes.create');
    }

    public function update(User $user): bool
    {
        return $user->can('admin.quotes.edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('admin.quotes.destroy');
    }
}
