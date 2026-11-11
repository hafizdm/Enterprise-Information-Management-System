<?php

namespace App\Jobs\Travel;

use App\Models\Spd;
use App\Services\Travel\TravelRequestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncApprovedSpdToTravelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(public int $spdId)
    {
    }

    public function handle(TravelRequestService $service): void
    {
        $spd = Spd::with('employee', 'project')->find($this->spdId);

        if (!$spd) {
            Log::warning("SyncApprovedSpdToTravelJob: Spd #{$this->spdId} not found.");
            return;
        }

        if ($spd->status !== 'approved') {
            Log::info("SyncApprovedSpdToTravelJob: Spd #{$this->spdId} status is '{$spd->status}', skipping sync.");
            return;
        }

        $service->syncFromApprovedSpd($spd);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("SyncApprovedSpdToTravelJob failed for Spd #{$this->spdId}: " . $exception->getMessage(), [
            'exception' => $exception,
        ]);
    }
}
