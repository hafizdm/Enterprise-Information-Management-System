<?php

namespace App\Services\Travel;

use App\Models\Spd;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\TravelRequestPassenger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TravelRequestService
{
    /**
     * Synchronize an approved SPD into a centralized Travel Request (idempotent).
     */
    public function syncFromApprovedSpd(Spd $spd): ?TravelRequest
    {
        if ($spd->status !== 'approved') {
            return null;
        }

        // Idempotency check: if already synchronized, return existing record
        $existing = TravelRequest::where('spd_id', $spd->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($spd) {
            // Re-check inside transaction with lock to avoid race conditions
            $locked = TravelRequest::where('spd_id', $spd->id)->lockForUpdate()->first();
            if ($locked) {
                return $locked;
            }

            $spd->loadMissing('employee', 'project');

            $travelRequest = TravelRequest::create([
                'request_number' => TravelRequest::generateRequestNumber(),
                'source_type' => 'spd',
                'spd_id' => $spd->id,
                'project_id' => $spd->project_id,
                'travel_category' => in_array($spd->travel_type, ['domestic', 'international']) ? $spd->travel_type : 'domestic',
                'status' => 'pending',
                'request_date' => $spd->document_approved_at ? $spd->document_approved_at->toDateString() : now()->toDateString(),
                'departure_date' => $spd->date_departure,
                'departure_time_window' => null,
                'return_date' => $spd->date_return,
                'return_time_window' => null,
                'origin' => $spd->from,
                'destination' => $spd->destination,
                'need_flight' => $spd->transportation === 'plane',
                'need_hotel' => false,
                'need_local_transport' => false,
                'notes' => "Auto-synced from SPD #{$spd->spd_number}. Purpose: {$spd->purpose}",
                'created_by' => $spd->created_by ?: 1,
            ]);

            // Create single passenger from the SPD traveler (employee)
            TravelRequestPassenger::create([
                'travel_request_id' => $travelRequest->id,
                'employee_id' => $spd->employee_id,
                'passenger_name' => $spd->employee?->full_name ?? 'Traveler',
                'id_card_type' => 'ktp',
                'id_card_number' => $spd->employee?->nik,
                'phone_number' => $spd->employee?->phone_number,
                'gender' => in_array($spd->employee?->gender, ['male', 'female']) ? $spd->employee->gender : null,
                'specific_needs' => null,
            ]);

            Log::info("Synced approved SPD #{$spd->id} to TravelRequest #{$travelRequest->request_number}");

            return $travelRequest;
        });
    }

    /**
     * Create a Project Travel Request (multi-passenger, direct from Site HR/GA).
     */
    public function createProjectTravelRequest(array $data, int $userId): TravelRequest
    {
        return DB::transaction(function () use ($data, $userId) {
            $travelRequest = TravelRequest::create([
                'request_number' => TravelRequest::generateRequestNumber(),
                'source_type' => 'project',
                'spd_id' => null,
                'project_id' => $data['project_id'] ?? null,
                'travel_category' => $data['travel_category'] ?? 'domestic',
                'status' => 'pending',
                'request_date' => $data['request_date'] ?? now()->toDateString(),
                'departure_date' => $data['departure_date'],
                'departure_time_window' => $data['departure_time_window'] ?? null,
                'return_date' => $data['return_date'] ?? null,
                'return_time_window' => $data['return_time_window'] ?? null,
                'origin' => $data['origin'],
                'destination' => $data['destination'],
                'need_flight' => !empty($data['need_flight']),
                'need_hotel' => !empty($data['need_hotel']),
                'need_local_transport' => !empty($data['need_local_transport']),
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            if (!empty($data['passengers']) && is_array($data['passengers'])) {
                foreach ($data['passengers'] as $p) {
                    if (empty($p['passenger_name'])) {
                        continue;
                    }

                    TravelRequestPassenger::create([
                        'travel_request_id' => $travelRequest->id,
                        'employee_id' => null, // Manual input - no relation to employees required
                        'passenger_name' => $p['passenger_name'],
                        'id_card_type' => in_array($p['id_card_type'] ?? '', ['ktp', 'passport']) ? $p['id_card_type'] : 'ktp',
                        'id_card_number' => $p['id_card_number'] ?? null,
                        'phone_number' => $p['phone_number'] ?? null,
                        'gender' => !empty($p['gender']) && in_array($p['gender'], ['male', 'female']) ? $p['gender'] : null,
                        'specific_needs' => $p['specific_needs'] ?? null,
                    ]);
                }
            }

            Log::info("Created Project Travel Request #{$travelRequest->request_number} with " . count($data['passengers'] ?? []) . " passenger(s)");

            return $travelRequest;
        });
    }
}
