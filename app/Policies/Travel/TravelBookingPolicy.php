<?php

namespace App\Policies\Travel;

use App\Models\Travel\TravelBooking;
use App\Models\User;

class TravelBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('travel.view-queue') ||
            $user->can('travel.manage') ||
            $user->can('travel.final-booking');
    }

    public function view(User $user, TravelBooking $booking): bool
    {
        if ($user->can('travel.manage') || $user->can('travel.final-booking') || $user->can('travel.view-queue')) {
            return true;
        }

        if ($user->can('travel.site-request')) {
            return $booking->travelRequest && $booking->travelRequest->created_by === $user->id;
        }

        return false;
    }

    public function update(User $user, TravelBooking $booking): bool
    {
        return $user->can('travel.manage');
    }
}
