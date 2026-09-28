@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Leave Monitoring</h1>
            <p class="text-muted mb-0">
                Monitor employee leave requests.
            </p>
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
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>First Date</th>
                            <th>Last Date</th>
                            <th>Total Days</th>
                            <th>Reason</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($leaveRequests as $leave)

                            <tr>

                                <td>
                                    {{ $leave->employee?->full_name ?? '-' }}
                                </td>

                                <td>
                                    @switch($leave->leave_type)

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
                                            {{ $leave->leave_type }}

                                    @endswitch
                                </td>

                                <td>
                                    {{ $leave->first_date->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $leave->last_date->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $leave->total_days }}
                                </td>

                                <td>
                                    {{ $leave->reason }}
                                </td>

                                <td>
                                    {{ $leave->manager?->full_name ?? '-' }}
                                </td>

                                <td>

                                    @if($leave->status === 'pending_manager')

                                        <span class="badge bg-warning text-dark">
                                            Pending Manager
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

                                <td>
                                    <a
                                        href="{{ route('leave-monitoring.show', $leave) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        View
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No leave requests found.
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