<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SpdReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\SpdReportNotification;

class SpdReportApprovalController extends Controller
{
    /**
     * Display SPD reports waiting for Manager approval.
     */
    public function index()
    {
        abort_unless(
            auth()->user()->can('spd-report.view-approval'),
            403,
            'You are not authorized to view SPD Report approvals.'
        );

        $manager = auth()->user()->employee;

        abort_unless(
            $manager,
            403,
            'Your user account is not linked to an employee.'
        );

        $reports = SpdReport::with([
            'spd.project',
            'employee',
            'employee.costLevel',
            'spd.manager',
        ])
            ->where('status_report', 'submitted')
            ->whereHas('employee', function ($query) use ($manager) {
                $query->where('report_to', $manager->id);
            })
            ->latest('submitted_at')
            ->paginate(10);

        return view(
            'hr.spd-reports.approval.index',
            compact('reports')
        );
    }

    /**
     * Display an SPD report for Manager review.
     */
    public function show(SpdReport $spdReport)
    {
        abort_unless(
            auth()->user()->can('spd-report.view-approval'),
            403,
            'You are not authorized to view SPD Report approvals.'
        );

        $manager = auth()->user()->employee;

        abort_unless(
            $manager,
            403,
            'Your user account is not linked to an employee.'
        );

        abort_unless(
            $spdReport->employee &&
            $spdReport->employee->report_to === $manager->id,
            403,
            'You are not authorized to review this SPD report.'
        );

        $spdReport->load([
            'spd.project',
            'spd.manager',
            'spd.approvalDocument',
            'employee.costLevel',
            'employee',
        ]);

        return view(
            'hr.spd-reports.approval.show',
            compact('spdReport')
        );
    }

    /**
 * Display SPD Report confirmation page from email.
 */

/**
 * Display SPD Report confirmation page from email.
 */
    public function emailConfirm(SpdReport $spdReport)
    {
        abort_unless(
            auth()->user()->can('spd-report.view-approval'),
            403,
            'You are not authorized to view SPD Report approvals.'
        );

        $manager = auth()->user()->employee;

        abort_unless(
            $manager,
            403,
            'Your user account is not linked to an employee.'
        );

        abort_unless(
            $spdReport->employee &&
            $spdReport->employee->report_to === $manager->id,
            403,
            'You are not authorized to review this SPD report.'
        );

        $spdReport->load([
            'spd.project',
            'spd.manager',
            'spd.approvalDocument',
            'spd.creator',
            'employee.costLevel',
            'employee',
        ]);

        if ($spdReport->status_report !== 'submitted') {
            return view(
                'hr.spd-reports.approval.already-processed',
                compact('spdReport')
            );
        }

        return view(
            'hr.spd-reports.approval.email-confirm',
            compact('spdReport')
        );
    }
   /**
     * Approve an SPD report.
     *
     * When approved:
     * - SPD Report status becomes approved.
     * - Manager approval timestamp is recorded.
     * - Employee SPD Limit is increased by 1.
     */

    
    public function approve(SpdReport $spdReport)
    {
        abort_unless(
            auth()->user()->can('spd-report.approve'),
            403,
            'You are not authorized to approve SPD reports.'
        );

        $manager = auth()->user()->employee;

        abort_unless(
            $manager,
            403,
            'Your user account is not linked to an employee.'
        );

        DB::transaction(function () use ($spdReport, $manager) {
            /*
            * Lock the report so the same report cannot be
            * approved twice at the same time.
            */
            $report = SpdReport::with('employee')
                ->lockForUpdate()
                ->findOrFail($spdReport->id);

            /*
            * Make sure this Manager is actually the
            * employee's direct manager.
            */
            abort_unless(
                $report->employee &&
                $report->employee->report_to === $manager->id,
                403,
                'You are not authorized to approve this SPD report.'
            );

            /*
            * Only submitted reports can be approved.
            */
            abort_unless(
                $report->status_report === 'submitted',
                422,
                'This SPD report is not waiting for Manager approval.'
            );

            /*
            * Lock the employee record before changing
            * the SPD Limit.
            */
            $employee = Employee::lockForUpdate()
                ->findOrFail($report->employee_id);

            /*
            * Return one SPD Limit to the employee.
            */
            $employee->increment('spd_limit', 1);

            /*
            * Mark the SPD Report as approved.
            */
            $report->update([
                'status_report' => 'approved',
                'manager_approved_at' => now(),
                'manager_rejection_reason' => null,
            ]);
        });

        /*
        * Notify Employee and HR after successful approval.
        */
        $spdReport->refresh()->loadMissing([
            'employee.user',
            'spd.creator',
        ]);

        $employeeUser = $spdReport->employee?->user;
        $hrUser = $spdReport->spd?->creator;

        if ($employeeUser) {
            $employeeUser->notify(
                new SpdReportNotification($spdReport, 'result')
            );
        }

        if (
            $hrUser &&
            (!$employeeUser || $hrUser->id !== $employeeUser->id)
        ) {
            $hrUser->notify(
                new SpdReportNotification($spdReport, 'result')
            );
        }

        return redirect()
            ->route('spd-report-approvals.index')
            ->with(
                'success',
                'SPD Report berhasil disetujui dan 1 SPD Limit dikembalikan kepada employee.'
            );
    }

    public function reject(Request $request,SpdReport $spdReport) 
    {
        abort_unless(
            auth()->user()->can('spd-report.reject'),
            403,
            'You are not authorized to reject SPD reports.'
        );

        $manager = auth()->user()->employee;

        abort_unless(
            $manager,
            403,
            'Your user account is not linked to an employee.'
        );

        abort_unless(
            $spdReport->employee &&
            $spdReport->employee->report_to === $manager->id,
            403,
            'You are not authorized to reject this SPD report.'
        );

        abort_unless(
            $spdReport->status_report === 'submitted',
            422,
            'This SPD report is not waiting for Manager approval.'
        );

        $validated = $request->validate([
            'manager_rejection_reason' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use (
            $spdReport,
            $validated
        ) {
            $spdReport->update([
                'status_report' => 'rejected',
                'manager_approved_at' => null,
                'manager_rejection_reason' =>
                    $validated['manager_rejection_reason'],
            ]);
        });

        /*
        * Send rejection notification to Employee only.
        * Do not notify HR when the report is rejected.
        */
        $spdReport->refresh()->loadMissing([
            'employee.user',
        ]);

        $employeeUser = $spdReport->employee?->user;

        if ($employeeUser) {
            $employeeUser->notify(
                new SpdReportNotification($spdReport, 'result')
            );
        }

        return redirect()
            ->route('spd-report-approvals.index')
            ->with(
                'success',
                'SPD Report berhasil ditolak dan dikembalikan kepada Employee.'
            );
    }

  
}
