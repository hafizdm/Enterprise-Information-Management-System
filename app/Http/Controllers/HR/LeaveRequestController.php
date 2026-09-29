<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveRequestController extends Controller
{

   public function index()
{
    $employee = auth()->user()->employee;

    if (!$employee) {
        abort(403, 'Your account is not linked to an employee.');
    }

    $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
        ->latest()
        ->paginate(10);

    // Hitung Annual Leave yang sudah digunakan
    $activeAnnualLeave = LeaveRequest::where(
    'employee_id',
    $employee->id
    )
        ->where('leave_type', 'annual')
        ->whereIn('status', ['pending_manager', 'approved'])
        ->sum('total_days');

    $remainingAnnualLeave =
        $employee->total_annual_leave - $activeAnnualLeave;

    return view('hr.leave_requests.index', compact(
        'leaveRequests',
        'remainingAnnualLeave'
    ));
}

    public function create()
    {
        return view('hr.leave_requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => [
                'required',
                'in:annual,sick,hajj,site,demobilization',
            ],

            'first_date' => [
                'required',
                'date',
            ],

            'last_date' => [
                'required',
                'date',
                'after_or_equal:first_date',
            ],

            'reason' => [
                'required',
                'string',
            ],
        ]);

        // Employee yang mengajukan
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        // Manager berdasarkan report_to
        $manager = $employee->manager;

        if (!$manager) {
            return back()
                ->withInput()
                ->withErrors([
                    'leave_type' => 'Your employee profile does not have a manager assigned.',
                ]);
        }


        // Hitung hari kerja: Monday - Friday
        $start = $validated['first_date'];
        $end = $validated['last_date'];

        $totalDays = 0;

        $current = \Carbon\Carbon::parse($start);
        $last = \Carbon\Carbon::parse($end);

        while ($current->lte($last)) {

            if ($current->isWeekday()) {
                $totalDays++;
            }

            $current->addDay();
        }

        // Pastikan periode memiliki minimal 1 hari kerja
        if ($totalDays < 1) {
            return back()
                ->withInput()
                ->withErrors([
                    'first_date' => 'The selected date range does not contain any working days.',
                ]);
        }

        // Cek saldo Annual Leave
        if ($validated['leave_type'] === 'annual') {

            $activeAnnualLeave = LeaveRequest::where(
            'employee_id',
            $employee->id
        )
            ->where('leave_type', 'annual')
            ->whereIn('status', ['pending_manager', 'approved'])
            ->sum('total_days');

        $remainingAnnualLeave =
            $employee->total_annual_leave - $activeAnnualLeave;

            if ($totalDays > $remainingAnnualLeave) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'first_date' =>
                            "Annual Leave balance is insufficient. Remaining balance: {$remainingAnnualLeave} day(s).",
                    ]);
            }
        }

        // Simpan Leave Request
        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => $validated['leave_type'],
            'first_date' => $validated['first_date'],
            'last_date' => $validated['last_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'manager_id' => $manager->id,
            'status' => 'pending_manager',
        ]);

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        // Pastikan leave request memang milik employee yang sedang login
        if ($leaveRequest->employee_id !== $employee->id) {
            abort(403, 'You are not authorized to view this leave request.');
        }

        $leaveRequest->load([
            'employee',
            'manager',
        ]);

        return view(
            'hr.leave_requests.show',
            compact('leaveRequest')
        );
    }


    public function pdf(LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        |
        | HRD / System Administrator:
        | Can view any leave request through leave.view-any.
        |
        | Employee:
        | Can only view their own leave request.
        |
        */

        if (
            !auth()->user()->can('leave.view-any') &&
            $leaveRequest->employee_id !== $employee->id
        ) {
            abort(403, 'You are not authorized to view this leave request.');
        }

        $leaveRequest->load([
            'employee.division',
            'employee.position',
            'employee.project',
            'manager',
        ]);

        $pdf = Pdf::loadView(
            'hr.leave_requests.pdf',
            compact('leaveRequest')
        );

        return $pdf->stream(
            'leave-request-' . $leaveRequest->id . '.pdf'
        );
    }


    public function destroy(LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        // Pastikan leave request milik employee yang sedang login
        if ($leaveRequest->employee_id !== $employee->id) {
            abort(403, 'You are not authorized to delete this leave request.');
        }

        // Hanya request yang masih menunggu approval manager yang boleh dihapus
        if ($leaveRequest->status !== 'pending_manager') {
            return back()
                ->withErrors([
                    'leave' => 'Only pending leave requests can be deleted.',
                ]);
        }

        $leaveRequest->delete();

        return redirect()
            ->route('leave-requests.index')
            ->with('success', 'Leave request deleted successfully.');
    }


    public function managerIndex()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        $leaveRequests = LeaveRequest::with([
            'employee',
            'manager',
        ])
            ->where('manager_id', $employee->id)
            ->where('status', 'pending_manager')
            ->latest()
            ->paginate(10);

        return view('hr.leave_requests.approval.index', compact(
            'leaveRequests'
        ));
    }


    public function managerShow(LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        // Pastikan request memang ditujukan kepada manager yang sedang login
        if ($leaveRequest->manager_id !== $employee->id) {
            abort(403, 'You are not authorized to view this leave request.');
        }

        $leaveRequest->load([
            'employee',
            'manager',
            'hrApprover',
        ]);

        return view(
            'hr.leave_requests.approval.show',
            compact('leaveRequest')
        );
    }

    public function managerApprove(LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        // Pastikan request memang milik manager yang sedang login
        if ($leaveRequest->manager_id !== $employee->id) {
            abort(403, 'You are not authorized to approve this leave request.');
        }

        // Pastikan request masih menunggu approval manager
        if ($leaveRequest->status !== 'pending_manager') {
            return back()
                ->withErrors([
                    'leave' => 'This leave request is no longer waiting for manager approval.',
                ]);
        }

       $leaveRequest->update([
            'status' => 'approved',
            'manager_approved_at' => now(),
        ]);

        return redirect()
            ->route('leave-approvals.index')
            ->with('success', 'Leave request approved successfully.');
    }


    public function managerReject(Request $request, LeaveRequest $leaveRequest)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Your account is not linked to an employee.');
        }

        // Pastikan request memang milik manager yang sedang login
        if ($leaveRequest->manager_id !== $employee->id) {
            abort(403, 'You are not authorized to reject this leave request.');
        }

        // Pastikan request masih menunggu approval manager
        if ($leaveRequest->status !== 'pending_manager') {
            return back()
                ->withErrors([
                    'leave' => 'This leave request is no longer waiting for manager approval.',
                ]);
        }

        $validated = $request->validate([
            'manager_rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'manager_rejection_reason' => $validated['manager_rejection_reason'],
        ]);

        return redirect()
            ->route('leave-approvals.index')
            ->with('success', 'Leave request rejected.');
    }

    public function hrIndex()
    {
        $leaveRequests = LeaveRequest::with([
            'employee',
            'manager',
        ])
            ->latest()
            ->paginate(10);

        return view(
            'hr.leave_requests.monitoring.index',
            compact('leaveRequests')
        );
    }

    public function monitoringShow(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load([
            'employee',
            'manager',
        ]);

        return view(
            'hr.leave_requests.monitoring.show',
            compact('leaveRequest')
        );
    }
}