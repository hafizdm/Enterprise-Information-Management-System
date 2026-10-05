<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Spd;
use App\Models\SpdReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SpdReportController extends Controller
{
    /**
     * Display the employee's own SPD reports.
     */
    public function index()
    {
        abort_unless(
            auth()->user()->can('spd-report.view-own'),
            403,
            'You are not authorized to view SPD reports.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        $reports = SpdReport::with([
            'spd.project',
            'employee',
        ])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        return view(
            'hr.spd-reports.index',
            compact('reports')
        );
    }

    /**
     * Show form for creating or resubmitting an SPD report.
     */
    public function create()
    {
        abort_unless(
            auth()->user()->can('spd-report.create'),
            403,
            'You are not authorized to create an SPD report.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        /*
        |--------------------------------------------------------------------------
        | Load Approved SPDs
        |--------------------------------------------------------------------------
        |
        | Available SPDs are:
        |
        | 1. Approved SPD without a report
        | 2. Approved SPD whose existing report was rejected
        |
        | A rejected report must be resubmitted using the SAME report record.
        |
        */

        $spds = Spd::with([
            'employee.costLevel',
            'project',
            'manager',
            'approvalDocument',
            'report',
        ])
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($query) {

                /*
                | SPD has never had a report
                */
                $query->whereDoesntHave('report')

                    /*
                    | OR existing report was rejected
                    */
                    ->orWhereHas('report', function ($reportQuery) {
                        $reportQuery->where(
                            'status_report',
                            'rejected'
                        );
                    });
            })
            ->latest()
            ->get();

        return view(
            'hr.spd-reports.create',
            compact('spds')
        );
    }

    /**
     * Store a new SPD report or resubmit a rejected report.
     */
    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->can('spd-report.create'),
            403,
            'You are not authorized to create an SPD report.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Employee Input
        |--------------------------------------------------------------------------
        |
        | Meals per Day and Allowance per Day are intentionally NOT validated
        | from the request because their values must come from the employee's
        | Cost Level on the server.
        |
        */

        $validated = $request->validate([
            'spd_id' => [
                'required',
                'integer',
                'exists:spds,id',
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

            'expense_evidence' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Load Approved SPD
        |--------------------------------------------------------------------------
        |
        | Security:
        | - SPD must belong to authenticated employee.
        | - SPD must be fully approved.
        |
        */

        $spd = Spd::with([
            'employee.costLevel',
            'project',
            'manager',
            'approvalDocument',
            'report',
        ])
            ->where('id', $validated['spd_id'])
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->first();

        abort_unless(
            $spd,
            403,
            'This SPD is not available for your report.'
        );

        /*
        |--------------------------------------------------------------------------
        | Determine Existing Report
        |--------------------------------------------------------------------------
        |
        | If a report exists:
        |
        | - rejected  = allowed to resubmit
        | - submitted = not allowed
        | - approved  = not allowed
        | - settled   = not allowed
        |
        */

        $existingReport = $spd->report;

        if ($existingReport) {

            abort_unless(
                $existingReport->status_report === 'rejected',
                422,
                'This SPD already has a report and cannot be submitted again.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Employee Cost Level
        |--------------------------------------------------------------------------
        */

        $costLevel = $employee->costLevel;

        abort_unless(
            $costLevel,
            422,
            'Your employee record does not have a Cost Level.'
        );

        abort_unless(
            $costLevel->is_active,
            422,
            'Your assigned Cost Level is not active.'
        );

        /*
        |--------------------------------------------------------------------------
        | Determine Meals & Allowance Rate
        |--------------------------------------------------------------------------
        |
        | The rate is taken from the employee's Cost Level and the approved
        | SPD travel type.
        |
        */

        $travelType = strtolower(
            trim((string) $spd->travel_type)
        );

        if ($travelType === 'domestic') {

            $mealsPerDay =
                (float) $costLevel->meals_domestic;

            $allowancePerDay =
                (float) $costLevel->allowance_domestic;

        } elseif ($travelType === 'international') {

            $mealsPerDay =
                (float) $costLevel->meals_international;

            $allowancePerDay =
                (float) $costLevel->allowance_international;

        } else {

            abort(
                422,
                'The SPD travel type is invalid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Actual Travel Duration
        |--------------------------------------------------------------------------
        */

        $dateDeparture = Carbon::parse(
            $validated['date_departure']
        );

        $dateReturn = Carbon::parse(
            $validated['date_return']
        );

        $totalDays =
            $dateDeparture->diffInDays($dateReturn) + 1;

        /*
        |--------------------------------------------------------------------------
        | Actual Expenses
        |--------------------------------------------------------------------------
        */

        $localTransport =
            (float) ($validated['local_transport'] ?? 0);

        $contingencies =
            (float) ($validated['contingencies'] ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Calculate Meals & Allowance Total
        |--------------------------------------------------------------------------
        */

        $mealsTotal =
            $mealsPerDay * $totalDays;

        $allowanceTotal =
            $allowancePerDay * $totalDays;

        /*
        |--------------------------------------------------------------------------
        | Calculate Actual Expense Balance
        |--------------------------------------------------------------------------
        */

        $expenseBalance =
            $mealsTotal
            + $allowanceTotal
            + $localTransport
            + $contingencies;

        /*
        |--------------------------------------------------------------------------
        | Snapshot Approved SPD Balance
        |--------------------------------------------------------------------------
        */

        $balanceReceived =
            (float) $spd->balance_received;

        /*
        |--------------------------------------------------------------------------
        | Calculate Settlement Result
        |--------------------------------------------------------------------------
        |
        | Negative = Company owes employee
        | Zero     = Cash clear
        | Positive = Employee returns money
        |
        */

        $expenseReportTotal =
            $balanceReceived - $expenseBalance;

        if ($expenseReportTotal < 0) {

            $settlementStatus = 'reimburse';

        } elseif ($expenseReportTotal > 0) {

            $settlementStatus = 'refund_employee';

        } else {

            $settlementStatus = 'cash_clear';
        }

        /*
        |--------------------------------------------------------------------------
        | Store Evidence
        |--------------------------------------------------------------------------
        */

        $evidencePath = null;

        if ($request->hasFile('expense_evidence')) {

            $evidencePath = $request->file('expense_evidence')
                ->store(
                    'spd-reports/evidence',
                    'public'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Create New Report OR Resubmit Existing Rejected Report
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $spd,
            $employee,
            $existingReport,
            $validated,
            $totalDays,
            $mealsPerDay,
            $allowancePerDay,
            $localTransport,
            $contingencies,
            $balanceReceived,
            $expenseBalance,
            $expenseReportTotal,
            $settlementStatus,
            $evidencePath
        ) {

            /*
            |--------------------------------------------------------------------------
            | New SPD Report
            |--------------------------------------------------------------------------
            */

            if (!$existingReport) {

                SpdReport::create([
                    'spd_id' =>
                        $spd->id,

                    'employee_id' =>
                        $employee->id,

                    /*
                    | Actual Travel
                    */
                    'date_departure' =>
                        $validated['date_departure'],

                    'date_return' =>
                        $validated['date_return'],

                    'total_days' =>
                        $totalDays,

                    /*
                    | Actual Expenses
                    */
                    'meals_per_day' =>
                        $mealsPerDay,

                    'allowance_per_day' =>
                        $allowancePerDay,

                    'local_transport' =>
                        $localTransport,

                    'contingencies' =>
                        $contingencies,

                    /*
                    | Financial Calculation
                    */
                    'balance_received' =>
                        $balanceReceived,

                    'expense_balance' =>
                        $expenseBalance,

                    'expense_report_total' =>
                        $expenseReportTotal,

                    /*
                    | Report Workflow
                    */
                    'status_report' =>
                        'submitted',

                    'settlement_status' =>
                        $settlementStatus,

                    /*
                    | Evidence
                    */
                    'expense_evidence' =>
                        $evidencePath,

                    'note' =>
                        $validated['note'] ?? null,

                    /*
                    | Submission
                    */
                    'submitted_at' =>
                        now(),
                ]);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Resubmit Existing Rejected Report
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | We update the existing report.
            |
            | The SPD number and spd_id remain exactly the same.
            |
            */

            $oldEvidencePath =
                $existingReport->expense_evidence;

            $existingReport->update([
                'date_departure' =>
                    $validated['date_departure'],

                'date_return' =>
                    $validated['date_return'],

                'total_days' =>
                    $totalDays,

                'meals_per_day' =>
                    $mealsPerDay,

                'allowance_per_day' =>
                    $allowancePerDay,

                'local_transport' =>
                    $localTransport,

                'contingencies' =>
                    $contingencies,

                'balance_received' =>
                    $balanceReceived,

                'expense_balance' =>
                    $expenseBalance,

                'expense_report_total' =>
                    $expenseReportTotal,

                /*
                | Rejected → Submitted
                */
                'status_report' =>
                    'submitted',

                'settlement_status' =>
                    $settlementStatus,

                /*
                | New evidence
                */
                'expense_evidence' =>
                    $evidencePath,

                'note' =>
                    $validated['note'] ?? null,

                /*
                | Reset Manager Decision
                */
                'manager_approved_at' =>
                    null,

                'manager_rejection_reason' =>
                    null,

                /*
                | New submission time
                */
                'submitted_at' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Delete Previous Evidence
            |--------------------------------------------------------------------------
            |
            | The old evidence is no longer needed after the employee
            | uploads the corrected evidence.
            |
            */

            if (
                $oldEvidencePath &&
                $oldEvidencePath !== $evidencePath
            ) {
                Storage::disk('public')
                    ->delete($oldEvidencePath);
            }
        });

        return redirect()
            ->route('spd-reports.index')
            ->with(
                'success',
                $existingReport
                    ? 'SPD Report berhasil diperbaiki dan diajukan kembali untuk approval Manager.'
                    : 'SPD Report berhasil dibuat dan disubmit untuk approval Manager.'
            );
    }

    /**
     * Display an SPD report.
     */
    public function show(SpdReport $spdReport)
    {
        abort_unless(
            auth()->user()->can('spd-report.view-own'),
            403,
            'You are not authorized to view SPD reports.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        /*
        |--------------------------------------------------------------------------
        | Employee Ownership Check
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $spdReport->employee_id === $employee->id,
            403,
            'You are not authorized to view this SPD report.'
        );

        $spdReport->load([
            'spd.project',
            'spd.manager',
            'spd.approvalDocument',
            'employee.costLevel',
            'employee',
        ]);

        return view(
            'hr.spd-reports.show',
            compact('spdReport')
        );
    }
}
