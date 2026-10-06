@extends('layouts.app')

@section('content')

<div class="container-fluid">


{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-file-earmark-check me-2"></i>
            SPD Report Monitoring
        </h4>
        <div class="text-muted">
            Monitoring SPD Report yang telah disetujui oleh Manager
        </div>
    </div>

    <a href="{{ route('spd-report-monitoring.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>
</div>

{{-- Status --}}
<div class="alert alert-success d-flex align-items-center mb-4">
    <i class="bi bi-check-circle-fill me-2"></i>
    <div>
        <strong>Approved by Manager</strong>
        <div class="small">
            This SPD Report has been approved and is available for HRD monitoring.
        </div>
    </div>
</div>

{{-- SPD Information --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>
            <i class="bi bi-card-text me-2"></i>
            SPD Information
        </strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label text-muted">SPD Number</label>
                <div class="fw-semibold">
                    {{ $spdReport->spd?->spd_number ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Employee</label>
                <div class="fw-semibold">
                    {{ $spdReport->employee?->full_name ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">NIK</label>
                <div class="fw-semibold">
                    {{ $spdReport->employee?->nik ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Project</label>
                <div>
                    {{ $spdReport->spd?->project?->name ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Cost Center</label>
                <div>
                    {{ $spdReport->spd?->project?->cost_center ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Manager</label>
                <div>
                    {{ $spdReport->spd?->manager?->full_name ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Approval Document</label>
                <div>
                    {{ $spdReport->spd?->approvalDocument?->full_name ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Travel Type</label>
                <div class="text-capitalize">
                    {{ $spdReport->spd?->travel_type ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Destination</label>
                <div>
                    {{ $spdReport->spd?->destination ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Travel Information --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>
            <i class="bi bi-airplane me-2"></i>
            Travel Information
        </strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-3">
                <label class="form-label text-muted">Departure</label>
                <div>
                    {{ $spdReport->date_departure?->format('d M Y') ?? '-' }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted">Return</label>
                <div>
                    {{ $spdReport->date_return?->format('d M Y') ?? '-' }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted">Total Days</label>
                <div>
                    {{ $spdReport->total_days ?? 0 }} day(s)
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted">Transportation</label>
                <div class="text-capitalize">
                    {{ $spdReport->spd?->transportation ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Expense Information --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>
            <i class="bi bi-cash-stack me-2"></i>
            Expense Information
        </strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label text-muted">Balance Received</label>
                <div class="fw-semibold">
                    Rp {{ number_format($spdReport->balance_received ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Expense Balance</label>
                <div>
                    Rp {{ number_format($spdReport->expense_balance ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Expense Report Total</label>
                <div class="fw-semibold">
                    Rp {{ number_format($spdReport->expense_report_total ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Meals / Day</label>
                <div>
                    Rp {{ number_format($spdReport->meals_per_day ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Allowance / Day</label>
                <div>
                    Rp {{ number_format($spdReport->allowance_per_day ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Local Transport</label>
                <div>
                    Rp {{ number_format($spdReport->local_transport ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Contingencies</label>
                <div>
                    Rp {{ number_format($spdReport->contingencies ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Settlement Status</label>
                <div>
                    @php
                        $settlementLabels = [
                            'reimburse' => 'Reimburse Employee',
                            'refund_employee' => 'Refund to Company',
                            'cash_clear' => 'Cash Clear',
                        ];
                    @endphp

                    <span class="badge bg-secondary">
                        {{ $settlementLabels[$spdReport->settlement_status] ?? ucfirst(str_replace('_', ' ', $spdReport->settlement_status ?? '-')) }}
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted">Manager Approved At</label>
                <div>
                    {{ $spdReport->manager_approved_at?->format('d M Y H:i') ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Evidence --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong>
            <i class="bi bi-paperclip me-2"></i>
            Expense Evidence
        </strong>
    </div>

    <div class="card-body">

        @if($spdReport->expense_evidence)

            <a href="{{ asset('storage/' . $spdReport->expense_evidence) }}"
               target="_blank"
               class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-text me-1"></i>
                View Evidence
            </a>

        @else

            <span class="text-muted">
                No expense evidence uploaded.
            </span>

        @endif

    </div>
</div>

{{-- Note --}}
@if($spdReport->note)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <strong>
                <i class="bi bi-chat-left-text me-2"></i>
                Note
            </strong>
        </div>

        <div class="card-body">
            {!! nl2br(e($spdReport->note)) !!}
        </div>
    </div>
@endif

{{-- HRD Monitoring Notice --}}
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    <strong>HRD Monitoring Only.</strong>
    This page is for monitoring purposes. Approval and rejection actions are handled by the Manager.
</div>


</div>

@endsection
