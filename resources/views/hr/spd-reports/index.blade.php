@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-receipt me-2"></i>
                My SPD Report
            </h4>

            <p class="text-muted mb-0">
                View and submit your business trip expense reports.
            </p>
        </div>

        @can('spd-report.create')
            <a href="{{ route('spd-reports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Create SPD Report
            </a>
        @endcan

    </div>


    {{-- Information Card --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex align-items-start gap-3">

                <div class="text-primary fs-4">
                    <i class="bi bi-info-circle"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-1">
                        SPD Report Information
                    </h6>

                    <p class="text-muted small mb-0">
                        Only SPD requests that have been fully approved are available
                        for expense reporting. Each approved SPD can have one report only.
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- Report List --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="fw-bold mb-0">
                        SPD Report History
                    </h6>

                    <small class="text-muted">
                        Your submitted business trip expense reports
                    </small>
                </div>

                <span class="badge bg-light text-dark border">
                    {{ $reports->count() }} Report{{ $reports->count() !== 1 ? 's' : '' }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($reports->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="px-3 py-3">
                                    No. SPD
                                </th>

                                <th>
                                    Project
                                </th>

                                <th>
                                    Destination
                                </th>

                                <th>
                                    Actual Departure
                                </th>

                                <th>
                                    Actual Return
                                </th>

                                <th class="text-end">
                                    Expense Report
                                </th>

                                <th>
                                    Report Status
                                </th>

                                <th>
                                    Settlement
                                </th>

                                <th class="text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($reports as $report)

                                <tr>

                                    {{-- SPD --}}
                                    <td class="px-3">

                                        <div class="fw-semibold">
                                            SPD #{{ $report->spd_id }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $report->spd?->travel_type ? ucfirst($report->spd->travel_type) : '-' }}
                                        </small>

                                    </td>


                                    {{-- Project --}}
                                    <td>

                                        <div class="fw-semibold">
                                            {{ $report->spd?->project?->name ?? '-' }}
                                        </div>

                                        @if($report->spd?->project?->cost_center)
                                            <small class="text-muted">
                                                Cost Center:
                                                {{ $report->spd->project->cost_center }}
                                            </small>
                                        @endif

                                    </td>


                                    {{-- Destination --}}
                                    <td>

                                        <div>
                                            {{ $report->spd?->from ?? '-' }}
                                        </div>

                                        <small class="text-muted">
                                            <i class="bi bi-arrow-right mx-1"></i>
                                            {{ $report->spd?->destination ?? '-' }}
                                        </small>

                                    </td>


                                    {{-- Actual Departure --}}
                                    <td>

                                        {{ $report->date_departure?->format('d M Y') ?? '-' }}

                                    </td>


                                    {{-- Actual Return --}}
                                    <td>

                                        {{ $report->date_return?->format('d M Y') ?? '-' }}

                                    </td>


                                    {{-- Expense Report Total --}}
                                    <td class="text-end">

                                        @php
                                            $expenseTotal = (float) $report->expense_report_total;
                                        @endphp

                                        <span class="fw-semibold">
                                            Rp {{ number_format(abs($expenseTotal), 0, ',', '.') }}
                                        </span>

                                        <div class="small text-muted">
                                            @if($expenseTotal < 0)
                                                Company owes employee
                                            @elseif($expenseTotal > 0)
                                                Employee returns balance
                                            @else
                                                No balance
                                            @endif
                                        </div>

                                    </td>


                                    {{-- Report Status --}}
                                    <td>

                                        @switch($report->status_report)

                                            @case('draft')
                                                <span class="badge bg-secondary">
                                                    Draft
                                                </span>
                                                @break

                                            @case('submitted')
                                                <span class="badge bg-primary">
                                                    Submitted
                                                </span>
                                                @break

                                            @case('reviewed')
                                                <span class="badge bg-info text-dark">
                                                    Reviewed
                                                </span>
                                                @break

                                            @case('settled')
                                                <span class="badge bg-success">
                                                    Settled
                                                </span>
                                                @break

                                            @default
                                                <span class="badge bg-light text-dark border">
                                                    {{ ucfirst($report->status_report ?? '-') }}
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- Settlement Status --}}
                                    <td>

                                        @switch($report->settlement_status)

                                            @case('reimburse')
                                                <span class="badge bg-warning text-dark">
                                                    Reimburse
                                                </span>
                                                @break

                                            @case('cash_clear')
                                                <span class="badge bg-success">
                                                    Cash Clear
                                                </span>
                                                @break

                                            @case('refund_employee')
                                                <span class="badge bg-danger">
                                                    Refund Employee
                                                </span>
                                                @break

                                            @default
                                                <span class="text-muted">
                                                    -
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- Action --}}
                                    <td class="text-center">

                                        <a
                                            href="{{ route('spd-reports.show', $report) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Report"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- Empty State --}}
                <div class="text-center py-5 px-3">

                    <div class="mb-3">

                        <i class="bi bi-receipt-cutoff display-4 text-muted"></i>

                    </div>

                    <h5 class="fw-semibold">
                        No SPD Reports Yet
                    </h5>

                    <p class="text-muted mb-4">
                        You don't have any submitted SPD reports yet.
                        Once your SPD has been fully approved, you can create
                        an expense report from it.
                    </p>

                    @can('spd-report.create')

                        <a
                            href="{{ route('spd-reports.create') }}"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-plus-lg me-1"></i>
                            Create SPD Report
                        </a>

                    @endcan

                </div>

            @endif

        </div>

    </div>

</div>

@endsection