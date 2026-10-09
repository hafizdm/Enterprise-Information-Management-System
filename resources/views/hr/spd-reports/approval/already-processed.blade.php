@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px;">
        <div class="card-body text-center py-5">

            <div class="mb-3">
                <i class="bi bi-info-circle text-primary"
                   style="font-size: 3rem;"></i>
            </div>

            <h4 class="mb-3">
                SPD Report No Longer Waiting for Manager Approval
            </h4>

            <p class="text-muted mb-4">
                This SPD Report has already been processed. The approval link
                can no longer be used to approve or reject this report.
            </p>

            <div class="border rounded p-3 text-start mb-4">
                <div class="mb-2">
                    <div class="text-muted small">SPD Number</div>
                    <div class="fw-semibold">
                        {{ $spdReport->spd?->spd_number ?? $spdReport->spd_id ?? '-' }}
                    </div>
                </div>

                <div class="mb-2">
                    <div class="text-muted small">Employee</div>
                    <div class="fw-semibold">
                        {{ $spdReport->employee?->full_name ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-muted small">Current Report Status</div>
                    <div class="fw-semibold">
                        {{ strtoupper($spdReport->status_report ?? '-') }}
                    </div>
                </div>
            </div>

            <a
                href="{{ route('spd-report-approvals.index') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to SPD Report Approvals
            </a>

        </div>
    </div>

</div>

@endsection
