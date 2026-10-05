@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <div class="mb-2">
                <a
                    href="{{ route('spd-report-approvals.index') }}"
                    class="text-decoration-none text-muted"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Approval List
                </a>
            </div>

            <h1 class="h3 fw-bold mb-1">
                SPD Report Review
            </h1>

            <p class="text-muted mb-0">
                Review the employee's SPD report before approving or rejecting it.
            </p>
        </div>

        <div>

            @if($spdReport->status_report === 'submitted')

                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Pending Approval
                </span>

            @elseif($spdReport->status_report === 'approved')

                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i>
                    Approved
                </span>

            @elseif($spdReport->status_report === 'rejected')

                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-3 py-2">
                    <i class="bi bi-x-circle me-1"></i>
                    Rejected
                </span>

            @else

                <span class="badge bg-secondary-subtle text-secondary-emphasis border px-3 py-2">
                    {{ ucfirst($spdReport->status_report) }}
                </span>

            @endif

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


    {{-- ================================================================ --}}
    {{-- ORIGINAL SPD INFORMATION --}}
    {{-- ================================================================ --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="fw-semibold mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                Original SPD Information
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- Employee --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Employee
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->employee->full_name ?? '-' }}
                    </div>

                    @if($spdReport->employee?->nik)

                        <small class="text-muted">
                            NIK: {{ $spdReport->employee->nik }}
                        </small>

                    @endif

                </div>


                {{-- Project --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Project
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->project->name ?? '-' }}
                    </div>

                </div>


                {{-- Cost Center --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Cost Center
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->project->cost_center ?? '-' }}
                    </div>

                </div>


                {{-- Manager --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Manager
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->manager->full_name ?? '-' }}
                    </div>

                </div>


                {{-- Approval Document --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Approval Document
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->approvalDocument->full_name ?? '-' }}
                    </div>

                </div>


                {{-- Travel Type --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Travel Type
                    </label>

                    @if($spdReport->spd->travel_type === 'domestic')

                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                            Domestic
                        </span>

                    @elseif($spdReport->spd->travel_type === 'international')

                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                            International
                        </span>

                    @else

                        <span class="text-muted">
                            {{ $spdReport->spd->travel_type ?? '-' }}
                        </span>

                    @endif

                </div>


                {{-- From --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        From
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->from ?? '-' }}
                    </div>

                </div>


                {{-- Destination --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Destination
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->destination ?? '-' }}
                    </div>

                </div>


                {{-- Original Departure --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Original Departure
                    </label>

                    <div class="fw-semibold">
                        {{ optional($spdReport->spd->date_departure)->format('d M Y') }}
                    </div>

                </div>


                {{-- Original Return --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Original Return
                    </label>

                    <div class="fw-semibold">
                        {{ optional($spdReport->spd->date_return)->format('d M Y') }}
                    </div>

                </div>


                {{-- Original Days --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Original Total Days
                    </label>

                    <div class="fw-semibold">
                        {{ $spdReport->spd->total_days ?? 0 }} days
                    </div>

                </div>


                {{-- Balance Received --}}
                <div class="col-md-6 col-xl-4">

                    <label class="form-label text-muted small mb-1">
                        Balance Received
                    </label>

                    <div class="fw-bold text-primary">
                        Rp {{ number_format((float) $spdReport->balance_received, 0, ',', '.') }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- ACTUAL SPD REPORT --}}
    {{-- ================================================================ --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="fw-semibold mb-0">
                <i class="bi bi-receipt me-2"></i>
                Actual SPD Report
            </h5>

        </div>

        <div class="card-body">

            {{-- Travel --}}
            <div class="mb-4">

                <h6 class="fw-semibold mb-3">
                    Actual Travel
                </h6>

                <div class="row g-4">

                    {{-- Actual Departure --}}
                    <div class="col-md-4">

                        <label class="form-label text-muted small mb-1">
                            Actual Departure
                        </label>

                        <div class="fw-semibold">
                            {{ optional($spdReport->date_departure)->format('d M Y') }}
                        </div>

                    </div>


                    {{-- Actual Return --}}
                    <div class="col-md-4">

                        <label class="form-label text-muted small mb-1">
                            Actual Return
                        </label>

                        <div class="fw-semibold">
                            {{ optional($spdReport->date_return)->format('d M Y') }}
                        </div>

                    </div>


                    {{-- Actual Days --}}
                    <div class="col-md-4">

                        <label class="form-label text-muted small mb-1">
                            Actual Total Days
                        </label>

                        <div>
                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-3 py-2">
                                {{ $spdReport->total_days }} day{{ $spdReport->total_days != 1 ? 's' : '' }}
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            <hr class="my-4">


            {{-- Cost Level --}}
            <div class="mb-4">

                <h6 class="fw-semibold mb-3">
                    Cost Level Rate
                </h6>

                <div class="row g-4">

                    {{-- Cost Level --}}
                    <div class="col-md-4">

                        <label class="form-label text-muted small mb-1">
                            Cost Level
                        </label>

                        <div class="fw-semibold">
                            {{ $spdReport->employee->costLevel->name ?? '-' }}
                        </div>

                    </div>


                    {{-- Meals --}}
                    <div class="col-md-4">

                        <label class="form-label text-muted small mb-1">
                            Meals per Day
                        </label>

                        <div class="fw-semibold">
                            Rp {{ number_format((float) $spdReport->meals_per_day, 0, ',', '.') }}
                        </div>

                    </div>


                    {{-- Allowance --}}
                    <div class="col-md-4">

                        <label class="form-label text-muted small mb-1">
                            Allowance per Day
                        </label>

                        <div class="fw-semibold">
                            Rp {{ number_format((float) $spdReport->allowance_per_day, 0, ',', '.') }}
                        </div>

                    </div>

                </div>

            </div>


            <hr class="my-4">


            {{-- Actual Expenses --}}
            <div>

                <h6 class="fw-semibold mb-3">
                    Actual Expenses
                </h6>

                <div class="row g-3">

                    {{-- Meals --}}
                    <div class="col-md-6 col-xl-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted mb-1">
                                Meals Total
                            </div>

                            <div class="fw-bold">
                                Rp {{ number_format((float) $spdReport->meals_per_day * (int) $spdReport->total_days, 0, ',', '.') }}
                            </div>

                        </div>

                    </div>


                    {{-- Allowance --}}
                    <div class="col-md-6 col-xl-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted mb-1">
                                Allowance Total
                            </div>

                            <div class="fw-bold">
                                Rp {{ number_format((float) $spdReport->allowance_per_day * (int) $spdReport->total_days, 0, ',', '.') }}
                            </div>

                        </div>

                    </div>


                    {{-- Local Transport --}}
                    <div class="col-md-6 col-xl-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted mb-1">
                                Local Transport
                            </div>

                            <div class="fw-bold">
                                Rp {{ number_format((float) $spdReport->local_transport, 0, ',', '.') }}
                            </div>

                        </div>

                    </div>


                    {{-- Contingencies --}}
                    <div class="col-md-6 col-xl-3">

                        <div class="border rounded p-3 h-100">

                            <div class="small text-muted mb-1">
                                Contingencies
                            </div>

                            <div class="fw-bold">
                                Rp {{ number_format((float) $spdReport->contingencies, 0, ',', '.') }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- FINANCIAL SUMMARY --}}
    {{-- ================================================================ --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="fw-semibold mb-0">
                <i class="bi bi-calculator me-2"></i>
                Financial Summary
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- Balance Received --}}
                <div class="col-md-6 col-xl-4">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Balance Received
                        </div>

                        <div class="fs-5 fw-bold">
                            Rp {{ number_format((float) $spdReport->balance_received, 0, ',', '.') }}
                        </div>

                    </div>

                </div>


                {{-- Expense Balance --}}
                <div class="col-md-6 col-xl-4">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Actual Expense Balance
                        </div>

                        <div class="fs-5 fw-bold">
                            Rp {{ number_format((float) $spdReport->expense_balance, 0, ',', '.') }}
                        </div>

                    </div>

                </div>


                {{-- Expense Report Total --}}
                <div class="col-md-6 col-xl-4">

                    <div class="border rounded p-3 h-100">

                        <div class="small text-muted mb-1">
                            Expense Report Total
                        </div>

                        @if((float) $spdReport->expense_report_total < 0)

                            <div class="fs-5 fw-bold text-danger">
                                Rp {{ number_format(abs((float) $spdReport->expense_report_total), 0, ',', '.') }}
                            </div>

                            <small class="text-danger">
                                Company reimburses employee
                            </small>

                        @elseif((float) $spdReport->expense_report_total > 0)

                            <div class="fs-5 fw-bold text-warning">
                                Rp {{ number_format((float) $spdReport->expense_report_total, 0, ',', '.') }}
                            </div>

                            <small class="text-warning-emphasis">
                                Employee refunds company
                            </small>

                        @else

                            <div class="fs-5 fw-bold text-success">
                                Rp 0
                            </div>

                            <small class="text-success">
                                Cash clear
                            </small>

                        @endif

                    </div>

                </div>

            </div>


            {{-- Settlement Status --}}
            <div class="mt-4 p-3 rounded bg-light">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                    <div>

                        <div class="small text-muted">
                            Settlement Status
                        </div>

                        <div class="fw-semibold">

                            @if($spdReport->settlement_status === 'reimburse')

                                <span class="text-danger">
                                    <i class="bi bi-arrow-up-circle me-1"></i>
                                    Reimburse Employee
                                </span>

                            @elseif($spdReport->settlement_status === 'refund_employee')

                                <span class="text-warning-emphasis">
                                    <i class="bi bi-arrow-down-circle me-1"></i>
                                    Refund Employee to Company
                                </span>

                            @elseif($spdReport->settlement_status === 'cash_clear')

                                <span class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Cash Clear
                                </span>

                            @else

                                <span class="text-muted">
                                    -
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="text-md-end">

                        <div class="small text-muted">
                            Report Submitted
                        </div>

                        <div class="fw-semibold">

                            @if($spdReport->submitted_at)

                                {{ $spdReport->submitted_at->format('d M Y H:i') }}

                            @else

                                -

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- EVIDENCE & NOTE --}}
    {{-- ================================================================ --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="fw-semibold mb-0">
                <i class="bi bi-paperclip me-2"></i>
                Evidence & Note
            </h5>

        </div>

        <div class="card-body">

            {{-- Evidence --}}
            <div class="mb-4">

                <label class="form-label text-muted small mb-1">
                    Expense Evidence
                </label>

                @if($spdReport->expense_evidence)

                    <div>
                        <a
                            href="{{ asset('storage/' . $spdReport->expense_evidence) }}"
                            target="_blank"
                            class="btn btn-outline-primary"
                        >
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>
                            Open Evidence
                        </a>
                    </div>

                @else

                    <div class="text-muted">
                        No evidence uploaded.
                    </div>

                @endif

            </div>


            {{-- Note --}}
            <div>

                <label class="form-label text-muted small mb-1">
                    Employee Note
                </label>

                <div class="border rounded p-3 bg-light">

                    @if($spdReport->note)

                        {!! nl2br(e($spdReport->note)) !!}

                    @else

                        <span class="text-muted">
                            No note provided.
                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================ --}}
    {{-- MANAGER DECISION --}}
    {{-- ================================================================ --}}

    @if($spdReport->status_report === 'submitted')

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 py-3">

                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-person-check me-2"></i>
                    Manager Decision
                </h5>

            </div>

            <div class="card-body">

                <div class="alert alert-warning">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    Please review all travel and expense information carefully
                    before approving this SPD report.

                </div>


                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">

                    {{-- Reject --}}
                    <button
                        type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal"
                    >
                        <i class="bi bi-x-circle me-1"></i>
                        Reject Report
                    </button>


                    {{-- Approve --}}
                    <form
                        action="{{ route('spd-report-approvals.approve', $spdReport) }}"
                        method="POST"
                        class="d-inline"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-success"
                            onclick="return confirm('Are you sure you want to approve this SPD Report?');"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Approve Report
                        </button>

                    </form>

                </div>

            </div>

        </div>

    @elseif($spdReport->status_report === 'approved')

        <div class="alert alert-success shadow-sm">

            <i class="bi bi-check-circle-fill me-2"></i>

            This SPD Report has already been approved.

            @if($spdReport->manager_approved_at)

                <span class="ms-1">
                    Approved on
                    {{ $spdReport->manager_approved_at->format('d M Y H:i') }}.
                </span>

            @endif

        </div>

    @elseif($spdReport->status_report === 'rejected')

        <div class="alert alert-danger shadow-sm">

            <div class="fw-semibold mb-2">

                <i class="bi bi-x-circle-fill me-2"></i>
                This SPD Report was rejected.
                
            </div>

            @if($spdReport->manager_rejection_reason)

                <div>
                    <strong>Reason:</strong>
                    {{ $spdReport->manager_rejection_reason }}
                </div>

            @endif

        </div>

    @endif


</div>


{{-- ================================================================ --}}
{{-- REJECT MODAL --}}
{{-- ================================================================ --}}

@if($spdReport->status_report === 'submitted')

    <div
        class="modal fade"
        id="rejectModal"
        tabindex="-1"
        aria-labelledby="rejectModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form
                    action="{{ route('spd-report-approvals.reject', $spdReport) }}"
                    method="POST"
                >

                    @csrf

                    <div class="modal-header">

                        <h5
                            class="modal-title fw-semibold"
                            id="rejectModalLabel"
                        >
                            Reject SPD Report
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>

                    </div>


                    <div class="modal-body">

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-triangle me-2"></i>

                            Please provide a clear reason for rejecting
                            this SPD report.

                        </div>


                        <div>

                            <label
                                for="manager_rejection_reason"
                                class="form-label fw-semibold"
                            >
                                Rejection Reason
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="manager_rejection_reason"
                                id="manager_rejection_reason"
                                rows="5"
                                class="form-control"
                                minlength="5"
                                maxlength="2000"
                                required
                                placeholder="Enter the reason for rejection..."
                            >{{ old('manager_rejection_reason') }}</textarea>

                            <div class="form-text">
                                Minimum 5 characters.
                            </div>

                            @error('manager_rejection_reason')

                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Reject Report
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endif

@endsection
