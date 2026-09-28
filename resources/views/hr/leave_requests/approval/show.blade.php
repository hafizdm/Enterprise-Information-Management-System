@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Leave Request Detail</h1>

            <p class="text-muted mb-0">
                Review employee leave request.
            </p>
        </div>

        <div>
            <a
                href="{{ route('leave-approvals.index') }}"
                class="btn btn-secondary"
            >
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Employee
                    </label>

                    <div>
                        {{ $leaveRequest->employee->full_name }}
                    </div>

                </div>

                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Leave Type
                    </label>

                    <div>
                        {{ match($leaveRequest->leave_type) {
                            'annual' => 'Annual Leave',
                            'sick' => 'Sick Leave',
                            'hajj' => 'Hajj Leave',
                            'site' => 'Site Leave',
                            'demobilization' => 'Demobilization Leave',
                            default => ucfirst($leaveRequest->leave_type),
                        } }}
                    </div>

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-4">

                    <label class="form-label text-muted">
                        First Date
                    </label>

                    <div>
                        {{ $leaveRequest->first_date->format('d M Y') }}
                    </div>

                </div>

                <div class="col-md-4">

                    <label class="form-label text-muted">
                        Last Date
                    </label>

                    <div>
                        {{ $leaveRequest->last_date->format('d M Y') }}
                    </div>

                </div>

                <div class="col-md-4">

                    <label class="form-label text-muted">
                        Total Days
                    </label>

                    <div>
                        {{ $leaveRequest->total_days }} day(s)
                    </div>

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label text-muted">
                    Reason
                </label>

                <div>
                    {{ $leaveRequest->reason }}
                </div>

            </div>

            <div class="mb-3">

                <label class="form-label text-muted">
                    Manager
                </label>

                <div>
                    {{ $leaveRequest->manager->full_name }}
                </div>

            </div>

            <div class="mb-3">

                <label class="form-label text-muted">
                    Status
                </label>

                <div>

                    @if($leaveRequest->status === 'pending_manager')

                        <span class="badge bg-warning text-dark">
                            Pending Manager
                        </span>

                    @elseif($leaveRequest->status === 'pending_hr')

                        <span class="badge bg-info text-dark">
                            Pending HR
                        </span>

                    @elseif($leaveRequest->status === 'approved')

                        <span class="badge bg-success">
                            Approved
                        </span>

                    @elseif($leaveRequest->status === 'rejected')

                        <span class="badge bg-danger">
                            Rejected
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            {{ ucfirst($leaveRequest->status) }}
                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@if($leaveRequest->status === 'pending_manager')

    <div class="mt-4 d-flex gap-2">

        {{-- Approve --}}

        <form
            action="{{ route('leave-approvals.approve', $leaveRequest) }}"
            method="POST"
        >
            @csrf

            <button
                type="submit"
                class="btn btn-success"
            >
                <i class="bi bi-check-lg"></i>
                Approve
            </button>

        </form>


        {{-- Reject --}}

        <button
            type="button"
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#rejectLeaveModal"
        >
            <i class="bi bi-x-lg"></i>
            Reject
        </button>

    </div>


    {{-- Reject Modal --}}

    <div
        class="modal fade"
        id="rejectLeaveModal"
        tabindex="-1"
        aria-labelledby="rejectLeaveModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog">

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

                        <div class="mb-3">

                            <label
                                for="manager_rejection_reason"
                                class="form-label"
                            >
                                Rejection Reason
                            </label>

                            <textarea
                                name="manager_rejection_reason"
                                id="manager_rejection_reason"
                                class="form-control"
                                rows="4"
                                required
                            ></textarea>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Reject Request
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endif

@endsection