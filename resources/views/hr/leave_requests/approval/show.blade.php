@extends('layouts.app')

@section('content')

<div class="container-fluid">

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">Leave Request Detail</h1>

        <p class="text-muted mb-0">
            Review and approve employee leave request.
        </p>
    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('leave-approvals.index') }}"
            class="btn btn-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>

</div>


{{-- Employee Information --}}
<div class="card mb-4">

    <div class="card-header bg-white py-3">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person me-2"></i>
            Employee Information
        </h6>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3 mb-md-0">

                <div class="text-muted small mb-1">
                    Employee Name
                </div>

                <div class="fw-semibold">
                    {{ $leaveRequest->employee?->full_name ?? '-' }}
                </div>

            </div>


            <div class="col-md-4 mb-3 mb-md-0">

                <div class="text-muted small mb-1">
                    NIK
                </div>

                <div class="fw-semibold">
                    {{ $leaveRequest->employee?->nik ?? '-' }}
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    Manager
                </div>

                <div class="fw-semibold">
                    {{ $leaveRequest->manager?->full_name ?? '-' }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- Leave Information --}}
<div class="card mb-4">

    <div class="card-header bg-white py-3">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-calendar-check me-2"></i>
            Leave Information
        </h6>

    </div>

    <div class="card-body">

        <div class="row">

            {{-- Leave Type --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    Leave Type
                </div>

                <div class="fw-semibold">

                    @switch($leaveRequest->leave_type)

                        @case('annual')
                            Annual Leave
                            @break

                        @case('sick')
                            Sick Leave
                            @break

                        @case('hajj')
                            Hajj Leave
                            @break

                        @case('site')
                            Site Leave
                            @break

                        @case('demobilization')
                            Demobilization Leave
                            @break

                        @default
                            {{ $leaveRequest->leave_type }}

                    @endswitch

                </div>

            </div>


            {{-- Current Status --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    Current Status
                </div>

                <div>

                    @if($leaveRequest->status === 'pending_manager')

                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="bi bi-clock me-1"></i>
                            Pending Manager
                        </span>

                    @elseif($leaveRequest->status === 'approved')

                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Approved
                        </span>

                    @elseif($leaveRequest->status === 'rejected')

                        <span class="badge bg-danger px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i>
                            Rejected
                        </span>

                    @else

                        <span class="badge bg-secondary px-3 py-2">
                            {{ ucfirst($leaveRequest->status) }}
                        </span>

                    @endif

                </div>

            </div>


            {{-- Total Days --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    Total Working Days
                </div>

                <div class="fw-semibold">
                    {{ $leaveRequest->total_days }} days
                </div>

            </div>


            {{-- First Date --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    First Date
                </div>

                <div>
                    {{ $leaveRequest->first_date->format('d M Y') }}
                </div>

            </div>


            {{-- Last Date --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    Last Date
                </div>

                <div>
                    {{ $leaveRequest->last_date->format('d M Y') }}
                </div>

            </div>


            {{-- Submitted --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    Submitted
                </div>

                <div>
                    {{ $leaveRequest->created_at?->format('d M Y H:i') ?? '-' }}
                </div>

            </div>


            {{-- Reason --}}
            <div class="col-12">

                <div class="text-muted small mb-2">
                    Reason
                </div>

                <div class="border rounded p-3 bg-light">

                    {{ $leaveRequest->reason ?: '-' }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- Manager Approval --}}
<div class="card mb-4">

    <div class="card-header bg-white py-3">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person-check me-2"></i>
            Manager Approval
        </h6>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-3 mb-md-0">

                <div class="text-muted small mb-1">
                    Approving Manager
                </div>

                <div class="fw-semibold">
                    {{ $leaveRequest->manager?->full_name ?? '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Approval Status
                </div>

                <div>

                    @if($leaveRequest->status === 'pending_manager')

                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-clock me-1"></i>
                            Awaiting Your Approval
                        </span>

                    @elseif($leaveRequest->status === 'approved')

                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Approved
                        </span>

                        <div class="small text-muted mt-2">
                            Approved at
                            {{ $leaveRequest->manager_approved_at?->format('d M Y H:i') ?? '-' }}
                        </div>

                    @elseif($leaveRequest->status === 'rejected')

                        <span class="badge bg-danger">
                            <i class="bi bi-x-circle me-1"></i>
                            Rejected
                        </span>

                    @endif

                </div>

            </div>

        </div>


        {{-- Rejection Reason --}}
        @if($leaveRequest->status === 'rejected')

            <div class="mt-4">

                <div class="text-muted small mb-2">
                    Manager Rejection Reason
                </div>

                <div class="alert alert-danger mb-0">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    {{ $leaveRequest->manager_rejection_reason ?: '-' }}

                </div>

            </div>

        @endif

    </div>

</div>


{{-- Approval Action --}}
@if($leaveRequest->status === 'pending_manager')

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                <div>

                    <h6 class="fw-semibold mb-1">
                        Manager Action Required
                    </h6>

                    <p class="text-muted mb-0 small">
                        Please review the leave information before approving or rejecting this request.
                    </p>

                </div>

                <div class="d-flex gap-2">

                    {{-- Approve --}}
                    <form
                        action="{{ route('leave-approvals.approve', $leaveRequest) }}"
                        method="POST"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-success px-4"
                        >
                            <i class="bi bi-check-lg me-1"></i>
                            Approve
                        </button>

                    </form>


                    {{-- Reject --}}
                    <button
                        type="button"
                        class="btn btn-danger px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectLeaveModal"
                    >
                        <i class="bi bi-x-lg me-1"></i>
                        Reject
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- Reject Modal --}}
    <div
        class="modal fade"
        id="rejectLeaveModal"
        tabindex="-1"
        aria-labelledby="rejectLeaveModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="rejectLeaveModalLabel"
                    >
                        Reject Leave Request
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <form
                    action="{{ route('leave-approvals.reject', $leaveRequest) }}"
                    method="POST"
                >

                    @csrf

                    <div class="modal-body">

                        <p class="text-muted small mb-3">
                            Please provide a reason for rejecting this leave request.
                        </p>

                        <div class="mb-3">

                            <label
                                for="manager_rejection_reason"
                                class="form-label fw-semibold"
                            >
                                Rejection Reason
                            </label>

                            <textarea
                                name="manager_rejection_reason"
                                id="manager_rejection_reason"
                                class="form-control"
                                rows="4"
                                placeholder="Enter the reason for rejection..."
                                required
                            ></textarea>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-x-lg me-1"></i>
                            Reject Request
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endif

</div>

@endsection
