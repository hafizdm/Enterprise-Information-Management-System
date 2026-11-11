<?php

namespace App\Policies\Travel;

use App\Models\Travel\TravelInquiry;
use App\Models\User;

class TravelInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('travel.inquiry') ||
            $user->can('travel.manage') ||
            $user->can('travel.view-queue');
    }

    public function view(User $user, TravelInquiry $inquiry): bool
    {
        if ($user->can('travel.manage') || $user->can('travel.inquiry') || $user->can('travel.view-queue')) {
            return true;
        }

        if ($user->can('travel.site-request')) {
            return $inquiry->travelRequest && $inquiry->travelRequest->created_by === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('travel.inquiry') ||
            $user->can('travel.manage');
    }

    public function createQuotation(User $user, TravelInquiry $inquiry): bool
    {
        if (!in_array($inquiry->status, ['sent', 'quoted'])) {
            return false;
        }

        return $user->can('travel.review-quotation') || $user->can('travel.manage');
    }
}
