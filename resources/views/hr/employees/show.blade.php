@extends('layouts.app')

@section('content')

<div class="container-fluid">

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

    <div>

        <h4 class="fw-bold mb-1">
            Employee Detail
        </h4>

        <p class="text-muted mb-0">
            View employee information and account details.
        </p>

    </div>


    <div class="d-flex gap-2">

        <a
            href="{{ route('employees.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>


        @can('update', $employee)

            <a
                href="{{ route('employees.edit', $employee) }}"
                class="btn btn-dark"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

        @endcan

    </div>

</div>


{{-- Employee Profile Summary --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="row align-items-center">

            {{-- Employee Photo --}}
            <div class="col-auto">

            @if($employee->photo)
                <img
                    src="{{ asset('storage/' . $employee->photo) }}"
                    alt="{{ $employee->full_name }}"
                    class="rounded-circle border"
                    style="width:110px;height:110px;object-fit:cover;"
                >
            @else
                <div
                    class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                    style="width:110px;height:110px;"
                >
                    <i class="bi bi-person text-secondary" style="font-size:3rem;"></i>
                </div>
            @endif

            </div>


            {{-- Employee Identity --}}
            <div class="col">

                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 mb-1">

                    <h3 class="fw-bold mb-0">
                        {{ $employee->full_name }}
                    </h3>


                    @if($employee->employee_status === 'Permanent')

                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-check-circle me-1"></i>
                            Permanent
                        </span>

                    @else

                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                            <i class="bi bi-clock me-1"></i>
                            Contract
                        </span>

                    @endif

                </div>


                <div class="text-muted mb-3">

                    NIK:
                    <span class="text-dark">
                        {{ $employee->nik }}
                    </span>

                </div>


                <div class="row g-3">

                    <div class="col-md-4">

                        <div class="text-muted small">
                            Position
                        </div>

                        <div class="fw-semibold">
                            {{ $employee->position?->name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-muted small">
                            Division
                        </div>

                        <div class="fw-semibold">
                            {{ $employee->division?->name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-muted small">
                            Project / Placement
                        </div>

                        <div class="fw-semibold">
                            {{ $employee->project?->name ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

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

                <div class="text-muted small mb-1">
                    Full Name
                </div>

                <div class="fw-semibold">
                    {{ $employee->full_name }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    NIK
                </div>

                <div class="fw-semibold">
                    {{ $employee->nik }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Birth Place
                </div>

                <div>
                    {{ $employee->birth_place ?: '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Birth Date
                </div>

                <div>
                    {{ $employee->birth_date?->format('d F Y') ?? '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Gender
                </div>

                <div>
                    {{ $employee->gender ?: '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Religion
                </div>

                <div>
                    {{ $employee->religion ?: '-' }}
                </div>

            </div>


            <div class="col-12">

                <div class="text-muted small mb-1">
                    Address
                </div>

                <div class="border rounded p-3 bg-light">

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

                <div class="text-muted small mb-1">
                    Email
                </div>

                <div class="fw-semibold">
                    {{ $employee->email ?: '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Phone Number
                </div>

                <div>
                    {{ $employee->phone_number ?: '-' }}
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    NPWP
                </div>

                <div>
                    {{ $employee->npwp ?: '-' }}
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    BPJS Health
                </div>

                <div>
                    {{ $employee->bpjs_health ?: '-' }}
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    BPJS Employment
                </div>

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

                <div class="text-muted small mb-1">
                    Division
                </div>

                <div class="fw-semibold">
                    {{ $employee->division?->name ?? '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Position
                </div>

                <div class="fw-semibold">
                    {{ $employee->position?->name ?? '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Project / Placement
                </div>

                <div>
                    {{ $employee->project?->name ?? '-' }}
                </div>

            </div>


            <div class="col-md-6">

                <div class="text-muted small mb-1">
                    Report To
                </div>

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

                <div class="text-muted small mb-1">
                    Employee Status
                </div>

                <div>

                    @if($employee->employee_status === 'Permanent')

                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            Permanent
                        </span>

                    @else

                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                            Contract
                        </span>

                    @endif

                </div>

            </div>


            @if($employee->employee_status === 'Contract')

                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Contract Start Date
                    </div>

                    <div>
                        {{ $employee->contract_start_date?->format('d F Y') ?? '-' }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Contract End Date
                    </div>

                    <div>
                        {{ $employee->contract_end_date?->format('d F Y') ?? '-' }}
                    </div>

                </div>

            @endif


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    SPD Limit
                </div>

                <div class="fw-semibold">
                    {{ number_format($employee->spd_limit) }}
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    Total Annual Leave
                </div>

                <div class="fw-semibold">
                    {{ number_format($employee->total_annual_leave) }} days
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    Active Annual Leave
                </div>

                <div>
                    {{ number_format(
                        $employee->total_annual_leave - $remainingAnnualLeave
                    ) }} days
                </div>

            </div>


            <div class="col-md-4">

                <div class="text-muted small mb-1">
                    Remaining Annual Leave
                </div>

                <div class="fw-semibold text-success">
                    {{ number_format($remainingAnnualLeave) }} days
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

                    <div class="text-muted small mb-1">
                        Username
                    </div>

                    <div class="fw-semibold">
                        {{ $employee->user->username }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Account Status
                    </div>

                    <div>

                        @if($employee->user->is_active)

                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle me-1"></i>
                                Active
                            </span>

                        @else

                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                <i class="bi bi-x-circle me-1"></i>
                                Inactive
                            </span>

                        @endif

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small mb-1">
                        Role
                    </div>

                    <div>

                        @forelse($employee->user->roles as $role)

                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">
                                {{ $role->name }}
                            </span>

                        @empty

                            <span class="text-muted">
                                No role assigned
                            </span>

                        @endforelse

                    </div>

                </div>

            </div>

        @else

            <div class="d-flex align-items-center text-muted">

                <i class="bi bi-info-circle me-2"></i>

                This employee does not have a login account.

            </div>

        @endif

    </div>

</div>

</div>

@endsection
