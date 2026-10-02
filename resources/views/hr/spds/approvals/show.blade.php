@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            SPD Approval Review
        </h4>

        <p class="text-muted mb-0">
            Review business trip request before making an approval decision
        </p>
    </div>

    <div>
        <a
            href="{{ route('spd.approvals.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Approvals
        </a>
    </div>

</div>


{{-- Approval Status --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h6 class="mb-1 fw-semibold">
                    Approval Status
                </h6>

                <small class="text-muted">
                    Current approval stage
                </small>
            </div>

            <div>

                @if ($spd->status === 'pending_manager')

                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="bi bi-person-check me-1"></i>
                        Manager Approval
                    </span>

                @elseif ($spd->status === 'pending_document')

                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="bi bi-file-earmark-check me-1"></i>
                        Cost Control Approval
                    </span>

                @elseif ($spd->status === 'approved')

                    <span class="badge bg-success px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>
                        Approved
                    </span>

                @elseif ($spd->status === 'rejected')

                    <span class="badge bg-danger px-3 py-2">
                        <i class="bi bi-x-circle me-1"></i>
                        Rejected
                    </span>

                @endif

            </div>

        </div>

    </div>

    <div class="card-body px-4 py-4">

        @if ($spd->status === 'pending_manager')

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <div class="fw-semibold mb-1">
                        Manager Approval Required
                    </div>

                    <div class="text-muted">
                        Please review the SPD information before approving or rejecting this request.
                    </div>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal"
                    >
                        <i class="bi bi-x-circle me-1"></i>
                        Reject
                    </button>

                    <form
                        action="{{ route('spd.approvals.approve', $spd) }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn btn-success"
                            onclick="return confirm('Are you sure you want to approve this SPD?')"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Approve
                        </button>

                    </form>

                </div>

            </div>

        @elseif ($spd->status === 'pending_document')

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <div class="fw-semibold mb-1">
                        Cost Control Approval Required
                    </div>

                    <div class="text-muted">
                        Manager approval has been completed. Please review the SPD before making your decision.
                    </div>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal"
                    >
                        <i class="bi bi-x-circle me-1"></i>
                        Reject
                    </button>

                    <form
                        action="{{ route('spd.approvals.approve', $spd) }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn btn-success"
                            onclick="return confirm('Are you sure you want to approve this SPD?')"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Approve
                        </button>

                    </form>

                </div>

            </div>

        @elseif ($spd->status === 'approved')

            <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle me-1"></i>
                This SPD has been fully approved.
            </div>

        @elseif ($spd->status === 'rejected')

            <div class="alert alert-danger mb-0">
                <i class="bi bi-x-circle me-1"></i>
                This SPD has been rejected and no longer requires approval.
            </div>

        @else

            <div class="alert alert-secondary mb-0">
                <i class="bi bi-info-circle me-1"></i>
                This SPD is no longer waiting for your approval.
            </div>

        @endif

    </div>

</div>


{{-- Employee Information --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-person me-2"></i>
            Employee Information
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="row g-4">

            {{-- Employee --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Employee
                </div>

                <div class="fw-semibold">
                    {{ $spd->employee->full_name }}
                </div>

            </div>


            {{-- NIK --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    NIK
                </div>

                <div class="fw-semibold">
                    {{ $spd->employee->nik ?? '-' }}
                </div>

            </div>


            {{-- Division --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Division
                </div>

                <div class="fw-semibold">
                    {{ $spd->employee->division->name ?? '-' }}
                </div>

            </div>


            {{-- Position --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Position
                </div>

                <div class="fw-semibold">
                    {{ $spd->employee->position->name ?? '-' }}
                </div>

            </div>


            {{-- Cost Level --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Cost Level
                </div>

                <div class="fw-semibold">
                    {{ $spd->employee->costLevel->name ?? '-' }}
                </div>

            </div>


            {{-- Current SPD Limit --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Current SPD Limit
                </div>

                <div class="fw-semibold">
                    {{ $spd->employee->spd_limit }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- Project Information --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-building me-2"></i>
            Project Information
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="row g-4">

            {{-- Project --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Project
                </div>

                <div class="fw-semibold">
                    {{ $spd->project->name ?? '-' }}
                </div>

            </div>


            {{-- Cost Center --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Cost Center
                </div>

                <div class="fw-semibold">
                    {{ $spd->project->cost_center ?? '-' }}
                </div>

            </div>


            {{-- Project Location --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Project Location
                </div>

                <div class="fw-semibold">
                    {{ $spd->project->location ?? '-' }}
                </div>

            </div>


            {{-- Cost Control --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Cost Control
                </div>

                <div class="fw-semibold">
                    {{ $spd->approvalDocument->full_name ?? '-' }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- Travel Information --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-airplane me-2"></i>
            Travel Information
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="row g-4">

            {{-- Travel Type --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Travel Type
                </div>

                <div class="fw-semibold">
                    {{ ucfirst($spd->travel_type) }}
                </div>

            </div>


            {{-- Transportation --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Transportation
                </div>

                <div class="fw-semibold">
                    {{ ucfirst($spd->transportation) }}
                </div>

            </div>


            {{-- From --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    From
                </div>

                <div class="fw-semibold">
                    {{ $spd->from }}
                </div>

            </div>


            {{-- Destination --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Destination
                </div>

                <div class="fw-semibold">
                    {{ $spd->destination }}
                </div>

            </div>


            {{-- Departure Date --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Departure Date
                </div>

                <div class="fw-semibold">
                    {{ $spd->date_departure?->format('d M Y') }}
                </div>

            </div>


            {{-- Duration --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Duration
                </div>

                <div class="fw-semibold">
                    {{ $spd->total_days }}
                    {{ $spd->total_days == 1 ? 'day' : 'days' }}
                </div>

            </div>


            {{-- Return Date --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Return Date
                </div>

                <div class="fw-semibold">
                    {{ $spd->date_return?->format('d M Y') }}
                </div>

            </div>


            {{-- Advance Payment --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Advance Payment
                </div>

                <div class="fw-semibold">
                    {{ $spd->advance_payment ? 'Yes' : 'No' }}
                </div>

            </div>

        </div>

    </div>

</div>


@php
    $currencySymbol = $spd->travel_type === 'international'
        ? '$'
        : 'Rp';
@endphp


{{-- Financial Information --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-cash-stack me-2"></i>
            Financial Information
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="row g-4">

            {{-- Meals --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Meals per Day
                </div>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->meals_per_day, 2, ',', '.') }}
                </div>

            </div>


            {{-- Allowance --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Allowance per Day
                </div>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->allowance_per_day, 2, ',', '.') }}
                </div>

            </div>


            {{-- Local Transport --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Local Transport
                </div>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->local_transport, 2, ',', '.') }}
                </div>

            </div>


            {{-- Contingencies --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Contingencies
                </div>

                <div class="fw-semibold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->contingencies, 2, ',', '.') }}
                </div>

            </div>


            {{-- Advance Payment --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Advance Payment
                </div>

                <div>

                    @if ($spd->advance_payment)

                        <span class="badge bg-success">
                            Yes
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            No
                        </span>

                    @endif

                </div>

            </div>


            {{-- Balance Received --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Balance Received
                </div>

                <div class="fs-5 fw-bold">
                    {{ $currencySymbol }}
                    {{ number_format($spd->balance_received, 2, ',', '.') }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- Purpose & Note --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-card-text me-2"></i>
            Purpose & Note
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="mb-4">

            <div class="small text-muted mb-1">
                Purpose
            </div>

            <div>
                {!! nl2br(e($spd->purpose)) !!}
            </div>

        </div>


        <div>

            <div class="small text-muted mb-1">
                Note
            </div>

            <div>

                @if ($spd->note)

                    {!! nl2br(e($spd->note)) !!}

                @else

                    <span class="text-muted">
                        No note provided.
                    </span>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- Approval History --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-check2-square me-2"></i>
            Approval History
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="row g-4">

            {{-- Manager Approval --}}
            <div class="col-md-6">

                <div class="border rounded p-3 h-100">

                    <div class="small text-muted mb-1">
                        Manager Approval
                    </div>

                    <div class="fw-semibold mb-2">
                        {{ $spd->manager->full_name ?? '-' }}
                    </div>

                    @if ($spd->manager_approved_at)

                        <span class="badge bg-success mb-2">
                            Approved
                        </span>

                        <div class="small text-muted">
                            {{ $spd->manager_approved_at->format('d M Y H:i') }}
                        </div>

                    @elseif ($spd->manager_rejection_reason)

                        <span class="badge bg-danger mb-2">
                            Rejected
                        </span>

                        <div class="small text-danger">
                            {{ $spd->manager_rejection_reason }}
                        </div>

                    @else

                        <span class="badge bg-warning text-dark">
                            Pending
                        </span>

                    @endif

                </div>

            </div>


            {{-- Cost Control Approval --}}
            <div class="col-md-6">

                <div class="border rounded p-3 h-100">

                    <div class="small text-muted mb-1">
                        Cost Control Approval
                    </div>

                    <div class="fw-semibold mb-2">
                        {{ $spd->approvalDocument->full_name ?? '-' }}
                    </div>

                    @if ($spd->document_approved_at)

                        <span class="badge bg-success mb-2">
                            Approved
                        </span>

                        <div class="small text-muted">
                            {{ $spd->document_approved_at->format('d M Y H:i') }}
                        </div>

                    @elseif ($spd->document_rejection_reason)

                        <span class="badge bg-danger mb-2">
                            Rejected
                        </span>

                        <div class="small text-danger">
                            {{ $spd->document_rejection_reason }}
                        </div>

                    @else

                        <span class="badge bg-warning text-dark">
                            Pending
                        </span>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


{{-- Request Information --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">

        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-info-circle me-2"></i>
            Request Information
        </h6>

    </div>

    <div class="card-body px-4 py-4">

        <div class="row g-4">

            {{-- Created By --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Created By
                </div>

                <div class="fw-semibold">
                    {{ $spd->creator->employee->full_name ?? $spd->creator->username }}
                </div>

            </div>


            {{-- Created At --}}
            <div class="col-md-6">

                <div class="small text-muted mb-1">
                    Created At
                </div>

                <div class="fw-semibold">
                    {{ $spd->created_at?->format('d M Y H:i') }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- Reject Modal --}}

@if (
    $spd->status === 'pending_manager'
    || $spd->status === 'pending_document'
)

    <div
        class="modal fade"
        id="rejectModal"
        tabindex="-1"
        aria-labelledby="rejectModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="rejectModalLabel"
                    >
                        Reject SPD
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <form
                    action="{{ route('spd.approvals.reject', $spd) }}"
                    method="POST"
                >

                    @csrf

                    <div class="modal-body">

                        <div class="mb-3">

                            <label
                                for="rejection_reason"
                                class="form-label"
                            >
                                Rejection Reason
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="rejection_reason"
                                id="rejection_reason"
                                class="form-control"
                                rows="4"
                                required
                                maxlength="2000"
                                placeholder="Please provide the reason for rejecting this SPD."
                            >{{ old('rejection_reason') }}</textarea>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Reject SPD
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endif

@endsection