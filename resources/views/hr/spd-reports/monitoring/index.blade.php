@extends('layouts.app')

@section('title', 'SPD Report Monitoring')

@section('content')

<div class="container-fluid">


{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-clipboard-data me-2"></i>
            SPD Report Monitoring
        </h4>

        <p class="text-muted mb-0">
            Monitoring SPD Report yang telah disetujui oleh Manager.
        </p>
    </div>
</div>


{{-- Success Message --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>
    </div>
@endif


{{-- Error Message --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>
    </div>
@endif


{{-- Monitoring Table --}}
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h6 class="mb-0 fw-semibold">
                    Approved SPD Reports
                </h6>

                <small class="text-muted">
                    Only SPD Reports approved by Manager are displayed.
                </small>
            </div>

            <span class="badge bg-success">
                {{ $reports->total() }} Report
                {{ $reports->total() !== 1 ? 's' : '' }}
            </span>

        </div>
    </div>


    <div class="card-body p-0">

        @if ($reports->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="text-nowrap px-3">
                                No. SPD
                            </th>

                            <th class="text-nowrap">
                                Employee
                            </th>

                            <th class="text-nowrap">
                                NIK
                            </th>

                            <th class="text-nowrap">
                                Project
                            </th>

                            <th class="text-nowrap">
                                Cost Center
                            </th>

                            <th class="text-nowrap">
                                Destination
                            </th>

                            <th class="text-nowrap">
                                Departure
                            </th>

                            <th class="text-nowrap">
                                Return
                            </th>

                            <th class="text-nowrap text-end">
                                Expense Report
                            </th>

                            <th class="text-nowrap text-center">
                                Status
                            </th>

                            <th class="text-nowrap">
                                Settlement
                            </th>

                            <th class="text-nowrap text-center px-3">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($reports as $report)

                            <tr>

                                {{-- SPD Number --}}
                                <td class="px-3">
                                    <span class="fw-semibold text-nowrap">
                                        {{ $report->spd?->spd_number ?? '-' }}
                                    </span>
                                </td>


                                {{-- Employee --}}
                                <td>
                                    <div class="fw-semibold text-nowrap">
                                        {{ $report->employee?->full_name ?? '-' }}
                                    </div>
                                </td>


                                {{-- NIK --}}
                                <td class="text-nowrap">
                                    {{ $report->employee?->nik ?? '-' }}
                                </td>


                                {{-- Project --}}
                                <td>
                                    <span class="text-nowrap">
                                        {{ $report->spd?->project?->name ?? '-' }}
                                    </span>
                                </td>


                                {{-- Cost Center --}}
                                <td class="text-nowrap">
                                    {{ $report->spd?->project?->cost_center ?? '-' }}
                                </td>


                                {{-- Destination --}}
                                <td>
                                    <span class="text-nowrap">
                                        {{ $report->spd?->destination ?? '-' }}
                                    </span>
                                </td>


                                {{-- Actual Departure --}}
                                <td class="text-nowrap">
                                    {{ $report->date_departure
                                        ? \Carbon\Carbon::parse($report->date_departure)->format('d M Y')
                                        : '-'
                                    }}
                                </td>


                                {{-- Actual Return --}}
                                <td class="text-nowrap">
                                    {{ $report->date_return
                                        ? \Carbon\Carbon::parse($report->date_return)->format('d M Y')
                                        : '-'
                                    }}
                                </td>


                                {{-- Expense Report --}}
                                <td class="text-end text-nowrap">

                                    @php
                                        $expenseReportTotal = (float) $report->expense_report_total;
                                    @endphp

                                    @if ($expenseReportTotal < 0)

                                        <span class="text-danger fw-semibold">
                                            Rp {{ number_format(abs($expenseReportTotal), 0, ',', '.') }}
                                        </span>

                                    @elseif ($expenseReportTotal > 0)

                                        <span class="text-success fw-semibold">
                                            Rp {{ number_format($expenseReportTotal, 0, ',', '.') }}
                                        </span>

                                    @else

                                        <span class="text-muted fw-semibold">
                                            Rp 0
                                        </span>

                                    @endif

                                </td>


                                {{-- Report Status --}}
                                <td class="text-center">

                                    @if ($report->status_report === 'approved')

                                        <span class="badge bg-success">
                                            Approved
                                        </span>

                                    @elseif ($report->status_report === 'settled')

                                        <span class="badge bg-primary">
                                            Settled
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ ucfirst($report->status_report) }}
                                        </span>

                                    @endif

                                </td>


                                {{-- Settlement --}}
                                <td class="text-nowrap">

                                    @switch($report->settlement_status)

                                        @case('reimburse')

                                            <span class="badge bg-danger">
                                                Reimburse
                                            </span>

                                            @break

                                        @case('cash_clear')

                                            <span class="badge bg-secondary">
                                                Cash Clear
                                            </span>

                                            @break

                                        @case('refund_employee')

                                            <span class="badge bg-warning text-dark">
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
                                <td class="text-center px-3">

                                    <a
                                        href="{{ route('spd-report-monitoring.show', $report->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View SPD Report"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">

                <div class="mb-3">
                    <i
                        class="bi bi-clipboard-x text-muted"
                        style="font-size: 3rem;"
                    ></i>
                </div>

                <h6 class="fw-semibold">
                    No Approved SPD Reports
                </h6>

                <p class="text-muted mb-0">
                    There are currently no SPD Reports approved by Manager.
                </p>

            </div>

        @endif

    </div>


    {{-- Pagination --}}
    @if ($reports->hasPages())

        <div class="card-footer bg-white border-top">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <small class="text-muted">
                    Showing
                    {{ $reports->firstItem() ?? 0 }}
                    to
                    {{ $reports->lastItem() ?? 0 }}
                    of
                    {{ $reports->total() }}
                    reports
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
                        @php
                            $currentPage = $reports->currentPage();
                            $lastPage = $reports->lastPage();

                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp


                        @if ($startPage > 1)

                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="{{ $reports->url(1) }}"
                                >
                                    1
                                </a>
                            </li>

                            @if ($startPage > 2)

                                <li class="page-item disabled">
                                    <span class="page-link">
                                        ...
                                    </span>
                                </li>

                            @endif

                        @endif


                        @for ($page = $startPage; $page <= $endPage; $page++)

                            <li
                                class="page-item {{ $page == $currentPage ? 'active' : '' }}"
                            >
                                <a
                                    class="page-link"
                                    href="{{ $reports->url($page) }}"
                                >
                                    {{ $page }}
                                </a>
                            </li>

                        @endfor


                        @if ($endPage < $lastPage)

                            @if ($endPage < $lastPage - 1)

                                <li class="page-item disabled">
                                    <span class="page-link">
                                        ...
                                    </span>
                                </li>

                            @endif

                            <li class="page-item">
                                <a
                                    class="page-link"
                                    href="{{ $reports->url($lastPage) }}"
                                >
                                    {{ $lastPage }}
                                </a>
                            </li>

                        @endif


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
