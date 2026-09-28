@extends('layouts.app')

@section('content')

<div class="container-fluid">


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">My Leave</h1>
        <p class="text-muted mb-0">
            View your leave requests.
        </p>
    </div>
    

    <div>
        <a
            href="{{ route('leave-requests.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Leave Request
        </a>
    </div>

</div>

<div class="row mb-4">

    <div class="col-md-4">

        <div class="card">

            <div class="card-body">

                <div class="text-muted mb-1">
                    Remaining Annual Leave
                </div>

                <h3 class="mb-1">
                    {{ $remainingAnnualLeave }} days
                </h3>

                <small class="text-muted">
                    Available annual leave balance
                </small>

            </div>

        </div>

    </div>

</div>


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


<div class="card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Leave Type</th>
                        <th>First Date</th>
                        <th>Last Date</th>
                        <th>Total Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($leaveRequests as $leave)

                        <tr>

                            <td>
                                {{ match($leave->leave_type) {
                                    'annual' => 'Annual Leave',
                                    'sick' => 'Sick Leave',
                                    'hajj' => 'Hajj Leave',
                                    'site' => 'Site Leave',
                                    'demobilization' => 'Demobilization Leave',
                                    default => ucfirst($leave->leave_type),
                                } }}
                            </td>

                            <td>
                                {{ $leave->first_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $leave->last_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $leave->total_days }} day(s)
                            </td>

                            <td>
                                {{ $leave->reason }}
                            </td>

                            <td>

                                @if($leave->status === 'pending_manager')

                                    <span class="badge bg-warning text-dark">
                                        Pending Manager
                                    </span>

                                @elseif($leave->status === 'pending_hr')

                                    <span class="badge bg-info text-dark">
                                        Pending HR
                                    </span>

                                @elseif($leave->status === 'approved')

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                @elseif($leave->status === 'rejected')

                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ ucfirst($leave->status) }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center text-muted py-4"
                            >
                                You do not have any leave requests yet.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-3">

            {{ $leaveRequests->links() }}

        </div>

    </div>

</div>


</div>

@endsection
