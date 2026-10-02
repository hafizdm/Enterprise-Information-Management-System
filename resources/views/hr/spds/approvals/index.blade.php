@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">SPD Approvals</h4>

        <p class="text-muted mb-0">
            Review business trip requests that require your approval
        </p>
    </div>

</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle me-1"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close">
        </button>

    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="bi bi-exclamation-circle me-1"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close">
        </button>

    </div>
@endif


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h6 class="mb-1 fw-semibold">
                    Pending SPD Approvals
                </h6>

                <small class="text-muted">
                    {{ $spds->total() }}
                    request{{ $spds->total() != 1 ? 's' : '' }}
                    require{{ $spds->total() == 1 ? 's' : '' }}
                    your approval
                </small>

            </div>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0 text-nowrap">

                <thead class="table-light">

                    <tr>

                        <th class="ps-4" style="width: 60px;">
                            #
                        </th>

                        <th>
                            Employee
                        </th>

                        <th>
                            NIK
                        </th>

                        <th>
                            Project
                        </th>

                        <th>
                            Cost Center
                        </th>

                        <th>
                            Destination
                        </th>

                        <th>
                            Travel Date
                        </th>

                        <th class="text-end">
                            Balance Received
                        </th>

                        <th>
                            Approval Stage
                        </th>

                        <th class="text-end pe-4">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($spds as $spd)

                        <tr>

                            <td class="ps-4 text-muted">
                                {{ $spds->firstItem() + $loop->index }}
                            </td>


                            <td>

                                <span class="fw-semibold">
                                    {{ $spd->employee->full_name }}
                                </span>

                            </td>


                            <td>

                                <span class="text-muted">
                                    {{ $spd->employee->nik ?? '-' }}
                                </span>

                            </td>


                            <td>

                                <span class="fw-semibold">
                                    {{ $spd->project->name }}
                                </span>

                            </td>


                            <td>

                                <span class="text-muted">
                                    {{ $spd->project->cost_center ?? '-' }}
                                </span>

                            </td>


                            <td>
                                {{ $spd->destination }}
                            </td>


                            <td>

                                <div>
                                    {{ $spd->date_departure->format('d M Y') }}
                                </div>

                                <small class="text-muted">
                                    to
                                    {{ $spd->date_return->format('d M Y') }}
                                </small>

                            </td>


                            <td class="text-end">

                                <span class="fw-semibold">
                                    Rp {{ number_format($spd->balance_received, 0, ',', '.') }}
                                </span>

                            </td>


                            <td>

                                @if ($spd->status === 'pending_manager')

                                    <span class="badge bg-warning text-dark">

                                        <i class="bi bi-person-check me-1"></i>

                                        Manager Approval

                                    </span>

                                @elseif ($spd->status === 'pending_document')

                                    <span class="badge bg-warning text-dark">

                                        <i class="bi bi-file-earmark-check me-1"></i>

                                        Cost Control Approval

                                    </span>

                                @else

                                    <span class="badge bg-secondary">

                                        {{ ucfirst(str_replace('_', ' ', $spd->status)) }}

                                    </span>

                                @endif

                            </td>


                            <td class="text-end pe-4">

                                <a
                                    href="{{ route('spd.approvals.show', $spd) }}"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye me-1"></i>

                                    Review

                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="10" class="text-center py-5">

                                <div class="text-muted">

                                    <i class="bi bi-check2-circle fs-1 d-block mb-3"></i>

                                    <h6 class="mb-1">
                                        No pending approvals
                                    </h6>

                                    <p class="mb-0">
                                        There are no SPD requests requiring your approval.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if ($spds->hasPages())

        <div class="card-footer bg-white border-0 py-3">

            <div class="d-flex justify-content-end">

                {{ $spds->links() }}

            </div>

        </div>

    @endif

</div>

@endsection