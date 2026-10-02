@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">SPD Management</h4>

        <p class="text-muted mb-0">
            Manage employee business trip requests
        </p>
    </div>

    <div>
        @can('spd.create')
            <a href="{{ route('spds.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Create SPD
            </a>
        @endcan
    </div>

</div>

{{-- Success Message --}}
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

{{-- Error Message --}}
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

    {{-- Card Header --}}
    <div class="card-header bg-white border-0 py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h6 class="mb-1 fw-semibold">
                    Business Trip Requests
                </h6>

                <small class="text-muted">
                    {{ $spds->total() }} SPD request{{ $spds->total() != 1 ? 's' : '' }}
                </small>

            </div>

        </div>

    </div>

    <div class="card-body p-0">

        {{-- Horizontal Scroll --}}
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
                            From
                        </th>

                        <th>
                            Destination
                        </th>

                        <th>
                            Departure
                        </th>

                        <th>
                            Return
                        </th>

                        <th class="text-center">
                            Days
                        </th>

                        <th class="text-end">
                            Balance Received
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-end pe-4">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($spds as $spd)

                        <tr>

                            {{-- Number --}}
                            <td class="ps-4 text-muted">

                                {{ $spds->firstItem() + $loop->index }}

                            </td>

                            {{-- Employee --}}
                            <td>

                                <span class="fw-semibold">
                                    {{ $spd->employee->full_name }}
                                </span>

                            </td>

                            {{-- NIK --}}
                            <td>

                                <span class="text-muted">
                                    {{ $spd->employee->nik ?? '-' }}
                                </span>

                            </td>

                            {{-- Project --}}
                            <td>

                                <span class="fw-semibold">
                                    {{ $spd->project->name }}
                                </span>

                            </td>

                            {{-- Cost Center --}}
                            <td>

                                <span class="text-muted">
                                    {{ $spd->project->cost_center ?? '-' }}
                                </span>

                            </td>

                            {{-- From --}}
                            <td>

                                {{ $spd->from }}

                            </td>

                            {{-- Destination --}}
                            <td>

                                {{ $spd->destination }}

                            </td>

                            {{-- Departure --}}
                            <td>

                                {{ $spd->date_departure->format('d M Y') }}

                            </td>

                            {{-- Return --}}
                            <td>

                                {{ $spd->date_return->format('d M Y') }}

                            </td>

                            {{-- Total Days --}}
                            <td class="text-center">

                                <span class="badge bg-light text-dark border">

                                    {{ $spd->total_days }}

                                    {{ $spd->total_days == 1 ? 'day' : 'days' }}

                                </span>

                            </td>

                            {{-- Balance Received --}}
                            <td class="text-end">

                                <span class="fw-semibold">

                                    Rp {{ number_format($spd->balance_received, 0, ',', '.') }}

                                </span>

                            </td>

                            {{-- Status --}}
                            <td>

                                @if ($spd->status === 'pending_manager')

                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>
                                        Pending Manager
                                    </span>

                                @elseif ($spd->status === 'pending_document')

                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>
                                        Pending Cost Control
                                    </span>

                                @elseif ($spd->status === 'approved')

                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Approved
                                    </span>

                                @elseif ($spd->status === 'rejected')

                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Rejected
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $spd->status)) }}
                                    </span>

                                @endif

                            </td>

                            {{-- Action --}}
                            <td class="text-end pe-4">

                                <div class="d-flex justify-content-end gap-2">

                                    {{-- View --}}
                                    <a
                                        href="{{ route('spds.show', $spd) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View SPD">

                                        <i class="bi bi-eye me-1"></i>
                                        View

                                    </a>

                                    {{-- Delete --}}
                                    @if ($spd->status === 'pending_manager')

                                        @can('spd.delete')

                                            <form
                                                action="{{ route('spds.destroy', $spd) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this SPD? The employee SPD limit will be returned.');"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete SPD">

                                                    <i class="bi bi-trash me-1"></i>
                                                    Delete

                                                </button>

                                            </form>

                                        @endcan

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="13"
                                class="text-center py-5">

                                <div class="text-muted">

                                    <i
                                        class="bi bi-file-earmark-text fs-1 d-block mb-3">
                                    </i>

                                    <h6 class="mb-1">
                                        No SPD requests found
                                    </h6>

                                    <p class="mb-0">
                                        There are no business trip requests available yet.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    @if ($spds->hasPages())

        <div class="card-footer bg-white border-0 py-3">

            <div class="d-flex justify-content-end">

                {{ $spds->links() }}

            </div>

        </div>

    @endif

</div>

@endsection