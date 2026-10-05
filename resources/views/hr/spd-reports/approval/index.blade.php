@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <h1 class="h3 fw-bold mb-1">
                SPD Report Approval
            </h1>

            <p class="text-muted mb-0">
                Review and approve SPD reports submitted by your subordinates.
            </p>
        </div>

        <div>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">
                <i class="bi bi-hourglass-split me-1"></i>
                Pending Manager Approval
            </span>
        </div>

    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif


    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Please check the following errors:
            </div>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Approval Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1 fw-semibold">
                        Reports Waiting for Approval
                    </h5>

                    <small class="text-muted">
                        Only submitted reports from your direct subordinates are displayed.
                    </small>
                </div>

                <span class="badge bg-primary-subtle text-primary-emphasis">
                    {{ $reports->total() }} Report{{ $reports->total() !== 1 ? 's' : '' }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if($reports->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="px-3 py-3">
                                    Employee
                                </th>

                                <th class="py-3">
                                    Project
                                </th>

                                <th class="py-3">
                                    Period
                                </th>

                                <th class="py-3 text-center">
                                    Days
                                </th>

                                <th class="py-3 text-end">
                                    Expense Balance
                                </th>

                                <th class="py-3 text-end">
                                    Settlement
                                </th>

                                <th class="py-3">
                                    Submitted
                                </th>

                                <th class="py-3 text-center">
                                    Action
                                </th>
                            </tr>

                        </thead>


                        <tbody>

                            @foreach($reports as $report)

                                <tr>

                                    {{-- Employee --}}
                                    <td class="px-3">

                                        <div class="fw-semibold">
                                            {{ $report->employee->full_name ?? '-' }}
                                        </div>

                                        @if($report->employee?->nik)
                                            <small class="text-muted">
                                                NIK: {{ $report->employee->nik }}
                                            </small>
                                        @endif

                                    </td>


                                    {{-- Project --}}
                                    <td>

                                        <div class="fw-semibold">
                                            {{ $report->spd->project->name ?? '-' }}
                                        </div>

                                        @if($report->spd->project?->cost_center)
                                            <small class="text-muted">
                                                {{ $report->spd->project->cost_center }}
                                            </small>
                                        @endif

                                    </td>


                                    {{-- Period --}}
                                    <td>

                                        <div>
                                            {{ optional($report->date_departure)->format('d M Y') }}
                                        </div>

                                        <small class="text-muted">
                                            to
                                            {{ optional($report->date_return)->format('d M Y') }}
                                        </small>

                                    </td>


                                    {{-- Days --}}
                                    <td class="text-center">

                                        <span class="badge bg-light text-dark border">
                                            {{ $report->total_days }} day{{ $report->total_days != 1 ? 's' : '' }}
                                        </span>

                                    </td>


                                    {{-- Expense Balance --}}
                                    <td class="text-end">

                                        <span class="fw-semibold">
                                            Rp {{ number_format((float) $report->expense_balance, 0, ',', '.') }}
                                        </span>

                                    </td>


                                    {{-- Settlement --}}
                                    <td class="text-end">

                                        @if($report->settlement_status === 'reimburse')

                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                                Reimburse
                                            </span>

                                        @elseif($report->settlement_status === 'refund_employee')

                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                Refund Employee
                                            </span>

                                        @elseif($report->settlement_status === 'cash_clear')

                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                                Cash Clear
                                            </span>

                                        @else

                                            <span class="text-muted">
                                                -

                                            </span>

                                        @endif

                                    </td>


                                    {{-- Submitted --}}
                                    <td>

                                        @if($report->submitted_at)

                                            <div>
                                                {{ $report->submitted_at->format('d M Y') }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $report->submitted_at->format('H:i') }}
                                            </small>

                                        @else

                                            <span class="text-muted">
                                                -
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="text-center">

                                        <a
                                            href="{{ route('spd-report-approvals.show', $report) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            Review
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

                        <i
                            class="bi bi-clipboard-check text-muted"
                            style="font-size: 3rem;"
                        ></i>

                    </div>

                    <h5 class="fw-semibold">
                        No SPD Reports Pending Approval
                    </h5>

                    <p class="text-muted mb-0">
                        There are currently no submitted SPD reports
                        waiting for your approval.
                    </p>

                </div>

            @endif

        </div>


        {{-- Pagination --}}
        @if($reports->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                {{ $reports->links() }}

            </div>

        @endif

    </div>

</div>

@endsection

