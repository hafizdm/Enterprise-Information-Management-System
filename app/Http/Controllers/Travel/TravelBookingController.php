<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\Travel\TravelBooking;
use App\Models\Travel\TravelBookingService;
use App\Models\Travel\TravelTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TravelBookingController extends Controller
{
    /**
     * Display list of confirmed bookings for internal users.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TravelBooking::class);

        $query = TravelBooking::with(['travelRequest.passengers', 'vendor', 'tickets', 'travelRequest.project'])
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('travel.bookings.index', compact('bookings'));
    }

    /**
     * Display booking detail for internal users.
     */
    public function show(TravelBooking $booking): View
    {
        Gate::authorize('view', $booking);

        $booking->load([
            'travelRequest.passengers',
            'travelRequest.project',
            'vendor',
            'quotationPackage',
            'services.tickets',
            'tickets.uploadedBy',
            'tickets.verifiedBy',
        ]);

        return view('travel.bookings.show', compact('booking'));
    }

    /**
     * Upload E-Ticket / Voucher file received from vendor via email.
     */
    public function uploadTicket(Request $request, TravelBooking $booking): RedirectResponse
    {
        Gate::authorize('upload', [TravelTicket::class, $booking]);

        $validated = $request->validate([
            'ticket_number' => 'required|string|max:100',
            'ticket_type' => 'required|in:flight_eticket,hotel_voucher,train_ticket,other',
            'travel_booking_service_id' => 'nullable|exists:travel_booking_services,id',
            'ticket_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('ticket_file');
        $storedPath = $file->store('travel/tickets', 'local');

        DB::transaction(function () use ($validated, $booking, $file, $storedPath, $request) {
            $ticket = TravelTicket::create([
                'ticket_number' => $validated['ticket_number'],
                'travel_booking_id' => $booking->id,
                'travel_booking_service_id' => $validated['travel_booking_service_id'] ?? null,
                'ticket_type' => $validated['ticket_type'],
                'file_path' => $storedPath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'uploaded_by' => $request->user()->id,
                'uploaded_at' => now(),
                'is_verified' => false,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update booking status to ticket_issued
            $booking->update(['status' => 'ticket_issued']);

            // Update travel request status to ticket_issued
            $booking->travelRequest->update(['status' => 'ticket_issued']);

            // If linked to a booking service, mark that service as ticketed
            if (!empty($validated['travel_booking_service_id'])) {
                TravelBookingService::where('id', $validated['travel_booking_service_id'])
                    ->update(['status' => 'ticketed']);
            }
        });

        return redirect()->route('travel.bookings.show', $booking)
            ->with('success', "Berkas tiket {$file->getClientOriginalName()} berhasil diunggah.");
    }

    /**
     * Verify an uploaded ticket (Internal HR/GA HO).
     */
    public function verifyTicket(Request $request, TravelTicket $ticket): RedirectResponse
    {
        Gate::authorize('verify', $ticket);

        DB::transaction(function () use ($ticket, $request) {
            $ticket->update([
                'is_verified' => true,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'notes' => $request->input('notes'),
            ]);

            // If all tickets for the booking are verified, mark booking as verified
            $booking = $ticket->booking;
            $unverifiedCount = $booking->tickets()->where('is_verified', false)->count();

            if ($unverifiedCount === 0) {
                $booking->update(['status' => 'verified']);
            }
        });

        return redirect()->back()
            ->with('success', "Tiket/Voucher #{$ticket->ticket_number} berhasil diverifikasi.");
    }

    /**
     * Securely download or stream a private ticket file.
     */
    public function downloadTicket(TravelTicket $ticket): StreamedResponse
    {
        Gate::authorize('view', $ticket);

        abort_unless(Storage::disk('local')->exists($ticket->file_path), 404, 'Berkas tiket tidak ditemukan di penyimpanan server.');

        return Storage::disk('local')->download($ticket->file_path, $ticket->file_name);
    }
}
