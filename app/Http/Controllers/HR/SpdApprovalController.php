<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Spd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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