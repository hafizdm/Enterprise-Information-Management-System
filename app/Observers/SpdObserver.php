<?php

namespace App\Observers;

use App\Jobs\Travel\SyncApprovedSpdToTravelJob;
use App\Models\Spd;
use Illuminate\Support\Facades\DB;

class SpdObserver
{
    /**
     * Handle the Spd "updated" event.
     */
    public function updated(Spd $spd): void
    {
        if ($spd->wasChanged('status') && $spd->status === 'approved') {
            DB::afterCommit(function () use ($spd) {
                SyncApprovedSpdToTravelJob::dispatch($spd->id);
            });
        }
    }

    /**
     * Handle the Spd "created" event (in case created directly as approved, e.g. import).
     */
    public function created(Spd $spd): void
    {
        if ($spd->status === 'approved') {
            DB::afterCommit(function () use ($spd) {
                SyncApprovedSpdToTravelJob::dispatch($spd->id);
            });
        }
    }
}
