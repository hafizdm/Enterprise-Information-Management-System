<?php

namespace App\Console\Commands\Travel;

use App\Models\Spd;
use App\Models\Travel\TravelRequest;
use App\Services\Travel\TravelRequestService;
use Illuminate\Console\Command;

class ReconcileSpdRequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'travel:reconcile-spd-requests {--dry-run : Only display what would be reconciled without creating records} {--limit=100 : Maximum number of records to process}';

    /**
     * The console command description.
     */
    protected $description = 'Reconcile approved SPDs missing from Central Travel Queue';

    /**
     * Execute the console command.
     */
    public function handle(TravelRequestService $service): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $this->info("Scanning for approved SPDs not yet present in Travel Requests...");

        $existingSpdIds = TravelRequest::whereNotNull('spd_id')->pluck('spd_id')->toArray();

        $query = Spd::with('employee', 'project')
            ->where('status', 'approved')
            ->whereNotIn('id', $existingSpdIds)
            ->orderBy('id', 'asc')
            ->limit($limit);

        $missingSpds = $query->get();

        if ($missingSpds->isEmpty()) {
            $this->info("All approved SPDs are already synchronized with Travel Requests. Nothing to reconcile.");
            return Command::SUCCESS;
        }

        $this->warn("Found {$missingSpds->count()} approved SPD(s) missing from Travel Requests.");

        if ($isDryRun) {
            $this->table(
                ['SPD ID', 'SPD Number', 'Employee', 'Destination', 'Departure Date'],
                $missingSpds->map(fn($s) => [
                    $s->id,
                    $s->spd_number,
                    $s->employee?->full_name ?? '-',
                    $s->destination,
                    $s->date_departure?->format('Y-m-d') ?? '-',
                ])
            );
            $this->info("Dry run completed. No records were modified.");
            return Command::SUCCESS;
        }

        $syncedCount = 0;
        foreach ($missingSpds as $spd) {
            $tr = $service->syncFromApprovedSpd($spd);
            if ($tr) {
                $syncedCount++;
                $this->line("Synced SPD #{$spd->spd_number} -> Travel Request #{$tr->request_number}");
            }
        }

        $this->info("Reconciliation complete. {$syncedCount} travel request(s) created.");
        return Command::SUCCESS;
    }
}
