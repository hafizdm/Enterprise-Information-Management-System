@extends('layouts.app')

@section('content')

<div class="container-fluid">


{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">Leave Request Detail</h1>

        <p class="text-muted mb-0">
            View employee leave request information and approval status.
        </p>
    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('leave-monitoring.index') }}"
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


            {{-- Status --}}
            <div class="col-md-4 mb-4">

                <div class="text-muted small mb-1">
                    Status
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
                    Manager
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

                    @if($leaveRequest->status === 'approved')

                        <span class="badge bg-success">
                            Approved
                        </span>

                        <div class="small text-muted mt-2">
                            Approved at
                            {{ $leaveRequest->manager_approved_at?->format('d M Y H:i') ?? '-' }}
                        </div>

                    @elseif($leaveRequest->status === 'rejected')

                        <span class="badge bg-danger">
                            Rejected
                        </span>

                    @else

                        <span class="badge bg-warning text-dark">
                            Pending Approval
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


{{-- HRD Monitoring --}}
<div class="card mb-4">

    <div class="card-header bg-white py-3">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-eye me-2"></i>
            HRD Monitoring
        </h6>

    </div>

    <div class="card-body">

        <div class="d-flex align-items-center">

            <div class="me-3">

                <span class="badge bg-info text-dark px-3 py-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Monitoring
                </span>

            </div>

            <div class="text-muted">
                This leave request is available for HRD monitoring.
            </div>

        </div>

    </div>

</div>


</div>

@endsection
