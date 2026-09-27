@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Employee Detail</h4>
            <p class="text-muted mb-0">
                View employee information
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

            @can('update', $employee)
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-dark">
                    <i class="bi bi-pencil me-1"></i>
                    Edit
                </a>
            @endcan
        </div>
    </div>

    {{-- Personal Information --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-person me-2"></i>
                Personal Information
            </h6>
        </div>

        <div class="card-body">
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="text-muted small">NIK</label>
                    <div class="fw-semibold">
                        {{ $employee->nik }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Full Name</label>
                    <div class="fw-semibold">
                        {{ $employee->full_name }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Birth Place</label>
                    <div>
                        {{ $employee->birth_place ?: '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Birth Date</label>
                    <div>
                        {{ $employee->birth_date?->format('d F Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Gender</label>
                    <div>
                        {{ $employee->gender }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Religion</label>
                    <div>
                        {{ $employee->religion }}
                    </div>
                </div>

                <div class="col-12">
                    <label class="text-muted small">Address</label>
                    <div>
                        {{ $employee->address ?: '-' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Contact & Identification --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-card-text me-2"></i>
                Contact & Identification
            </h6>
        </div>

        <div class="card-body">
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="text-muted small">Email</label>
                    <div>
                        {{ $employee->email }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Phone Number</label>
                    <div>
                        {{ $employee->phone_number ?: '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted small">NPWP</label>
                    <div>
                        {{ $employee->npwp ?: '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted small">BPJS Health</label>
                    <div>
                        {{ $employee->bpjs_health ?: '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted small">BPJS Employment</label>
                    <div>
                        {{ $employee->bpjs_employment ?: '-' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Organization --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-diagram-3 me-2"></i>
                Organization
            </h6>
        </div>

        <div class="card-body">
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="text-muted small">Division</label>
                    <div class="fw-semibold">
                        {{ $employee->division?->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Position</label>
                    <div class="fw-semibold">
                        {{ $employee->position?->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Project / Placement</label>
                    <div>
                        {{ $employee->project?->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Report To</label>
                    <div>
                        {{ $employee->manager?->full_name ?? '-' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Employment --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-briefcase me-2"></i>
                Employment
            </h6>
        </div>

        <div class="card-body">
            <div class="row g-4">

                <div class="col-md-4">
                    <label class="text-muted small">Employee Status</label>
                    <div>
                        <span class="badge bg-dark">
                            {{ $employee->employee_status }}
                        </span>
                    </div>
                </div>

                @if($employee->employee_status === 'Contract')
                    <div class="col-md-4">
                        <label class="text-muted small">Contract Start Date</label>
                        <div>
                            {{ $employee->contract_start_date?->format('d F Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small">Contract End Date</label>
                        <div>
                            {{ $employee->contract_end_date?->format('d F Y') ?? '-' }}
                        </div>
                    </div>
                @endif

                <div class="col-md-6">
                    <label class="text-muted small">SPD Limit</label>
                    <div>
                        {{ number_format($employee->spd_limit) }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Total Annual Leave</label>
                    <div>
                        {{ number_format($employee->total_annual_leave) }} days
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Login Account --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-person-lock me-2"></i>
                Login Account
            </h6>
        </div>

        <div class="card-body">
            @if($employee->user)

                <div class="row g-4">

                    <div class="col-md-4">
                        <label class="text-muted small">Username</label>
                        <div class="fw-semibold">
                            {{ $employee->user->username }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small">Account Status</label>
                        <div>
                            @if($employee->user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="text-muted small">Role</label>
                        <div>
                            @forelse($employee->user->roles as $role)
                                <span class="badge bg-primary me-1">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-muted">No role assigned</span>
                            @endforelse
                        </div>
                    </div>

                </div>

            @else

                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    This employee does not have a login account.
                </div>

            @endif
        </div>
    </div>

</div>
@endsection