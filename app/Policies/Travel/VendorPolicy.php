<?php

namespace App\Policies\Travel;

use App\Models\Travel\Vendor;
use App\Models\User;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('travel.vendor-manage') || $user->can('travel.manage');
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return $user->can('travel.vendor-manage') || $user->can('travel.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('travel.vendor-manage');
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->can('travel.vendor-manage');
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->can('travel.vendor-manage');
    }

    public function activate(User $user, Vendor $vendor): bool
    {
        return $user->can('travel.vendor-manage');
    }
}
