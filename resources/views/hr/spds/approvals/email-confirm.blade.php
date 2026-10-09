
@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">SPD Approval Confirmation</h4>
            <p class="text-muted mb-0">
                Confirm your approval decision for this business trip request.
            </p>
        </div>

        <a
            href="{{ route('spd.approvals.index') }}"
            class="btn btn-outline-secondary"
        >
            Back to Approvals
        </a>
    </div>

    <div class="alert alert-info">
        <strong>Security check passed.</strong>
        Please review the SPD details below before confirming your decision.
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Approval Information</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">SPD Number</div>
                    <div class="fw-semibold">
                        {{ $spd->spd_number }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Approval Stage</div>
                    <div class="fw-semibold">
                        {{ $stage === 'manager' ? 'Manager' : 'Cost Control' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Employee</div>
                    <div class="fw-semibold">
                        {{ $spd->employee->full_name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Project</div>
                    <div class="fw-semibold">
                        {{ $spd->project->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Departure Date</div>
                    <div class="fw-semibold">
                        {{ $spd->date_departure?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Return Date</div>
                    <div class="fw-semibold">
                        {{ $spd->date_return?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small">Destination</div>
                    <div class="fw-semibold">
                        {{ $spd->destination ?? '-' }}
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small">Purpose</div>
                    <div>
                        {!! nl2br(e($spd->purpose ?? '-')) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small">Note</div>
                    <div>
                        {!! nl2br(e($spd->note ?? '-')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Confirm Your Decision</h5>
        </div>

        <div class="card-body">
            <p>
                You are acting as
                <strong>{{ $stage === 'manager' ? 'Manager' : 'Cost Control' }}</strong>.
                Please choose one of the following actions.
            </p>

            <div class="d-flex flex-wrap gap-2">
                <form
                    action="{{ route('spd.approvals.approve', $spd) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to approve this SPD?')"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="email_approval_stage"
                        value="{{ $stage }}"
                    >

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Confirm Approve
                    </button>
                </form>

                <button
                    type="button"
                    class="btn btn-danger"
                    data-bs-toggle="collapse"
                    data-bs-target="#rejectForm"
                    aria-expanded="{{ $errors->has('rejection_reason') ? 'true' : 'false' }}"
                    aria-controls="rejectForm"
                >
                    <i class="bi bi-x-circle me-1"></i>
                    Reject SPD
                </button>
            </div>

            <div
                class="collapse mt-4 {{ $errors->has('rejection_reason') ? 'show' : '' }}"
                id="rejectForm"
            >
                <div class="border rounded p-3">
                    <form
                        action="{{ route('spd.approvals.reject', $spd) }}"
                        method="POST"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="email_approval_stage"
                            value="{{ $stage }}"
                        >

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
                                class="form-control @error('rejection_reason') is-invalid @enderror"
                                rows="4"
                                maxlength="2000"
                                required
                            >{{ old('rejection_reason') }}</textarea>

                            @error('rejection_reason')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to reject this SPD?')"
                        >
                            Confirm Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection