
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

            <a
                href="{{ route('spd-reports.create') }}"
                class="btn btn-primary"
            >
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
                        Rejected reports can be resubmitted using the same SPD number.
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

                    {{ $reports->total() }}

                    Report{{ $reports->total() !== 1 ? 's' : '' }}

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($reports->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0 text-nowrap">

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

                                    {{-- SPD Number --}}
                                    <td class="px-3">

                                        <div class="fw-semibold text-primary">
                                            {{ $report->spd?->spd_number ?? '-' }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $report->spd?->travel_type
                                                ? ucfirst($report->spd->travel_type)
                                                : '-' }}
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

                                            Rp
                                            {{ number_format(abs($expenseTotal), 0, ',', '.') }}

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

                                            @case('submitted')

                                                <span class="badge bg-primary">

                                                    <i class="bi bi-clock me-1"></i>
                                                    Submitted

                                                </span>

                                                @break


                                            @case('approved')

                                                <span class="badge bg-success">

                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Approved

                                                </span>

                                                @break


                                            @case('rejected')

                                                <span class="badge bg-danger">

                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Rejected

                                                </span>

                                                @break


                                            @case('settled')

                                                <span class="badge bg-success">

                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Settled

                                                </span>

                                                @break


                                            @default

                                                <span class="badge bg-light text-dark border">

                                                    {{ ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $report->status_report ?? '-'
                                                        )
                                                    ) }}

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


        {{-- Pagination --}}
        @if ($reports->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <small class="text-muted">

                        Showing
                        <strong>{{ $reports->firstItem() }}</strong>
                        to
                        <strong>{{ $reports->lastItem() }}</strong>
                        of
                        <strong>{{ $reports->total() }}</strong>
                        results

                    </small>


                    <nav aria-label="SPD Report pagination">

                        <ul class="pagination pagination-sm mb-0">

                            {{-- Previous --}}
                            @if ($reports->onFirstPage())

                                <li class="page-item disabled">

                                    <span class="page-link">

                                        <i class="bi bi-chevron-left"></i>

                                    </span>

                                </li>

                            @else

                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="{{ $reports->previousPageUrl() }}"
                                        aria-label="Previous"
                                    >

                                        <i class="bi bi-chevron-left"></i>

                                    </a>

                                </li>

                            @endif


                            {{-- Page Numbers --}}
                            @foreach ($reports->getUrlRange(
                                max(1, $reports->currentPage() - 2),
                                min($reports->lastPage(), $reports->currentPage() + 2)
                            ) as $page => $url)

                                @if ($page == $reports->currentPage())

                                    <li
                                        class="page-item active"
                                        aria-current="page"
                                    >

                                        <span class="page-link">
                                            {{ $page }}
                                        </span>

                                    </li>

                                @else

                                    <li class="page-item">

                                        <a
                                            class="page-link"
                                            href="{{ $url }}"
                                        >
                                            {{ $page }}
                                        </a>

                                    </li>

                                @endif

                            @endforeach


                            {{-- Next --}}
                            @if ($reports->hasMorePages())

                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="{{ $reports->nextPageUrl() }}"
                                        aria-label="Next"
                                    >

                                        <i class="bi bi-chevron-right"></i>

                                    </a>

                                </li>

                            @else

                                <li class="page-item disabled">

                                    <span class="page-link">

                                        <i class="bi bi-chevron-right"></i>

                                    </span>

                                </li>

                            @endif

                        </ul>

                    </nav>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection
