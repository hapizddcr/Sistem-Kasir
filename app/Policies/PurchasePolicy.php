<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view purchases');
    }

    public function view(User $user, Purchase $purchase): bool
    {
        return $user->can('view purchases');
    }

    public function create(User $user): bool
    {
        return $user->can('create purchases');
    }

    public function receive(User $user, Purchase $purchase): bool
    {
        return $user->can('receive purchases');
    }
}
