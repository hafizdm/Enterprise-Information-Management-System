<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\Travel\TravelInquiry;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\TravelVendorControl;
use App\Models\Travel\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TravelInquiryController extends Controller
{
    /**
     * Show form to send an inquiry to the active vendor.
     */
    public function create(Request $request): View|RedirectResponse
    {
        Gate::authorize('create', TravelInquiry::class);

        $travelRequestId = $request->input('travel_request_id');
        $travelRequest = TravelRequest::with(['passengers', 'project', 'spd'])->findOrFail($travelRequestId);

        // Fetch the currently active vendor
        $activeVendorId = TravelVendorControl::find(1)?->active_vendor_id;
        $activeVendor = $activeVendorId ? Vendor::find($activeVendorId) : Vendor::where('is_active', true)->first();

        if (!$activeVendor) {
            return redirect()->route('travel.requests.show', $travelRequest)
                ->with('error', 'Tidak ada Vendor Travel yang aktif saat ini. Silakan aktifkan rekanan vendor terlebih dahulu pada Master Vendor.');
        }

        return view('travel.inquiries.create', compact('travelRequest', 'activeVendor'));
    }

    /**
     * Store and dispatch the inquiry to the vendor.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', TravelInquiry::class);

        $validated = $request->validate([
            'travel_request_id' => 'required|exists:travel_requests,id',
            'vendor_id' => 'required|exists:vendors,id',
            'deadline_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $travelRequest = TravelRequest::findOrFail($validated['travel_request_id']);

        $inquiry = DB::transaction(function () use ($validated, $travelRequest, $request) {
            $inquiry = TravelInquiry::create([
                'inquiry_number' => TravelInquiry::generateInquiryNumber(),
                'travel_request_id' => $travelRequest->id,
                'vendor_id' => $validated['vendor_id'],
                'status' => 'sent',
                'deadline_at' => $validated['deadline_at'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'sent_at' => now(),
                'created_by' => $request->user()->id,
            ]);

            $travelRequest->update([
                'status' => 'in_inquiry',
            ]);

            return $inquiry;
        });

        return redirect()->route('travel.requests.show', $travelRequest)
            ->with('success', "Inquiry {$inquiry->inquiry_number} berhasil dikirim ke vendor.");
    }

    /**
     * Display inquiry details and quotation status.
     */
    public function show(TravelInquiry $inquiry): View
    {
        Gate::authorize('view', $inquiry);

        $inquiry->load([
            'travelRequest.passengers',
            'travelRequest.project',
            'vendor',
            'creator',
            'quotations.services',
        ]);

        return view('travel.inquiries.show', compact('inquiry'));
    }
}
