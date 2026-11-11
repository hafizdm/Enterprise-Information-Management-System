<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\Travel\TravelBooking;
use App\Models\Travel\TravelBookingService;
use App\Models\Travel\TravelInquiry;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\VendorQuotationPackage;
use App\Models\Travel\VendorQuotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VendorQuotationController extends Controller
{
    /**
     * Show form to input quotation received from vendor by email.
     */
    public function create(TravelInquiry $inquiry): View
    {
        Gate::authorize('createQuotation', $inquiry);

        $inquiry->load(['travelRequest.passengers', 'vendor']);

        return view('travel.quotations.create', compact('inquiry'));
    }

    /**
     * Store quotation package received from vendor.
     */
    public function store(Request $request, TravelInquiry $inquiry): RedirectResponse
    {
        Gate::authorize('createQuotation', $inquiry);

        $validated = $request->validate([
            'package_name' => 'required|string|max:150',
            'valid_until' => 'required|date|after:now',
            'vendor_notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.service_type' => 'required|in:flight,hotel,train,bus,car_rental,ferry,other',
            'services.*.service_name' => 'required|string|max:200',
            'services.*.service_date' => 'required|date',
            'services.*.service_details' => 'nullable|string',
            'services.*.estimated_cost' => 'required|numeric|min:0',
        ]);

        $package = DB::transaction(function () use ($validated, $inquiry, $request) {
            $totalCost = collect($validated['services'])->sum('estimated_cost');

            $package = VendorQuotationPackage::create([
                'quotation_number' => VendorQuotationPackage::generateQuotationNumber(),
                'travel_inquiry_id' => $inquiry->id,
                'vendor_id' => $inquiry->vendor_id,
                'package_name' => $validated['package_name'],
                'total_estimated_cost' => $totalCost,
                'valid_until' => $validated['valid_until'],
                'status' => 'submitted',
                'vendor_notes' => $validated['vendor_notes'] ?? null,
            ]);

            foreach ($validated['services'] as $svc) {
                VendorQuotationService::create([
                    'vendor_quotation_package_id' => $package->id,
                    'service_type' => $svc['service_type'],
                    'service_name' => $svc['service_name'],
                    'service_date' => $svc['service_date'],
                    'service_details' => $svc['service_details'] ?? null,
                    'estimated_cost' => $svc['estimated_cost'],
                ]);
            }

            // Update inquiry and travel request status to quoted
            $inquiry->update(['status' => 'quoted']);
            $inquiry->travelRequest->update(['status' => 'quoted']);

            return $package;
        });

        return redirect()->route('travel.quotations.review', $inquiry)
            ->with('success', "Penawaran harga {$package->quotation_number} ({$package->package_name}) berhasil disimpan.");
    }

    /**
     * Internal HR/GA review of quotations for an inquiry.
     */
    public function review(TravelInquiry $inquiry): View
    {
        Gate::authorize('view', $inquiry);

        $inquiry->load([
            'travelRequest.passengers',
            'travelRequest.project',
            'vendor',
            'quotations.services',
        ]);

        return view('travel.quotations.review', compact('inquiry'));
    }

    /**
     * Select a quotation package and convert it to a confirmed booking.
     */
    public function select(Request $request, VendorQuotationPackage $package): RedirectResponse
    {
        Gate::authorize('select', $package);

        $booking = DB::transaction(function () use ($package, $request) {
            // 1. Mark selected package
            $package->update([
                'status' => 'selected',
                'selected_at' => now(),
                'selected_by' => $request->user()->id,
                'internal_notes' => $request->input('notes'),
            ]);

            // 2. Reject other packages in this inquiry
            VendorQuotationPackage::where('travel_inquiry_id', $package->travel_inquiry_id)
                ->where('id', '!=', $package->id)
                ->update(['status' => 'rejected']);

            // 3. Update Travel Request status
            $travelRequest = $package->inquiry->travelRequest;
            $travelRequest->update(['status' => 'approved']);

            // 4. Automatically create Travel Booking
            $booking = TravelBooking::create([
                'booking_number' => TravelBooking::generateBookingNumber(),
                'travel_request_id' => $travelRequest->id,
                'vendor_id' => $package->vendor_id,
                'vendor_quotation_package_id' => $package->id,
                'status' => 'confirmed',
                'total_actual_amount' => $package->total_estimated_cost,
                'confirmed_at' => now(),
                'confirmed_by' => $request->user()->id,
                'notes' => "Dikonfirmasi dari pemilihan penawaran {$package->quotation_number} ({$package->package_name})",
            ]);

            // 5. Clone services into booking services
            foreach ($package->services as $svc) {
                TravelBookingService::create([
                    'travel_booking_id' => $booking->id,
                    'vendor_quotation_service_id' => $svc->id,
                    'service_type' => $svc->service_type,
                    'service_name' => $svc->service_name,
                    'booking_reference' => null,
                    'actual_cost' => $svc->estimated_cost,
                    'status' => 'booked',
                ]);
            }

            // Update Travel Request to booked
            $travelRequest->update(['status' => 'booked']);

            return $booking;
        });

        return redirect()->route('travel.requests.show', $package->inquiry->travelRequest)
            ->with('success', "Opsi penawaran {$package->package_name} berhasil dipilih. Booking #{$booking->booking_number} telah dikonfirmasi.");
    }
}
