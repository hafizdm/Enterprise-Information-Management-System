<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Travel\TravelRequest;
use App\Services\Travel\TravelRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TravelRequestController extends Controller
{
    public function __construct(protected TravelRequestService $service)
    {
    }

    /**
     * Display the Central Travel Queue (for HR/GA HO and Site HR/GA).
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TravelRequest::class);

        $query = TravelRequest::with(['spd.employee', 'project', 'creator', 'passengers'])
            ->latest('id');

        // Site HR/GA views requests from their project or created by them
        $user = $request->user();
        if (!$user->can('travel.manage') && !$user->hasRole('HRD')) {
            if ($user->can('travel.site-request') || $user->hasRole('HR/GA Site')) {
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                    if ($user->employee && $user->employee->project_id) {
                        $q->orWhere('project_id', $user->employee->project_id);
                    }
                });
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->input('source_type'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'LIKE', "%{$search}%")
                    ->orWhere('origin', 'LIKE', "%{$search}%")
                    ->orWhere('destination', 'LIKE', "%{$search}%")
                    ->orWhereHas('passengers', function ($p) use ($search) {
                        $p->where('passenger_name', 'LIKE', "%{$search}%")
                            ->orWhere('id_card_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        $travelRequests = $query->paginate(15)->withQueryString();
        $projects = Project::orderBy('name')->get();

        return view('travel.requests.index', compact('travelRequests', 'projects'));
    }

    /**
     * Show form for creating a Project Travel Request.
     */
    public function create(): View
    {
        Gate::authorize('create', TravelRequest::class);

        $projects = Project::orderBy('name')->get();

        return view('travel.requests.create', compact('projects'));
    }

    /**
     * Store a new Project Travel Request.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', TravelRequest::class);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'travel_category' => 'required|in:domestic,international',
            'request_date' => 'required|date',
            'departure_date' => 'required|date',
            'departure_time_window' => 'nullable|string|max:100',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'return_time_window' => 'nullable|string|max:100',
            'origin' => 'required|string|max:150',
            'destination' => 'required|string|max:150',
            'need_flight' => 'nullable|boolean',
            'need_hotel' => 'nullable|boolean',
            'need_local_transport' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'passengers' => 'required|array|min:1',
            'passengers.*.passenger_name' => 'required|string|max:200',
            'passengers.*.id_card_type' => 'required|in:ktp,passport',
            'passengers.*.id_card_number' => 'nullable|string|max:50',
            'passengers.*.gender' => 'nullable|in:male,female',
            'passengers.*.phone_number' => 'nullable|string|max:50',
            'passengers.*.specific_needs' => 'nullable|string|max:255',
        ]);

        $travelRequest = $this->service->createProjectTravelRequest($validated, $request->user()->id);

        return redirect()->route('travel.requests.show', $travelRequest)
            ->with('success', "Permintaan perjalanan {$travelRequest->request_number} berhasil dibuat.");
    }

    /**
     * Display a specific Travel Request.
     */
    public function show(TravelRequest $travelRequest): View
    {
        Gate::authorize('view', $travelRequest);

        $travelRequest->load([
            'spd.employee',
            'project',
            'creator',
            'passengers.employee',
            'inquiries.vendor',
            'inquiries.quotations.services',
            'bookings.services',
            'bookings.tickets',
        ]);

        return view('travel.requests.show', compact('travelRequest'));
    }

    /**
     * Cancel a Travel Request.
     */
    public function cancel(Request $request, TravelRequest $travelRequest): RedirectResponse
    {
        Gate::authorize('cancel', $travelRequest);

        $travelRequest->update([
            'status' => 'cancelled',
            'notes' => $travelRequest->notes . "\n[Cancelled by " . $request->user()->username . " at " . now()->toDateTimeString() . "]",
        ]);

        return redirect()->route('travel.requests.show', $travelRequest)
            ->with('success', "Permintaan perjalanan {$travelRequest->request_number} telah dibatalkan.");
    }
}
