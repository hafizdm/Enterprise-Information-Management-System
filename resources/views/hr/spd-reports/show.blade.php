@extends('layouts.app')

@section('content')

<div class="container-fluid">


{{-- ========================================================== --}}
{{-- PAGE HEADER --}}
{{-- ========================================================== --}}

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

    <div>

        <h4 class="fw-bold mb-1">
            <i class="bi bi-file-earmark-text me-2"></i>
            SPD Report — {{ $spdReport->spd?->spd_number ?? '-' }}
        </h4>

        <p class="text-muted mb-0">
            View your submitted SPD report and expense details.
        </p>

    </div>

    <div>

        <a href="{{ route('spd-reports.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back to My SPD Reports

        </a>

    </div>

</div>


{{-- ========================================================== --}}
{{-- SUCCESS MESSAGE --}}
{{-- ========================================================== --}}

@if (session('success'))

    <div class="alert alert-success border-0 shadow-sm">

        <i class="bi bi-check-circle me-1"></i>

        {{ session('success') }}

    </div>

@endif


{{-- ========================================================== --}}
{{-- REPORT STATUS --}}
{{-- ========================================================== --}}

@php

    $reportStatus = $spdReport->status_report;

    $statusLabel = match ($reportStatus) {
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'settled' => 'Settled',
        default => ucfirst(str_replace('_', ' ', $reportStatus ?? '-')),
    };

    $statusClass = match ($reportStatus) {
        'draft' => 'text-bg-secondary',
        'submitted' => 'text-bg-warning',
        'approved' => 'text-bg-success',
        'rejected' => 'text-bg-danger',
        'settled' => 'text-bg-primary',
        default => 'text-bg-secondary',
    };

    $settlementLabel = match ($spdReport->settlement_status) {
        'reimburse' => 'Reimburse — Company owes employee',
        'cash_clear' => 'Cash Clear — No balance remaining',
        'refund_employee' => 'Refund Employee — Employee returns balance',
        default => '-',
    };

@endphp


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="row g-4 align-items-center">

            {{-- SPD Number --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    SPD Number
                </div>

                <div class="fs-5 fw-bold">
                    {{ $spdReport->spd?->spd_number ?? '-' }}
                </div>

            </div>


            {{-- Report Status --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    SPD Report Status
                </div>

                <span class="badge {{ $statusClass }} fs-6">
                    {{ $statusLabel }}
                </span>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- REJECTION INFORMATION --}}
{{-- ========================================================== --}}

@if ($reportStatus === 'rejected')

    <div class="alert alert-danger border-0 shadow-sm mb-4">

        <div class="d-flex align-items-start">

            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>

            <div class="flex-grow-1">

                <div class="fw-bold mb-1">
                    SPD Report Rejected
                </div>

                <div class="small mb-2">
                    This SPD Report was rejected by your Manager.
                    Please review the rejection reason and resubmit
                    the report using the same SPD number.
                </div>

                <div class="small text-muted mb-1">
                    Manager Rejection Reason
                </div>

                <div class="fw-semibold">
                    {{ $spdReport->manager_rejection_reason ?: 'No rejection reason provided.' }}
                </div>

                <div class="mt-3">

                    <a href="{{ route('spd-reports.create') }}"
                       class="btn btn-danger btn-sm">

                        <i class="bi bi-arrow-repeat me-1"></i>
                        Resubmit SPD Report

                    </a>

                </div>

            </div>

        </div>

    </div>

@endif


{{-- ========================================================== --}}
{{-- SECTION 1 : ORIGINAL SPD INFORMATION --}}
{{-- ========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <h6 class="fw-bold mb-1">
            <i class="bi bi-file-earmark-check me-2"></i>
            Original SPD Information
        </h6>

        <small class="text-muted">
            This information comes from the approved SPD request
            and cannot be changed from the SPD Report.
        </small>

    </div>

    <div class="card-body">

        {{-- General Information --}}

        <div class="mb-4">

            <div class="small text-uppercase text-muted fw-semibold mb-3">
                General Information
            </div>

            <div class="row g-4">

                {{-- SPD Number --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        SPD Number
                    </div>

                    <div class="fw-bold">
                        {{ $spdReport->spd?->spd_number ?? '-' }}
                    </div>

                </div>


                {{-- SPD Status --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        SPD Status
                    </div>

                    <span class="badge text-bg-success">
                        {{ ucfirst($spdReport->spd?->status ?? 'Approved') }}
                    </span>

                </div>


                {{-- Employee --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Employee
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->employee?->full_name ?? '-' }}
                    </div>

                </div>


                {{-- Project --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Project
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->project?->name ?? '-' }}
                    </div>

                </div>


                {{-- Cost Center --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Cost Center
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->project?->cost_center ?? '-' }}
                    </div>

                </div>


                {{-- Manager --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Manager
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->manager?->full_name ?? '-' }}
                    </div>

                </div>


                {{-- Approval Document --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Approval Document / PIC
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->approvalDocument?->full_name ?? '-' }}
                    </div>

                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Travel Information --}}

        <div class="mb-4">

            <div class="small text-uppercase text-muted fw-semibold mb-3">
                Travel Information
            </div>

            <div class="row g-4">

                {{-- Travel Type --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Travel Type
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->travel_type
                            ? ucfirst($spdReport->spd->travel_type)
                            : '-' }}
                    </div>

                </div>


                {{-- Transportation --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Transportation
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->transportation
                            ? ucfirst($spdReport->spd->transportation)
                            : '-' }}
                    </div>

                </div>


                {{-- From --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        From
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->from ?? '-' }}
                    </div>

                </div>


                {{-- Destination --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Destination
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->destination ?? '-' }}
                    </div>

                </div>


                {{-- SPD Departure --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        SPD Date Departure
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->date_departure?->format('d M Y') ?? '-' }}
                    </div>

                </div>


                {{-- SPD Return --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        SPD Date Return
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->date_return?->format('d M Y') ?? '-' }}
                    </div>

                </div>


                {{-- SPD Total Days --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        SPD Total Days
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->total_days ?? 0 }}
                        {{ ($spdReport->spd?->total_days ?? 0) == 1 ? 'day' : 'days' }}
                    </div>

                </div>


                {{-- Advance Payment --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Advance Payment
                    </div>

                    <div class="fw-semibold">
                        {{ $spdReport->spd?->advance_payment ? 'Yes' : 'No' }}
                    </div>

                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Approved Financial Information --}}

        <div>

            <div class="small text-uppercase text-muted fw-semibold mb-3">
                Approved Financial Information
            </div>

            <div class="row g-4">

                {{-- Meals --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Approved Meals per Day
                    </div>

                    <div class="fw-semibold">
                        Rp {{ number_format(
                            (float) ($spdReport->spd?->meals_per_day ?? 0),
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>


                {{-- Allowance --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Approved Allowance per Day
                    </div>

                    <div class="fw-semibold">
                        Rp {{ number_format(
                            (float) ($spdReport->spd?->allowance_per_day ?? 0),
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>


                {{-- Local Transport --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Approved Local Transport
                    </div>

                    <div class="fw-semibold">
                        Rp {{ number_format(
                            (float) ($spdReport->spd?->local_transport ?? 0),
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>


                {{-- Contingencies --}}
                <div class="col-md-6">

                    <div class="small text-muted mb-1">
                        Approved Contingencies
                    </div>

                    <div class="fw-semibold">
                        Rp {{ number_format(
                            (float) ($spdReport->spd?->contingencies ?? 0),
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>


                {{-- Balance Received --}}
                <div class="col-12">

                    <div class="p-3 rounded bg-light border">

                        <div class="small text-muted mb-1">
                            Balance Received
                        </div>

                        <div class="fs-5 fw-bold text-primary">
                            Rp {{ number_format(
                                (float) ($spdReport->balance_received ?? 0),
                                0,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Purpose / Note --}}

        <div>

            <div class="small text-uppercase text-muted fw-semibold mb-3">
                Business Trip Details
            </div>

            <div class="row g-4">

                <div class="col-12">

                    <div class="small text-muted mb-1">
                        Purpose
                    </div>

                    <div class="p-3 bg-light rounded border">
                        {{ $spdReport->spd?->purpose ?? '-' }}
                    </div>

                </div>


                <div class="col-12">

                    <div class="small text-muted mb-1">
                        SPD Note
                    </div>

                    <div class="p-3 bg-light rounded border">
                        {{ $spdReport->spd?->note ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- SECTION 2 : ACTUAL TRAVEL & EXPENSE --}}
{{-- ========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <h6 class="fw-bold mb-1">
            <i class="bi bi-calendar-check me-2"></i>
            Actual Travel &amp; Expenses
        </h6>

        <small class="text-muted">
            Actual travel dates and expenses submitted by the employee.
        </small>

    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- Actual Departure --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Date Departure
                </div>

                <div class="fw-semibold">
                    {{ $spdReport->date_departure?->format('d M Y') ?? '-' }}
                </div>

            </div>


            {{-- Actual Return --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Date Return
                </div>

                <div class="fw-semibold">
                    {{ $spdReport->date_return?->format('d M Y') ?? '-' }}
                </div>

            </div>


            {{-- Actual Days --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Travel Duration
                </div>

                <div class="fw-semibold">

                    {{ $spdReport->total_days }}

                    {{ $spdReport->total_days == 1 ? 'day' : 'days' }}

                </div>

            </div>


            {{-- Cost Level --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Cost Level
                </div>

                <div class="fw-semibold">
                    {{ $spdReport->employee?->costLevel?->name ?? '-' }}
                </div>

            </div>


            {{-- Meals Per Day --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Meals per Day
                </div>

                <div class="fw-semibold">
                    Rp {{ number_format(
                        (float) $spdReport->meals_per_day,
                        0,
                        ',',
                        '.'
                    ) }}
                </div>

            </div>


            {{-- Allowance Per Day --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Allowance per Day
                </div>

                <div class="fw-semibold">
                    Rp {{ number_format(
                        (float) $spdReport->allowance_per_day,
                        0,
                        ',',
                        '.'
                    ) }}
                </div>

            </div>


            {{-- Meals Total --}}
            <div class="col-md-6">

                <div class="p-3 rounded bg-light border">

                    <div class="small text-muted mb-1">
                        Actual Meals Total
                    </div>

                    <div class="fw-bold">
                        Rp {{ number_format(
                            (float) $spdReport->meals_per_day *
                            (int) $spdReport->total_days,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>


            {{-- Allowance Total --}}
            <div class="col-md-6">

                <div class="p-3 rounded bg-light border">

                    <div class="small text-muted mb-1">
                        Actual Allowance Total
                    </div>

                    <div class="fw-bold">
                        Rp {{ number_format(
                            (float) $spdReport->allowance_per_day *
                            (int) $spdReport->total_days,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>


            {{-- Local Transport --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Local Transport
                </div>

                <div class="fw-semibold">
                    Rp {{ number_format(
                        (float) $spdReport->local_transport,
                        0,
                        ',',
                        '.'
                    ) }}
                </div>

            </div>


            {{-- Contingencies --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Actual Contingencies
                </div>

                <div class="fw-semibold">
                    Rp {{ number_format(
                        (float) $spdReport->contingencies,
                        0,
                        ',',
                        '.'
                    ) }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- SECTION 3 : FINANCIAL SUMMARY --}}
{{-- ========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <h6 class="fw-bold mb-1">
            <i class="bi bi-calculator me-2"></i>
            Financial Summary
        </h6>

        <small class="text-muted">
            Summary of the SPD advance and actual expenses.
        </small>

    </div>

    <div class="card-body">

        <div class="row g-3">

            {{-- Balance Received --}}
            <div class="col-md-4">

                <div class="border rounded p-3 h-100">

                    <div class="small text-muted mb-1">
                        Balance Received
                    </div>

                    <div class="fs-5 fw-bold">
                        Rp {{ number_format(
                            (float) $spdReport->balance_received,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>


            {{-- Expense Balance --}}
            <div class="col-md-4">

                <div class="border rounded p-3 h-100">

                    <div class="small text-muted mb-1">
                        Actual Expense Balance
                    </div>

                    <div class="fs-5 fw-bold">
                        Rp {{ number_format(
                            (float) $spdReport->expense_balance,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>


            {{-- Expense Report Total --}}
            <div class="col-md-4">

                <div class="border rounded p-3 h-100">

                    <div class="small text-muted mb-1">
                        Expense Report Total
                    </div>

                    <div class="fs-5 fw-bold">
                        Rp {{ number_format(
                            (float) $spdReport->expense_report_total,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>


            {{-- Settlement --}}
            <div class="col-12">

                <div class="alert alert-light border mb-0">

                    <div class="d-flex align-items-center">

                        <i class="bi bi-arrow-left-right fs-5 me-2"></i>

                        <div>

                            <div class="small text-muted">
                                Settlement Status
                            </div>

                            <div class="fw-bold">
                                {{ $settlementLabel }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- SECTION 4 : EXPENSE EVIDENCE --}}
{{-- ========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <h6 class="fw-bold mb-1">
            <i class="bi bi-paperclip me-2"></i>
            Expense Evidence
        </h6>

        <small class="text-muted">
            Supporting documents submitted with this SPD Report.
        </small>

    </div>

    <div class="card-body">

        @if ($spdReport->expense_evidence)

            <div class="d-flex flex-column flex-md-row
                        justify-content-between
                        align-items-md-center
                        gap-3
                        p-3
                        border
                        rounded">

                <div class="d-flex align-items-center">

                    <i class="bi bi-file-earmark-pdf fs-3 me-3"></i>

                    <div>

                        <div class="fw-semibold">
                            Expense Evidence
                        </div>

                        <div class="small text-muted">
                            Supporting document
                        </div>

                    </div>

                </div>


                <a href="{{ asset('storage/' . $spdReport->expense_evidence) }}"
                   target="_blank"
                   class="btn btn-outline-primary">

                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    View Evidence

                </a>

            </div>

        @else

            <div class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                No expense evidence uploaded.
            </div>

        @endif

    </div>

</div>


{{-- ========================================================== --}}
{{-- SECTION 5 : REPORT NOTE --}}
{{-- ========================================================== --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <h6 class="fw-bold mb-0">
            <i class="bi bi-chat-left-text me-2"></i>
            Report Note
        </h6>

    </div>

    <div class="card-body">

        <div class="p-3 bg-light rounded border">

            {{ $spdReport->note ?: 'No additional note provided.' }}

        </div>

    </div>

</div>


{{-- ========================================================== --}}
{{-- SECTION 6 : MANAGER DECISION --}}
{{-- ========================================================== --}}

<div class="card border-0 shadow-sm mb-5">

    <div class="card-header bg-white border-bottom py-3">

        <h6 class="fw-bold mb-1">
            <i class="bi bi-person-check me-2"></i>
            Manager Decision
        </h6>

        <small class="text-muted">
            Current approval status of this SPD Report.
        </small>

    </div>

    <div class="card-body">

        @if ($reportStatus === 'submitted')

            <div class="alert alert-warning border-0 mb-0">

                <i class="bi bi-clock-history me-1"></i>

                Waiting for Manager approval.

            </div>

        @elseif ($reportStatus === 'approved')

            <div class="alert alert-success border-0 mb-0">

                <i class="bi bi-check-circle-fill me-1"></i>

                This SPD Report has been approved by the Manager.

                @if ($spdReport->manager_approved_at)

                    <div class="small mt-1">

                        Approved at:
                        {{ $spdReport->manager_approved_at->format('d M Y H:i') }}

                    </div>

                @endif

            </div>

        @elseif ($reportStatus === 'rejected')

            <div class="alert alert-danger border-0 mb-0">

                <i class="bi bi-x-circle-fill me-1"></i>

                This SPD Report was rejected by the Manager.

                @if ($spdReport->manager_rejection_reason)

                    <div class="small mt-2">

                        <strong>Reason:</strong>

                        {{ $spdReport->manager_rejection_reason }}

                    </div>

                @endif

            </div>

        @elseif ($reportStatus === 'settled')

            <div class="alert alert-primary border-0 mb-0">

                <i class="bi bi-check2-circle me-1"></i>

                This SPD Report has been settled.

            </div>

        @else

            <div class="alert alert-secondary border-0 mb-0">

                Current status:
                {{ $statusLabel }}

            </div>

        @endif

    </div>

</div>


</div>

@endsection
