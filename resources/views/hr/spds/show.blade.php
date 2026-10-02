@extends('layouts.app')

@section('content')

@php
    $currencySymbol = $spd->travel_type === 'international' ? '$' : 'Rp';
@endphp

{{-- ============================================================= --}}
{{-- Page Header --}}
{{-- ============================================================= --}}

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

    <div>

        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">

            <h4 class="mb-0 fw-semibold">
                SPD Detail
            </h4>

            @if ($spd->status === 'pending_manager')

                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                    <i class="bi bi-clock me-1"></i>
                    Pending Manager
                </span>

            @elseif ($spd->status === 'pending_document')

                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                    <i class="bi bi-clock me-1"></i>
                    Pending Cost Control
                </span>

            @elseif ($spd->status === 'approved')

                <span class="badge rounded-pill bg-success px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i>
                    Approved
                </span>

            @elseif ($spd->status === 'rejected')

                <span class="badge rounded-pill bg-danger px-3 py-2">
                    <i class="bi bi-x-circle me-1"></i>
                    Rejected
                </span>

            @else

                <span class="badge rounded-pill bg-secondary px-3 py-2">
                    {{ ucfirst(str_replace('_', ' ', $spd->status)) }}
                </span>

            @endif

        </div>

        <p class="text-muted mb-0">
            Business trip request details and approval information
        </p>

    </div>

    <div>

        <a
            href="{{ route('spds.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>

</div>


{{-- ============================================================= --}}
{{-- Employee & Project --}}
{{-- ============================================================= --}}

<div class="row g-4 mb-4">

    {{-- Employee Information --}}
    <div class="col-xl-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0 px-4 py-3">

                <div class="d-flex align-items-center gap-2">

                    <div class="text-primary">
                        <i class="bi bi-person-vcard fs-5"></i>
                    </div>

                    <div>

                        <h6 class="mb-0 fw-semibold">
                            Employee Information
                        </h6>

                        <small class="text-muted">
                            Employee assigned to this SPD
                        </small>

                    </div>

                </div>

            </div>

            <div class="card-body px-4 py-3">

                <div class="row g-4">

                    {{-- Employee Name --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Employee Name
                        </small>

                        <div class="fw-semibold">
                            {{ $spd->employee->full_name ?? '-' }}
                        </div>

                    </div>

                    {{-- NIK --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            NIK
                        </small>

                        <div class="fw-semibold">
                            {{ $spd->employee->nik ?? '-' }}
                        </div>

                    </div>

                    {{-- Division --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Division
                        </small>

                        <div>
                            {{ $spd->employee->division->name ?? '-' }}
                        </div>

                    </div>

                    {{-- Position --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Position
                        </small>

                        <div>
                            {{ $spd->employee->position->name ?? '-' }}
                        </div>

                    </div>

                    {{-- Cost Level --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Cost Level
                        </small>

                        <div>

                            @if ($spd->employee->costLevel)

                                <span class="badge bg-light text-dark border">
                                    {{ $spd->employee->costLevel->name }}
                                </span>

                            @else

                                <span class="text-muted">
                                    -
                                </span>

                            @endif

                        </div>

                    </div>

                    {{-- SPD Limit --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Current SPD Limit
                        </small>

                        <div class="fw-semibold">
                            {{ number_format($spd->employee->spd_limit, 0, ',', '.') }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Project Information --}}
    <div class="col-xl-6">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0 px-4 py-3">

                <div class="d-flex align-items-center gap-2">

                    <div class="text-primary">
                        <i class="bi bi-building fs-5"></i>
                    </div>

                    <div>

                        <h6 class="mb-0 fw-semibold">
                            Project Information
                        </h6>

                        <small class="text-muted">
                            Project and cost control assignment
                        </small>

                    </div>

                </div>

            </div>

            <div class="card-body px-4 py-3">

                <div class="row g-4">

                    {{-- Project --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Project
                        </small>

                        <div class="fw-semibold">
                            {{ $spd->project->name ?? '-' }}
                        </div>

                    </div>

                    {{-- Cost Center --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Cost Center
                        </small>

                        <div class="fw-semibold">
                            {{ $spd->project->cost_center ?? '-' }}
                        </div>

                    </div>

                    {{-- Project Location --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Project Location
                        </small>

                        <div>
                            {{ $spd->project->location ?? '-' }}
                        </div>

                    </div>

                    {{-- Cost Control --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Cost Control
                        </small>

                        <div class="fw-semibold">
                            {{ $spd->approvalDocument->full_name ?? '-' }}
                        </div>

                    </div>

                    {{-- Manager --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Manager
                        </small>

                        <div>
                            {{ $spd->manager->full_name ?? '-' }}
                        </div>

                    </div>

                    {{-- Created By --}}
                    <div class="col-sm-6">

                        <small class="text-muted d-block mb-1">
                            Created By
                        </small>

                        <div>
                            {{ $spd->creator->username ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- Travel Information --}}
{{-- ============================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 px-4 py-3">

        <div class="d-flex align-items-center gap-2">

            <div class="text-primary">
                <i class="bi bi-airplane fs-5"></i>
            </div>

            <div>

                <h6 class="mb-0 fw-semibold">
                    Travel Information
                </h6>

                <small class="text-muted">
                    Business trip schedule and transportation details
                </small>

            </div>

        </div>

    </div>

    <div class="card-body px-4 py-3">

        <div class="row g-4">

            {{-- Travel Type --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Travel Type
                </small>

                @if ($spd->travel_type === 'domestic')

                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        Domestic
                    </span>

                @else

                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="bi bi-globe me-1"></i>
                        International
                    </span>

                @endif

            </div>

            {{-- Transportation --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Transportation
                </small>

                <div class="fw-semibold">

                    @switch($spd->transportation)

                        @case('car')

                            <i class="bi bi-car-front me-1 text-primary"></i>
                            Car

                            @break

                        @case('plane')

                            <i class="bi bi-airplane me-1 text-primary"></i>
                            Plane

                            @break

                        @case('ship')

                            <i class="bi bi-water me-1 text-primary"></i>
                            Ship

                            @break

                        @default

                            <i class="bi bi-three-dots me-1 text-primary"></i>
                            Other

                    @endswitch

                </div>

            </div>

            {{-- Date Departure --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Date Departure
                </small>

                <div class="fw-semibold">

                    <i class="bi bi-calendar-event me-1 text-muted"></i>

                    {{ $spd->date_departure?->format('d M Y') ?? '-' }}

                </div>

            </div>

            {{-- Date Return --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Date Return
                </small>

                <div class="fw-semibold">

                    <i class="bi bi-calendar-check me-1 text-muted"></i>

                    {{ $spd->date_return?->format('d M Y') ?? '-' }}

                </div>

            </div>

            {{-- From --}}
            <div class="col-md-4">

                <small class="text-muted d-block mb-1">
                    From
                </small>

                <div class="fw-semibold">
                    {{ $spd->from ?? '-' }}
                </div>

            </div>

            {{-- Destination --}}
            <div class="col-md-4">

                <small class="text-muted d-block mb-1">
                    Destination
                </small>

                <div class="fw-semibold">
                    {{ $spd->destination ?? '-' }}
                </div>

            </div>

            {{-- Total Days --}}
            <div class="col-md-4">

                <small class="text-muted d-block mb-1">
                    Duration
                </small>

                <div class="fw-semibold">

                    {{ $spd->total_days }}

                    {{ $spd->total_days == 1 ? 'Day' : 'Days' }}

                </div>

            </div>

            {{-- Advance Payment --}}
            <div class="col-md-4">

                <small class="text-muted d-block mb-1">
                    Advance Payment
                </small>

                @if ($spd->advance_payment)

                    <span class="text-success fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>
                        Yes
                    </span>

                @else

                    <span class="text-muted fw-semibold">
                        <i class="bi bi-dash-circle me-1"></i>
                        No
                    </span>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- Financial Information --}}
{{-- ============================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 px-4 py-3">

        <div class="d-flex align-items-center gap-2">

            <div class="text-primary">
                <i class="bi bi-cash-stack fs-5"></i>
            </div>

            <div>

                <h6 class="mb-0 fw-semibold">
                    Financial Information
                </h6>

                <small class="text-muted">
                    Allowance and estimated business trip costs
                </small>

            </div>

        </div>

    </div>

    <div class="card-body px-4 py-3">

        <div class="row g-4">

            {{-- Meals --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Meals / Day
                </small>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->meals_per_day, 2, ',', '.') }}
                </div>

            </div>

            {{-- Allowance --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Allowance / Day
                </small>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->allowance_per_day, 2, ',', '.') }}
                </div>

            </div>

            {{-- Local Transport --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Local Transport
                </small>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->local_transport, 2, ',', '.') }}
                </div>

            </div>

            {{-- Contingencies --}}
            <div class="col-md-3">

                <small class="text-muted d-block mb-1">
                    Contingencies
                </small>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->contingencies, 2, ',', '.') }}
                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Total --}}
        <div class="rounded-3 bg-light p-4">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <small class="text-muted d-block mb-1">
                        Total Balance Received
                    </small>

                    <div class="small text-muted">
                        Meals + Allowance + Local Transport + Contingencies
                    </div>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <div class="fs-4 fw-bold text-primary">

                        {{ $currencySymbol }}
                        {{ number_format($spd->balance_received, 2, ',', '.') }}

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- Purpose & Note --}}
{{-- ============================================================= --}}

<div class="row g-4 mb-4">

    {{-- Purpose --}}
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0 px-4 py-3">

                <div class="d-flex align-items-center gap-2">

                    <div class="text-primary">
                        <i class="bi bi-card-text fs-5"></i>
                    </div>

                    <h6 class="mb-0 fw-semibold">
                        Purpose
                    </h6>

                </div>

            </div>

            <div class="card-body px-4">

                <div
                    class="text-secondary"
                    style="white-space: pre-line;"
                >
                    {{ $spd->purpose }}
                </div>

            </div>

        </div>

    </div>


    {{-- Note --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0 px-4 py-3">

                <div class="d-flex align-items-center gap-2">

                    <div class="text-primary">
                        <i class="bi bi-sticky fs-5"></i>
                    </div>

                    <h6 class="mb-0 fw-semibold">
                        Note
                    </h6>

                </div>

            </div>

            <div class="card-body px-4">

                @if ($spd->note)

                    <div
                        class="text-secondary"
                        style="white-space: pre-line;"
                    >
                        {{ $spd->note }}
                    </div>

                @else

                    <div class="text-muted">
                        No additional notes.
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- Approval & Monitoring --}}
{{-- ============================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 px-4 py-3">

        <div class="d-flex align-items-center gap-2">

            <div class="text-primary">
                <i class="bi bi-diagram-3 fs-5"></i>
            </div>

            <div>

                <h6 class="mb-0 fw-semibold">
                    Approval & Monitoring
                </h6>

                <small class="text-muted">
                    Approval status for each workflow stage
                </small>

            </div>

        </div>

    </div>

    <div class="card-body px-4 py-3">

        <div class="row g-4">

            {{-- Manager Approval --}}
            <div class="col-lg-6">

                <div class="border rounded-3 p-4 h-100">

                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">

                        <div>

                            <small class="text-muted d-block mb-1">
                                Step 1
                            </small>

                            <div class="fw-semibold">
                                Manager Approval
                            </div>

                            <div class="small text-muted mt-1">
                                {{ $spd->manager->full_name ?? '-' }}
                            </div>

                        </div>

                        @if ($spd->manager_approved_at)

                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Approved
                            </span>

                        @elseif (
                            $spd->status === 'rejected'
                            && $spd->manager_rejection_reason
                        )

                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>
                                Rejected
                            </span>

                        @else

                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>
                                Pending
                            </span>

                        @endif

                    </div>


                    @if ($spd->manager_approved_at)

                        <div class="small">

                            <span class="text-muted">
                                Approved on
                            </span>

                            <div class="fw-semibold mt-1">
                                {{ $spd->manager_approved_at->format('d M Y, H:i') }}
                            </div>

                        </div>

                    @endif


                    @if ($spd->manager_rejection_reason)

                        <div class="mt-3 p-3 rounded-3 bg-danger-subtle">

                            <small class="text-danger fw-semibold d-block mb-1">
                                Rejection Reason
                            </small>

                            <div class="small text-secondary">
                                {{ $spd->manager_rejection_reason }}
                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Cost Control Approval --}}
            <div class="col-lg-6">

                <div class="border rounded-3 p-4 h-100">

                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">

                        <div>

                            <small class="text-muted d-block mb-1">
                                Step 2
                            </small>

                            <div class="fw-semibold">
                                Cost Control Approval
                            </div>

                            <div class="small text-muted mt-1">
                                {{ $spd->approvalDocument->full_name ?? '-' }}
                            </div>

                        </div>

                        @if ($spd->document_approved_at)

                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Approved
                            </span>

                        @elseif (
                            $spd->status === 'rejected'
                            && $spd->document_rejection_reason
                        )

                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>
                                Rejected
                            </span>

                        @else

                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>
                                Pending
                            </span>

                        @endif

                    </div>


                    @if ($spd->document_approved_at)

                        <div class="small">

                            <span class="text-muted">
                                Approved on
                            </span>

                            <div class="fw-semibold mt-1">
                                {{ $spd->document_approved_at->format('d M Y, H:i') }}
                            </div>

                        </div>

                    @endif


                    @if ($spd->document_rejection_reason)

                        <div class="mt-3 p-3 rounded-3 bg-danger-subtle">

                            <small class="text-danger fw-semibold d-block mb-1">
                                Rejection Reason
                            </small>

                            <div class="small text-secondary">
                                {{ $spd->document_rejection_reason }}
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- Request Information --}}
{{-- ============================================================= --}}

<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 px-4 py-3">

        <div class="d-flex align-items-center gap-2">

            <div class="text-primary">
                <i class="bi bi-info-circle fs-5"></i>
            </div>

            <h6 class="mb-0 fw-semibold">
                Request Information
            </h6>

        </div>

    </div>

    <div class="card-body px-4 py-3">

        <div class="row g-4">

            {{-- Created By --}}
            <div class="col-md-6">

                <small class="text-muted d-block mb-1">
                    Created By
                </small>

                <div class="fw-semibold">
                    {{ $spd->creator->username ?? '-' }}
                </div>

            </div>

            {{-- Created At --}}
            <div class="col-md-6">

                <small class="text-muted d-block mb-1">
                    Created At
                </small>

                <div class="fw-semibold">

                    <i class="bi bi-calendar3 me-1 text-muted"></i>

                    {{ $spd->created_at?->format('d M Y, H:i') ?? '-' }}

                </div>

            </div>

        </div>

    </div>

</div>

@endsection