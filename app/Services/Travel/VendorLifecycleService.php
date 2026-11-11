<?php

namespace App\Services\Travel;

use App\Models\Travel\TravelVendorControl;
use App\Models\Travel\Vendor;
use App\Models\Travel\VendorActiveHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorLifecycleService
{
    /**
     * Activate a vendor as the single active booking vendor.
     */
    public function activateVendor(int $targetVendorId, string $effectiveDate, string $reason, int $userId): Vendor
    {
        return DB::transaction(function () use ($targetVendorId, $effectiveDate, $reason, $userId) {
            // Ensure control row exists and lock it
            DB::table('travel_vendor_controls')->updateOrInsert(
                ['id' => 1],
                ['updated_at' => now()]
            );

            $control = DB::table('travel_vendor_controls')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            $targetVendor = Vendor::lockForUpdate()->findOrFail($targetVendorId);

            $currentActiveId = $control?->active_vendor_id;

            if ($currentActiveId === $targetVendorId && $targetVendor->is_active) {
                return $targetVendor;
            }

            // 1. Deactivate old active vendor
            if ($currentActiveId) {
                Vendor::where('id', $currentActiveId)->update(['is_active' => false]);

                VendorActiveHistory::where('vendor_id', $currentActiveId)
                    ->whereNull('deactivated_date')
                    ->update(['deactivated_date' => $effectiveDate]);
            }

            // Also ensure any stray active vendors are deactivated
            Vendor::where('id', '!=', $targetVendorId)->where('is_active', true)->update(['is_active' => false]);

            // 2. Activate target vendor
            $targetVendor->is_active = true;
            $targetVendor->save();

            // 3. Update singleton control row
            DB::table('travel_vendor_controls')->where('id', 1)->update([
                'active_vendor_id' => $targetVendorId,
                'updated_at' => now(),
            ]);

            // 4. Record new history
            VendorActiveHistory::create([
                'vendor_id' => $targetVendorId,
                'effective_date' => $effectiveDate,
                'deactivated_date' => null,
                'changed_by' => $userId,
                'reason' => $reason,
            ]);

            return $targetVendor;
        });
    }

    /**
     * Get the currently active vendor.
     */
    public function getActiveVendor(): ?Vendor
    {
        $control = TravelVendorControl::with('activeVendor')->find(1);

        if ($control && $control->activeVendor && $control->activeVendor->is_active) {
            return $control->activeVendor;
        }

        return Vendor::where('is_active', true)->first();
    }
}
