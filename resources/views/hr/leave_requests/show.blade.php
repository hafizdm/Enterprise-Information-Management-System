@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Leave Request Detail</h1>
            <p class="text-muted mb-0">
                View your leave request information.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('leave-requests.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </div>

    </div>


    {{-- Request Information --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Leave Request Information</h5>
        </div>

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-6">

                    <div class="text-muted small">
                        Leave Type
                    </div>

                    <div class="fw-semibold">

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


                <div class="col-md-6">

                    <div class="text-muted small">
                        Status
                    </div>

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

            </div>


            <div class="row mb-3">

                <div class="col-md-4">

                    <div class="text-muted small">
                        First Date
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->first_date->format('d M Y') }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Last Date
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->last_date->format('d M Y') }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Total Days
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->total_days }} day(s)
                    </div>

                </div>

            </div>


            <div>

                <div class="text-muted small mb-1">
                    Reason
                </div>

                <div>
                    {{ $leaveRequest->reason }}
                </div>

            </div>

        </div>

    </div>


    {{-- Employee Information --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Employee Information</h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <div class="text-muted small">
                        Employee ID
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->employee->nik ?? '-' }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Employee Name
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->employee->full_name ?? '-' }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Department
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->employee->division->name ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Manager Approval --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Manager Approval</h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <div class="text-muted small">
                        Manager
                    </div>

                    <div class="fw-semibold">
                        {{ $leaveRequest->manager->full_name ?? '-' }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Approval Status
                    </div>

                    <div>

                        @if($leaveRequest->status === 'approved')

                            <span class="badge bg-success">
                                Approved
                            </span>

                        @elseif($leaveRequest->status === 'rejected')

                            <span class="badge bg-danger">
                                Rejected
                            </span>

                        @else

                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>

                        @endif

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Approved At
                    </div>

                    <div class="fw-semibold">

                        @if($leaveRequest->manager_approved_at)

                            {{ $leaveRequest->manager_approved_at->format('d M Y H:i') }}

                        @else

                            -

                        @endif

                    </div>

                </div>

            </div>


            @if($leaveRequest->status === 'rejected')

                <div class="mt-4">

                    <div class="text-muted small mb-1">
                        Rejection Reason
                    </div>

                    <div class="alert alert-danger mb-0">
                        {{ $leaveRequest->manager_rejection_reason ?? '-' }}
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- Actions --}}
    <div class="d-flex justify-content-end gap-2">

        @if($leaveRequest->status === 'pending_manager')

            <form
                action="{{ route('leave-requests.destroy', $leaveRequest) }}"
                method="POST"
                onsubmit="return confirm('Are you sure you want to delete this leave request?');"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-outline-danger"
                >
                    <i class="bi bi-trash"></i>
                    Delete Request
                </button>

            </form>

        @endif

    </div>

</div>

@endsection