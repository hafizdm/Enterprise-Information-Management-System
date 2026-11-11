<?php

namespace App\Policies\Travel;

use App\Models\Travel\TravelBooking;
use App\Models\Travel\TravelTicket;
use App\Models\User;

class TravelTicketPolicy
{
    public function view(User $user, TravelTicket $ticket): bool
    {
        if ($user->can('travel.manage') || $user->can('travel.verify-ticket') || $user->can('travel.view-queue')) {
            return true;
        }

        if ($user->can('travel.site-request')) {
            return $ticket->booking && $ticket->booking->travelRequest && $ticket->booking->travelRequest->created_by === $user->id;
        }

        return false;
    }

    public function upload(User $user, TravelBooking $booking): bool
    {
        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return false;
        }

        return $user->can('travel.verify-ticket') || $user->can('travel.manage');
    }

    public function verify(User $user, TravelTicket $ticket): bool
    {
        return $user->can('travel.verify-ticket') || $user->can('travel.manage');
    }
}
