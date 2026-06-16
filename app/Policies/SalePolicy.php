<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view sales');
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->can('view sales');
    }

    public function create(User $user): bool
    {
        return $user->can('create sales');
    }

    public function refund(User $user, Sale $sale): bool
    {
        return $user->can('refund sales');
    }
}
