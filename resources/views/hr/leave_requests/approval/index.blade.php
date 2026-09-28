@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Leave Approval</h1>

            <p class="text-muted mb-0">
                Review leave requests from your employees.
            </p>
        </div>

    </div>

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
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($leaveRequests as $leave)

                            <tr>

                                <td>
                                    {{ $leave->employee->full_name }}
                                </td>

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
                                    <span class="badge bg-warning text-dark">
                                        Pending Manager
                                    </span>
                                </td>

                                <td>
                                    <a
                                        href="{{ route('leave-approvals.show', $leave) }}"
                                        class="btn btn-sm btn-primary"
                                    >
                                        View
                                    </a>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="8"
                                    class="text-center text-muted py-4"
                                >
                                    There are no leave requests waiting for your approval.
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