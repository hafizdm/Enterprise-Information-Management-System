
@extends('layouts.app')

@section('content')

<div class="container-fluid py-2">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-person-vcard fs-4 text-dark"></i>

                <h4 class="fw-bold mb-0">
                    Employee Detail
                </h4>
            </div>

            <p class="text-muted mb-0">
                View employee information, employment details, and account access.
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
                    Edit Employee
                </a>

            @endcan

        </div>

    </div>


    {{-- =========================================================
        PROFILE SUMMARY
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-4">

            <div class="row align-items-center g-4">

                {{-- PHOTO --}}
                <div class="col-auto">

                    @if($employee->photo)

                        <img
                            src="{{ asset('storage/' . $employee->photo) }}"
                            alt="{{ $employee->full_name }}"
                            class="rounded-3 border"
                            style="width:120px;height:120px;object-fit:cover;"
                        >

                    @else

                        <div
                            class="rounded-3 bg-light border d-flex align-items-center justify-content-center"
                            style="width:120px;height:120px;"
                        >
                            <i
                                class="bi bi-person text-secondary"
                                style="font-size:3.5rem;"
                            ></i>
                        </div>

                    @endif

                </div>


                {{-- IDENTITY --}}
                <div class="col">

                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                        <h3 class="fw-bold mb-0">
                            {{ $employee->full_name }}
                        </h3>

                        @if($employee->employee_status === 'Permanent')

                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i>
                                Permanent
                            </span>

                        @else

                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="bi bi-clock me-1"></i>
                                Contract
                            </span>

                        @endif

                    </div>


                    <div class="text-muted mb-3">
                        Employee ID / NIK:
                        <span class="text-dark fw-semibold">
                            {{ $employee->nik }}
                        </span>
                    </div>


                    <div class="row g-3">

                        <div class="col-md-4">

                            <div class="text-muted small mb-1">
                                <i class="bi bi-briefcase me-1"></i>
                                Position
                            </div>

                            <div class="fw-semibold">
                                {{ $employee->position?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="text-muted small mb-1">
                                <i class="bi bi-diagram-3 me-1"></i>
                                Division
                            </div>

                            <div class="fw-semibold">
                                {{ $employee->division?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="text-muted small mb-1">
                                <i class="bi bi-building me-1"></i>
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



{{-- =========================================================
    PERSONAL INFORMATION
========================================================== --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3 px-4">
        <div class="d-flex align-items-center gap-2">

            <div class="rounded-2 bg-light p-2">
                <i class="bi bi-person text-dark"></i>
            </div>

            <div>
                <h6 class="fw-bold mb-0">
                    Personal Information
                </h6>

                <small class="text-muted">
                    Basic personal and demographic information
                </small>
            </div>

        </div>
    </div>

    <div class="card-body p-4">

        <div class="row g-3">

            {{-- FULL NAME --}}
            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        Full Name
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->full_name ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- NIK --}}
            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        NIK
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->nik ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- BIRTH PLACE --}}
            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        Birth Place
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->birth_place ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- BIRTH DATE --}}
            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        Birth Date
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->birth_date?->format('d F Y') ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- GENDER --}}
            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        Gender
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->gender ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- RELIGION --}}
            <div class="col-md-6">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        Religion
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->religion ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- ADDRESS --}}
            <div class="col-12">
                <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small mb-2">
                        Address
                    </div>

                    <div class="fw-semibold text-break">
                        {{ $employee->address ?: '-' }}
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>




    {{-- =========================================================
        CONTACT & IDENTIFICATION
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom py-3 px-4">

            <div class="d-flex align-items-center gap-2">

                <div class="rounded-2 bg-light p-2">
                    <i class="bi bi-card-text text-dark"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-0">
                        Contact & Identification
                    </h6>

                    <small class="text-muted">
                        Contact details and government identification
                    </small>
                </div>

            </div>

        </div>

        <div class="card-body p-4">

            <div class="row g-3">

                {{-- EMAIL --}}
                <div class="col-md-6">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            Email Address
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->email ?: '-' }}
                        </div>

                    </div>

                </div>

                {{-- PHONE --}}
                <div class="col-md-6">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            Phone Number
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->phone_number ?: '-' }}
                        </div>

                    </div>

                </div>

                {{-- NPWP --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            NPWP
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->npwp ?: '-' }}
                        </div>

                    </div>

                </div>

                {{-- BPJS HEALTH --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            BPJS Health
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->bpjs_health ?: '-' }}
                        </div>

                    </div>

                </div>

                {{-- BPJS EMPLOYMENT --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            BPJS Employment
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->bpjs_employment ?: '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
        ORGANIZATION
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex align-items-center gap-2">

                <div class="rounded-2 bg-light p-2">
                    <i class="bi bi-diagram-3 text-dark"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-0">
                        Organization
                    </h6>

                    <small class="text-muted">
                        Organizational structure and reporting information
                    </small>
                </div>

            </div>
        </div>

        <div class="card-body p-4">

            <div class="row g-3">

                {{-- DIVISION --}}
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small mb-2">
                            Division
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->division?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- POSITION --}}
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small mb-2">
                            Position
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->position?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- PROJECT --}}
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small mb-2">
                            Project / Placement
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->project?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- COST LEVEL --}}
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small mb-2">
                            Cost Level
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->costLevel?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- REPORT TO --}}
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small mb-2">
                            Report To
                        </div>

                        <div class="fw-semibold text-break">
                            {{ $employee->manager?->full_name ?? '-' }}
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>




    {{-- =========================================================
        EMPLOYMENT
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom py-3 px-4">

            <div class="d-flex align-items-center gap-2">

                <div class="rounded-2 bg-light p-2">
                    <i class="bi bi-briefcase text-dark"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-0">
                        Employment
                    </h6>

                    <small class="text-muted">
                        Employment status, contract period, and leave entitlement
                    </small>
                </div>

            </div>

        </div>


        <div class="card-body p-4">

            <div class="row g-3">

                {{-- STATUS --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            Employee Status
                        </div>

                        @if($employee->employee_status === 'Permanent')

                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i>
                                Permanent
                            </span>

                        @else

                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="bi bi-clock me-1"></i>
                                Contract
                            </span>

                        @endif

                    </div>

                </div>


                {{-- CONTRACT START --}}
                @if($employee->employee_status === 'Contract')

                    <div class="col-md-4">

                        <div class="border rounded-3 p-3 h-100">

                            <div class="text-muted small mb-2">
                                Contract Start Date
                            </div>

                            <div class="fw-semibold">
                                {{ $employee->contract_start_date?->format('d F Y') ?? '-' }}
                            </div>

                        </div>

                    </div>


                    {{-- CONTRACT END --}}
                    <div class="col-md-4">

                        <div class="border rounded-3 p-3 h-100">

                            <div class="text-muted small mb-2">
                                Contract End Date
                            </div>

                            <div class="fw-semibold">
                                {{ $employee->contract_end_date?->format('d F Y') ?? '-' }}
                            </div>

                        </div>

                    </div>

                @endif


                {{-- SPD LIMIT --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            SPD Limit
                        </div>

                        <div class="fw-semibold">
                            {{ number_format($employee->spd_limit) }}
                        </div>

                    </div>

                </div>


                {{-- TOTAL ANNUAL LEAVE --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            Total Annual Leave
                        </div>

                        <div class="fw-semibold">
                            {{ number_format($employee->total_annual_leave) }}
                            <span class="text-muted fw-normal">days</span>
                        </div>

                    </div>

                </div>


                {{-- ACTIVE ANNUAL LEAVE --}}
                <div class="col-md-4">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="text-muted small mb-2">
                            Active Annual Leave
                        </div>

                        <div class="fw-semibold">
                            {{ number_format(
                                $employee->total_annual_leave - $remainingAnnualLeave
                            ) }}
                            <span class="text-muted fw-normal">days</span>
                        </div>

                    </div>

                </div>


                {{-- REMAINING ANNUAL LEAVE --}}
                <div class="col-md-4">

                    <div class="border border-success-subtle bg-success-subtle rounded-3 p-3 h-100">

                        <div class="text-success small mb-2">
                            Remaining Annual Leave
                        </div>

                        <div class="fw-bold text-success fs-5">
                            {{ number_format($remainingAnnualLeave) }}
                            <span class="fw-normal fs-6">days</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        LOGIN ACCOUNT
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-bottom py-3 px-4">

            <div class="d-flex align-items-center gap-2">

                <div class="rounded-2 bg-light p-2">
                    <i class="bi bi-person-lock text-dark"></i>
                </div>

                <div>
                    <h6 class="fw-bold mb-0">
                        Login Account
                    </h6>

                    <small class="text-muted">
                        System access and assigned roles
                    </small>
                </div>

            </div>

        </div>


        <div class="card-body p-4">

            @if($employee->user)

                <div class="row g-4">

                    {{-- USERNAME --}}
                    <div class="col-md-4">

                        <div class="text-muted small mb-1">
                            Username
                        </div>

                        <div class="fw-semibold">
                            {{ $employee->user->username }}
                        </div>

                    </div>


                    {{-- ACCOUNT STATUS --}}
                    <div class="col-md-4">

                        <div class="text-muted small mb-1">
                            Account Status
                        </div>

                        <div>

                            @if($employee->user->is_active)

                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Active
                                </span>

                            @else

                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Inactive
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- ROLES --}}
                    <div class="col-md-4">

                        <div class="text-muted small mb-1">
                            Assigned Role
                        </div>

                        <div>

                            @forelse($employee->user->roles as $role)

                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1 mb-1">
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

                <div class="d-flex align-items-center gap-3 bg-light border rounded-3 p-3">

                    <div class="rounded-circle bg-white border d-flex align-items-center justify-content-center"
                         style="width:40px;height:40px;">

                        <i class="bi bi-info-circle text-secondary"></i>

                    </div>

                    <div>

                        <div class="fw-semibold">
                            No Login Account
                        </div>

                        <div class="text-muted small">
                            This employee does not have a login account.
                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        FOOTER SPACING
    ========================================================== --}}
    <div class="pb-4"></div>

</div>

@endsection
