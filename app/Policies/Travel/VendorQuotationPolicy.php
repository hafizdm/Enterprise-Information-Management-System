<?php

namespace App\Policies\Travel;

use App\Models\Travel\VendorQuotationPackage;
use App\Models\User;

class VendorQuotationPolicy
{
    public function view(User $user, VendorQuotationPackage $package): bool
    {
        return $user->can('travel.review-quotation') ||
            $user->can('travel.select-quotation') ||
            $user->can('travel.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('travel.review-quotation') ||
            $user->can('travel.manage');
    }

    public function select(User $user, VendorQuotationPackage $package): bool
    {
        if ($package->status !== 'submitted') {
            return false;
        }

        return $user->can('travel.select-quotation') ||
            $user->can('travel.review-quotation') ||
            $user->can('travel.manage');
    }
}
