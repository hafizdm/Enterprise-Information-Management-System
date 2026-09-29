@extends('layouts.app')

@section('content')

<div class="container-fluid">


{{-- PAGE HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">Leave Monitoring</h1>

        <p class="text-muted mb-0">
            Monitor employee leave requests.
        </p>
    </div>

</div>


{{-- SUCCESS MESSAGE --}}
@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif


{{-- LEAVE MONITORING TABLE --}}
<div class="card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Employee</th>

                        <th>Leave Type</th>

                        <th>Date</th>

                        <th>Days</th>

                        <th>Reason</th>

                        <th>Manager</th>

                        <th>Status</th>

                        <th class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($leaveRequests as $leave)

                        <tr>

                            {{-- EMPLOYEE --}}
                            <td>

                                <div class="fw-semibold">
                                    {{ $leave->employee?->full_name ?? '-' }}
                                </div>

                                <small class="text-muted">
                                    {{ $leave->employee?->nik ?? '-' }}
                                </small>

                            </td>


                            {{-- LEAVE TYPE --}}
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
                                        {{ ucfirst($leave->leave_type) }}

                                @endswitch

                            </td>


                            {{-- DATE --}}
                            <td>

                                <div>
                                    {{ $leave->first_date->format('d M Y') }}
                                </div>

                                <small class="text-muted">
                                    to {{ $leave->last_date->format('d M Y') }}
                                </small>

                            </td>


                            {{-- TOTAL DAYS --}}
                            <td>
                                {{ $leave->total_days }}
                            </td>


                            {{-- REASON --}}
                            <td style="min-width: 180px; max-width: 250px;">

                                <span
                                    title="{{ $leave->reason }}"
                                >
                                    {{ \Illuminate\Support\Str::limit($leave->reason, 60) }}
                                </span>

                            </td>


                            {{-- MANAGER --}}
                            <td>
                                {{ $leave->manager?->full_name ?? '-' }}
                            </td>


                            {{-- STATUS --}}
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


                            {{-- ACTION --}}
                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-1">

                                    <a
                                        href="{{ route('leave-monitoring.show', $leave) }}"
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

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center text-muted py-4"
                            >
                                No leave requests found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        <div class="mt-3">

            {{ $leaveRequests->links() }}

        </div>

    </div>

</div>


</div>

@endsection
