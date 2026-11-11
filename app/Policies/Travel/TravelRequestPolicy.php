<?php

namespace App\Policies\Travel;

use App\Models\Travel\TravelRequest;
use App\Models\User;

class TravelRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('travel.view-queue') ||
            $user->can('travel.site-request') ||
            $user->can('travel.manage');
    }

    public function view(User $user, TravelRequest $travelRequest): bool
    {
        if ($user->can('travel.manage') || $user->can('travel.view-queue')) {
            return true;
        }

        if ($user->can('travel.site-request')) {
            return $travelRequest->created_by === $user->id ||
                ($user->employee && $user->employee->project_id === $travelRequest->project_id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('travel.site-request') ||
            $user->can('travel.manage');
    }

    public function update(User $user, TravelRequest $travelRequest): bool
    {
        if ($user->can('travel.manage')) {
            return in_array($travelRequest->status, ['pending', 'in_inquiry']);
        }

        if ($user->can('travel.site-request') && $travelRequest->created_by === $user->id) {
            return $travelRequest->status === 'pending';
        }

        return false;
    }

    public function cancel(User $user, TravelRequest $travelRequest): bool
    {
        return $user->can('travel.manage') &&
            !in_array($travelRequest->status, ['completed', 'cancelled']);
    }
}
