<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Spd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class SpdController extends Controller
{
    /**
     * Display all SPD requests.
     */
    public function index()
    {
        abort_unless(
            auth()->user()->can('spd.view-any'),
            403,
            'You are not authorized to view SPD requests.'
        );

        $spds = Spd::with([
            'employee',
            'project',
            'manager',
            'approvalDocument',
            'creator',
        ])
            ->latest()
            ->paginate(10);

        return view('hr.spds.index', compact('spds'));
    }

    /**
     * Show the SPD creation form.
     */
    public function create()
    {
        abort_unless(
            auth()->user()->can('spd.create'),
            403,
            'You are not authorized to create an SPD.'
        );

        $employees = Employee::with([
            'manager',
            'project',
            'costLevel',
        ])
            ->orderBy('full_name')
            ->get();

        $projects = Project::with([
            'approvalEmployee',
        ])
            ->orderBy('name')
            ->get();

        return view(
            'hr.spds.create',
            compact('employees', 'projects')
        );
    }

    /**
     * Store a newly created SPD.
     */
   public function store(Request $request)
    {
        abort_unless(
            auth()->user()->can('spd.create'),
            403,
            'You are not authorized to create an SPD.'
        );

        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'project_id' => [
                'required',
                'exists:projects,id',
            ],

            'travel_type' => [
                'required',
                'in:domestic,international',
            ],

            'from' => [
                'required',
                'string',
                'max:255',
            ],

            'destination' => [
                'required',
                'string',
                'max:255',
            ],

            'date_departure' => [
                'required',
                'date',
            ],

            'date_return' => [
                'required',
                'date',
                'after_or_equal:date_departure',
            ],

            'local_transport' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'contingencies' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'transportation' => [
                'required',
                'in:car,plane,ship,other',
            ],

            'advance_payment' => [
                'required',
                'boolean',
            ],

            'note' => [
                'nullable',
                'string',
            ],

            'purpose' => [
                'required',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | SPD Numbering
        |--------------------------------------------------------------------------
        |
        | Sequence resets every year.
        |
        | Example:
        | 2026 -> 001, 002, 003, ...
        | 2027 -> 001, 002, 003, ...
        |
        | Month represents the month when the SPD is created.
        |
        */

        $documentYear = now()->year;

        $lockName = 'eims_spd_numbering_' . $documentYear;

        $lockResult = DB::selectOne(
            'SELECT GET_LOCK(?, 10) AS acquired',
            [$lockName]
        );

        abort_unless(
            $lockResult && (int) $lockResult->acquired === 1,
            503,
            'The system is currently generating another SPD number. Please try again.'
        );

        try {

            DB::transaction(function () use (
                $validated,
                $documentYear
            ) {

                /*
                |--------------------------------------------------------------------------
                | Employee
                |--------------------------------------------------------------------------
                */

                $employee = Employee::with([
                    'manager',
                    'costLevel',
                ])
                    ->lockForUpdate()
                    ->findOrFail($validated['employee_id']);

                /*
                |--------------------------------------------------------------------------
                | SPD Limit
                |--------------------------------------------------------------------------
                */

                if ($employee->spd_limit <= 0) {
                    abort(
                        422,
                        'This employee has no available SPD limit.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Manager
                |--------------------------------------------------------------------------
                */

                $manager = $employee->manager;

                if (!$manager) {
                    abort(
                        422,
                        'The selected employee does not have a manager assigned.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Project
                |--------------------------------------------------------------------------
                */

                $project = Project::with([
                    'approvalEmployee',
                ])
                    ->findOrFail($validated['project_id']);

                /*
                |--------------------------------------------------------------------------
                | Cost Level
                |--------------------------------------------------------------------------
                */

                $costLevel = $employee->costLevel;

                if (!$costLevel) {
                    abort(
                        422,
                        'The selected employee does not have a Cost Level assigned.'
                    );
                }

                if (!$costLevel->is_active) {
                    abort(
                        422,
                        'The Cost Level assigned to this employee is inactive.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Cost Control / Approval Document
                |--------------------------------------------------------------------------
                */

                $approvalDocument = $project->approvalEmployee;

                if (!$approvalDocument) {
                    abort(
                        422,
                        'The selected project does not have an Approval Document employee assigned.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Total Days
                |--------------------------------------------------------------------------
                */

                $dateDeparture = Carbon::parse(
                    $validated['date_departure']
                );

                $dateReturn = Carbon::parse(
                    $validated['date_return']
                );

                $totalDays = $dateDeparture->diffInDays($dateReturn) + 1;

                /*
                |--------------------------------------------------------------------------
                | Meals & Allowance
                |--------------------------------------------------------------------------
                */

                if ($validated['travel_type'] === 'domestic') {
                    $mealsPerDay = (float) $costLevel->meals_domestic;
                    $allowancePerDay = (float) $costLevel->allowance_domestic;
                } else {
                    $mealsPerDay = (float) $costLevel->meals_international;
                    $allowancePerDay = (float) $costLevel->allowance_international;
                }

                /*
                |--------------------------------------------------------------------------
                | Additional Costs
                |--------------------------------------------------------------------------
                */

                $localTransport = (float) (
                    $validated['local_transport'] ?? 0
                );

                $contingencies = (float) (
                    $validated['contingencies'] ?? 0
                );

                /*
                |--------------------------------------------------------------------------
                | Balance Received
                |--------------------------------------------------------------------------
                */

                $balanceReceived =
                    ($mealsPerDay * $totalDays)
                    + ($allowancePerDay * $totalDays)
                    + $localTransport
                    + $contingencies;

                /*
                |--------------------------------------------------------------------------
                | Generate SPD Number
                |--------------------------------------------------------------------------
                */

                $lastSequence = Spd::where(
                    'document_year',
                    $documentYear
                )
                    ->max('document_sequence');

                $documentSequence = ((int) $lastSequence) + 1;

                $documentMonth = now()->format('m');

                $spdNumber = sprintf(
                    'RII/HC-SPD/%s/%d/%03d',
                    $documentMonth,
                    $documentYear,
                    $documentSequence
                );

                /*
                |--------------------------------------------------------------------------
                | Create SPD
                |--------------------------------------------------------------------------
                */

                Spd::create([
                    'spd_number' => $spdNumber,

                    'document_year' => $documentYear,

                    'document_sequence' => $documentSequence,

                    'employee_id' => $employee->id,

                    'project_id' => $project->id,

                    'manager_id' => $manager->id,

                    'approval_document_id' => $approvalDocument->id,

                    'travel_type' => $validated['travel_type'],

                    'from' => $validated['from'],

                    'destination' => $validated['destination'],

                    'date_departure' => $validated['date_departure'],

                    'date_return' => $validated['date_return'],

                    'total_days' => $totalDays,

                    'meals_per_day' => $mealsPerDay,

                    'allowance_per_day' => $allowancePerDay,

                    'local_transport' => $localTransport,

                    'contingencies' => $contingencies,

                    'balance_received' => $balanceReceived,

                    'transportation' => $validated['transportation'],

                    'advance_payment' => $validated['advance_payment'],

                    'note' => $validated['note'] ?? null,

                    'purpose' => $validated['purpose'],

                    'status' => 'pending_manager',

                    'created_by' => auth()->id(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Decrease SPD Limit
                |--------------------------------------------------------------------------
                */

                $employee->spd_limit = $employee->spd_limit - 1;

                $employee->save();
            });

        } finally {

            /*
            |--------------------------------------------------------------------------
            | Release SPD Numbering Lock
            |--------------------------------------------------------------------------
            */

            DB::select(
                'SELECT RELEASE_LOCK(?)',
                [$lockName]
            );
        }

        return redirect()
            ->route('spds.index')
            ->with(
                'success',
                'SPD berhasil dibuat dan menunggu approval Manager.'
            );
    }
   /**
     * Display the specified SPD.
     */
    public function show(Spd $spd)
    {
        abort_unless(
            auth()->user()->can('spd.view-any'),
            403,
            'You are not authorized to view this SPD.'
        );

        $spd->load([
            'employee',
            'project',
            'manager',
            'approvalDocument',
            'creator',
        ]);

        return view('hr.spds.show', compact('spd'));
    }

    /**
 * Generate SPD PDF.
 */
   /**
 * Generate SPD PDF.
 */
/**
 * Generate SPD PDF.
 */
    public function pdf(Spd $spd)
    {
        $user = auth()->user();

        $employee = $user->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        |
        | User can view the SPD PDF if:
        |
        | 1. User has spd.view-any
        | 2. User is the assigned Manager
        | 3. User is the assigned Cost Center User
        |
        */

        $canViewAny = $user->can('spd.view-any');

        $isManager = $spd->manager_id === $employee->id;

        $isCostCenterUser =
            $spd->approval_document_id === $employee->id;

        abort_unless(
            $canViewAny ||
            $isManager ||
            $isCostCenterUser,
            403,
            'You are not authorized to print this SPD.'
        );

        $spd->load([
            'employee.division',
            'employee.position',
            'employee.project',
            'project',
            'manager.position',
            'approvalDocument.position',
            'creator.employee',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'hr.spds.pdf',
            compact('spd')
        );

        return $pdf->stream(
            'spd-' . $spd->id . '.pdf'
        );
    }

        /**
         * Delete an SPD request.
         */
    public function destroy(Spd $spd)
    {
        abort_unless(
            auth()->user()->can('spd.delete'),
            403,
            'You are not authorized to delete this SPD.'
        );

        abort_unless(
            $spd->status === 'pending_manager',
            422,
            'Only SPD requests waiting for Manager approval can be deleted.'
        );

        DB::transaction(function () use ($spd) {

            $employee = Employee::lockForUpdate()
                ->findOrFail($spd->employee_id);

            $employee->spd_limit = $employee->spd_limit + 1;

            $employee->save();

            $spd->delete();
        });

        return redirect()
            ->route('spds.index')
            ->with(
                'success',
                'SPD berhasil dihapus dan SPD limit employee dikembalikan.'
            );
    }
}