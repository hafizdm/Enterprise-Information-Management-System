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
    

    

</div>

<div class="d-flex justify-content-between align-items-center mb-3">

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
                        <th>Action</th>
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

                                <div class="d-flex gap-2">

                                    <a
                                        href="{{ route('leave-requests.show', $leave) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-eye"></i>
                                        View
                                    </a>

                                    <a
                                        href="{{ route('leave-requests.pdf', $leave) }}"
                                        class="btn btn-sm btn-outline-secondary"
                                        target="_blank"
                                    >
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        PDF
                                    </a>

                                    @if($leave->status === 'pending_manager')

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteLeaveModal{{ $leave->id }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                            Delete
                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @if($leave->status === 'pending_manager')

                        <div
                            class="modal fade"
                            id="deleteLeaveModal{{ $leave->id }}"
                            tabindex="-1"
                            aria-labelledby="deleteLeaveModalLabel{{ $leave->id }}"
                            aria-hidden="true"
                        >

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5
                                            class="modal-title"
                                            id="deleteLeaveModalLabel{{ $leave->id }}"
                                        >
                                            Delete Leave Request?
                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"
                                        ></button>

                                    </div>


                                    <div class="modal-body">

                                        <p class="mb-2">
                                            Are you sure you want to delete this leave request?
                                        </p>

                                        <p class="text-muted small mb-0">
                                            This action cannot be undone.
                                        </p>

                                    </div>


                                    <div class="modal-footer">

                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal"
                                        >
                                            Cancel
                                        </button>


                                        <form
                                            action="{{ route('leave-requests.destroy', $leave) }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                            >
                                                <i class="bi bi-trash"></i>
                                                Delete Request
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                    @empty

                        <tr>

                            <td
                                colspan="7"
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
