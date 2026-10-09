<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Spd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\SpdApprovalResultNotification;

class SpdApprovalController extends Controller
{
    /**
     * Display SPD requests that require approval
     * from the currently authenticated employee.
     */
    public function index()
    {
        abort_unless(
            auth()->user()->can('spd.approve'),
            403,
            'You are not authorized to approve SPD requests.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        $spds = Spd::with([
            'employee',
            'project',
            'manager',
            'approvalDocument',
            'creator',
        ])
            ->where(function ($query) use ($employee) {

                // Manager Approval
                $query->where(function ($managerQuery) use ($employee) {

                    $managerQuery
                        ->where('manager_id', $employee->id)
                        ->where('status', 'pending_manager');

                })

                // Cost Control Approval
                ->orWhere(function ($costControlQuery) use ($employee) {

                    $costControlQuery
                        ->where(
                            'approval_document_id',
                            $employee->id
                        )
                        ->where(
                            'status',
                            'pending_document'
                        );

                });

            })
            ->latest()
            ->paginate(10);

        return view(
            'hr.spds.approvals.index',
            compact('spds')
        );
    }

    /**
     * Display an SPD request for approval.
     */
    public function show(Spd $spd)
    {
        abort_unless(
            auth()->user()->can('spd.approve'),
            403,
            'You are not authorized to approve SPD requests.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        $isManagerApproval =
            $spd->manager_id === $employee->id
            && $spd->status === 'pending_manager';

        $isCostControlApproval =
            $spd->approval_document_id === $employee->id
            && $spd->status === 'pending_document';

        abort_unless(
            $isManagerApproval || $isCostControlApproval,
            403,
            'You are not authorized to review this SPD.'
        );

        $spd->load([
            'employee.division',
            'employee.position',
            'project',
            'manager',
            'approvalDocument',
            'creator',
        ]);

        return view(
            'hr.spds.approvals.show',
            compact('spd')
        );
    }

    
    /**
     * Display SPD confirmation page opened from an approval email.
     */
    public function emailConfirm(Spd $spd, string $stage)
    {
        abort_unless(
            auth()->user()->can('spd.approve'),
            403,
            'You are not authorized to approve SPD requests.'
        );

        abort_unless(
            in_array($stage, ['manager', 'cost_control'], true),
            404,
            'Invalid SPD approval stage.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        if ($stage === 'manager') {
            abort_unless(
                $spd->manager_id === $employee->id
                && $spd->status === 'pending_manager',
                403,
                'This SPD is not awaiting your Manager approval.'
            );
        }

        if ($stage === 'cost_control') {
            abort_unless(
                $spd->approval_document_id === $employee->id
                && $spd->status === 'pending_document',
                403,
                'This SPD is not awaiting your Cost Control approval.'
            );
        }

        $spd->load([
            'employee.division',
            'employee.position',
            'employee.costLevel',
            'project',
            'manager',
            'approvalDocument',
            'creator.employee',
        ]);

        return view(
            'hr.spds.approvals.email-confirm',
            compact('spd', 'stage')
        );
    }

    /**
     * Approve an SPD request.
     */
   
    public function approve(Spd $spd)
    {
        abort_unless(
            auth()->user()->can('spd.approve'),
            403,
            'You are not authorized to approve SPD requests.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        /*
        |--------------------------------------------------------------------------
        | Manager Approval
        |--------------------------------------------------------------------------
        */

        if ($spd->status === 'pending_manager') {

            abort_unless(
                $spd->manager_id === $employee->id,
                403,
                'You are not the manager assigned to approve this SPD.'
            );

            $spd->manager_approved_at = now();

            /*
            |--------------------------------------------------------------------------
            | Next Approval Stage
            |--------------------------------------------------------------------------
            |
            | Even if the Manager and Cost Control are the same person,
            | the SPD must still move to pending_document.
            |
            */

            $spd->status = 'pending_document';

            $spd->save();

            /*
            |--------------------------------------------------------------------------
            | Notify Cost Control
            |--------------------------------------------------------------------------
            |
            | Send the approval request after the SPD status is saved.
            |
            */

            $spd->loadMissing([
                'approvalDocument.user',
            ]);

            $costControlUser = $spd->approvalDocument?->user;

            if ($costControlUser) {
                $costControlUser->notify(
                    new SpdApprovalResultNotification(
                        $spd,
                        'approved',
                        'cost_control',
                        'request'
                    )
                );
            }

            return redirect()
                ->route('spd.approvals.index')
                ->with(
                    'success',
                    'SPD berhasil disetujui oleh Manager dan menunggu approval Cost Control.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cost Control Approval
        |--------------------------------------------------------------------------
        */

        if ($spd->status === 'pending_document') {

            abort_unless(
                $spd->approval_document_id === $employee->id,
                403,
                'You are not the Cost Control employee assigned to approve this SPD.'
            );

            $spd->document_approved_at = now();

            $spd->status = 'approved';

            $spd->save();

            /*
            |--------------------------------------------------------------------------
            | Notify HR and Employee
            |--------------------------------------------------------------------------
            |
            | Send the final approval result to the SPD creator and the employee
            | who will travel. Avoid duplicate notification to the same account.
            |
            */

            $spd->loadMissing([
                'creator',
                'employee.user',
            ]);

            $hrUser = $spd->creator;
            $employeeUser = $spd->employee?->user;

            if ($hrUser) {
                $hrUser->notify(
                    new SpdApprovalResultNotification(
                        $spd,
                        'approved',
                        'cost_control'
                    )
                );
            }

            if (
                $employeeUser
                && (!$hrUser || $employeeUser->id !== $hrUser->id)
            ) {
                $employeeUser->notify(
                    new SpdApprovalResultNotification(
                        $spd,
                        'approved',
                        'cost_control'
                    )
                );
            }

            return redirect()
                ->route('spd.approvals.index')
                ->with(
                    'success',
                    'SPD berhasil disetujui oleh Cost Control.'
                );
        }

        abort(
            422,
            'This SPD is not waiting for your approval.'
        );
    }

    /**
     * Reject an SPD request.
     */

    public function reject(Request $request, Spd $spd)
    {
        abort_unless(
            auth()->user()->can('spd.approve'),
            403,
            'You are not authorized to reject SPD requests.'
        );

        $employee = auth()->user()->employee;

        abort_unless(
            $employee,
            403,
            'Your user account is not linked to an employee.'
        );

        /*
        |--------------------------------------------------------------------------
        | Validate rejection reason
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Manager Rejection
        |--------------------------------------------------------------------------
        */

        if ($spd->status === 'pending_manager') {

            abort_unless(
                $spd->manager_id === $employee->id,
                403,
                'You are not the manager assigned to reject this SPD.'
            );

            DB::transaction(function () use ($spd, $validated) {

                $employee = Employee::lockForUpdate()
                    ->findOrFail($spd->employee_id);

                $spd->manager_rejection_reason =
                    $validated['rejection_reason'];

                $spd->status = 'rejected';

                $spd->save();

                $employee->spd_limit = $employee->spd_limit + 1;

                $employee->save();
            });

            /*
            |--------------------------------------------------------------------------
            | Notify HR after Manager Rejection
            |--------------------------------------------------------------------------
            */

            $spd->loadMissing('creator');

            $hrUser = $spd->creator;

            if ($hrUser) {
                $hrUser->notify(
                    new SpdApprovalResultNotification(
                        $spd,
                        'rejected',
                        'manager'
                    )
                );
            }

            return redirect()
                ->route('spd.approvals.index')
                ->with(
                    'success',
                    'SPD berhasil ditolak oleh Manager dan SPD limit employee dikembalikan.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Cost Control Rejection
        |--------------------------------------------------------------------------
        */

        if ($spd->status === 'pending_document') {

            abort_unless(
                $spd->approval_document_id === $employee->id,
                403,
                'You are not the Cost Control employee assigned to reject this SPD.'
            );

            DB::transaction(function () use ($spd, $validated) {

                $employee = Employee::lockForUpdate()
                    ->findOrFail($spd->employee_id);

                $spd->document_rejection_reason =
                    $validated['rejection_reason'];

                $spd->status = 'rejected';

                $spd->save();

                $employee->spd_limit = $employee->spd_limit + 1;

                $employee->save();
            });

            /*
            |--------------------------------------------------------------------------
            | Notify HR after Cost Control Rejection
            |--------------------------------------------------------------------------
            */

            $spd->loadMissing('creator');

            $hrUser = $spd->creator;

            if ($hrUser) {
                $hrUser->notify(
                    new SpdApprovalResultNotification(
                        $spd,
                        'rejected',
                        'cost_control'
                    )
                );
            }

            return redirect()
                ->route('spd.approvals.index')
                ->with(
                    'success',
                    'SPD berhasil ditolak oleh Cost Control dan SPD limit employee dikembalikan.'
                );
        }

        abort(
            422,
            'This SPD is not waiting for your approval.'
        );
    }

}