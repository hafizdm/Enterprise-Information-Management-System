@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Leave Request Detail</h1>
            <p class="text-muted mb-0">
                View leave request information.
            </p>
        </div>

        <div>
            <a
                href="{{ route('leave-monitoring.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>
        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Employee
                    </label>

                    <div class="fw-semibold">
                        {{ $leaveRequest->employee?->full_name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Leave Type
                    </label>

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

                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        First Date
                    </label>

                    <div>
                        {{ $leaveRequest->first_date->format('d M Y') }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Last Date
                    </label>

                    <div>
                        {{ $leaveRequest->last_date->format('d M Y') }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Total Days
                    </label>

                    <div class="fw-semibold">
                        {{ $leaveRequest->total_days }} days
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Manager
                    </label>

                    <div>
                        {{ $leaveRequest->manager?->full_name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Status
                    </label>

                    <div>

                        @if($leaveRequest->status === 'pending_manager')

                            <span class="badge bg-warning text-dark">
                                Pending Manager
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

                <div class="col-12 mb-3">
                    <label class="text-muted small">
                        Reason
                    </label>

                    <div>
                        {{ $leaveRequest->reason ?: '-' }}
                    </div>
                </div>

                @if($leaveRequest->status === 'approved')

                    <div class="col-md-6 mb-3">

                        <label class="text-muted small">
                            Manager Approved At
                        </label>

                        <div>
                            {{ $leaveRequest->manager_approved_at?->format('d M Y H:i') ?? '-' }}
                        </div>

                    </div>

                @endif

                @if($leaveRequest->status === 'rejected')

                    <div class="col-12 mb-3">

                        <label class="text-muted small">
                            Manager Rejection Reason
                        </label>

                        <div class="alert alert-danger mb-0">
                            {{ $leaveRequest->manager_rejection_reason ?: '-' }}
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection