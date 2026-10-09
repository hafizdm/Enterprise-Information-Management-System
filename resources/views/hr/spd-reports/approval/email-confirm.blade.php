@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">SPD Report Approval Confirmation</h4>
            <p class="text-muted mb-0">
                Review the business trip report before confirming your decision.
            </p>
        </div>

        <a
            href="{{ route('spd-report-approvals.index') }}"
            class="btn btn-outline-secondary"
        >
            Back to Approvals
        </a>
    </div>

    <div class="alert alert-info">
        <strong>Security check passed.</strong>
        Please review the SPD Report details below before confirming your decision.
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Report Information</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <div class="text-muted small">SPD Number</div>
                    <div class="fw-semibold">
                        {{ $spdReport->spd?->spd_number ?? $spdReport->spd_id ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Approval Stage</div>
                    <div class="fw-semibold">Manager</div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Employee</div>
                    <div class="fw-semibold">
                        {{ $spdReport->employee?->full_name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Project</div>
                    <div class="fw-semibold">
                        {{ $spdReport->spd?->project?->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Departure Date</div>
                    <div class="fw-semibold">
                        {{ $spdReport->date_departure?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Return Date</div>
                    <div class="fw-semibold">
                        {{ $spdReport->date_return?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Total Days</div>
                    <div class="fw-semibold">
                        {{ $spdReport->total_days ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Report Status</div>
                    <div class="fw-semibold">
                        {{ strtoupper($spdReport->status_report ?? '-') }}
                    </div>
                </div>

                @if($spdReport->manager_rejection_reason)
                    <div class="col-12">
                        <div class="text-muted small">
                            Previous Rejection Reason
                        </div>
                        <div>
                            {!! nl2br(e($spdReport->manager_rejection_reason)) !!}
                        </div>
                    </div>
                @endif

                @if($spdReport->approvalDocument)
                    <div class="col-12">
                        <div class="text-muted small">Supporting Document</div>

                        <a
                            href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($spdReport->approvalDocument->file_path) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-sm btn-outline-primary mt-1"
                        >
                            <i class="bi bi-file-earmark-text me-1"></i>
                            View Supporting Document
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Confirm Your Decision</h5>
        </div>

        <div class="card-body">

            <p>
                You are acting as <strong>Manager</strong>.
                Please choose one of the following actions.
            </p>

            <div class="d-flex flex-wrap gap-2">

                @can('spd-report.approve')
                    <form
                        action="{{ route('spd-report-approvals.approve', $spdReport) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to approve this SPD Report?')"
                    >
                        @csrf

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Confirm Approve
                        </button>
                    </form>
                @endcan

                @can('spd-report.reject')
                    <button
                        type="button"
                        class="btn btn-danger"
                        onclick="toggleRejectForm()"
                    >
                        <i class="bi bi-x-circle me-1"></i>
                        Reject SPD Report
                    </button>
                @endcan

            </div>

            @can('spd-report.reject')
                <div
                    id="rejectForm"
                    class="mt-4 {{ $errors->has('manager_rejection_reason') ? '' : 'd-none' }}"
                >
                    <div class="border rounded p-3">

                        <h6 class="mb-3">Reject SPD Report</h6>

                        <form
                            action="{{ route('spd-report-approvals.reject', $spdReport) }}"
                            method="POST"
                        >
                            @csrf

                            <div class="mb-3">
                                <label
                                    for="manager_rejection_reason"
                                    class="form-label"
                                >
                                    Rejection Reason
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea
                                    name="manager_rejection_reason"
                                    id="manager_rejection_reason"
                                    class="form-control @error('manager_rejection_reason') is-invalid @enderror"
                                    rows="4"
                                    maxlength="2000"
                                    required
                                >{{ old('manager_rejection_reason') }}</textarea>

                                @error('manager_rejection_reason')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to reject this SPD Report?')"
                                >
                                    <i class="bi bi-x-circle me-1"></i>
                                    Confirm Reject
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="toggleRejectForm()"
                                >
                                    Cancel
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            @endcan

        </div>
    </div>

</div>

<script>
    function toggleRejectForm() {
        const rejectForm = document.getElementById('rejectForm');

        if (rejectForm) {
            rejectForm.classList.toggle('d-none');

            if (!rejectForm.classList.contains('d-none')) {
                const reasonField = document.getElementById('manager_rejection_reason');

                if (reasonField) {
                    reasonField.focus();
                }
            }
        }
    }
</script>

@endsection
