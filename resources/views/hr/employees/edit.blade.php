@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">Edit Employee</h4>

            <p class="text-muted mb-0">
                Update employee information
            </p>
        </div>

        <a href="{{ route('employees.show', $employee) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Back

        </a>

    </div>

    <form action="{{ route('employees.update', $employee) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- Personal Information --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h6 class="fw-bold mb-0">

                    <i class="bi bi-person me-2"></i>

                    Personal Information

                </h6>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            NIK <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nik"
                            value="{{ old('nik', $employee->nik) }}"
                            class="form-control @error('nik') is-invalid @enderror"
                        >

                        @error('nik')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Full Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            value="{{ old('full_name', $employee->full_name) }}"
                            class="form-control @error('full_name') is-invalid @enderror"
                        >

                        @error('full_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Birth Place
                        </label>

                        <input
                            type="text"
                            name="birth_place"
                            value="{{ old('birth_place', $employee->birth_place) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Birth Date
                        </label>

                        <input
                            type="date"
                            name="birth_date"
                            value="{{ old('birth_date', $employee->birth_date?->format('Y-m-d')) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Religion <span class="text-danger">*</span>
                        </label>

                        <select
                            name="religion"
                            class="form-select @error('religion') is-invalid @enderror"
                        >

                            <option value="">
                                Select Religion
                            </option>

                            @foreach([
                                'Islam',
                                'Hindu',
                                'Buddha',
                                'Kristen',
                                'Katolik',
                                'Kong Hu Cu'
                            ] as $religion)

                                <option
                                    value="{{ $religion }}"
                                    @selected(old('religion', $employee->religion) === $religion)
                                >
                                    {{ $religion }}
                                </option>

                            @endforeach

                        </select>

                        @error('religion')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Gender <span class="text-danger">*</span>
                        </label>

                        <select
                            name="gender"
                            class="form-select @error('gender') is-invalid @enderror"
                        >

                            <option value="">
                                Select Gender
                            </option>

                            <option
                                value="Male"
                                @selected(old('gender', $employee->gender) === 'Male')
                            >
                                Male
                            </option>

                            <option
                                value="Female"
                                @selected(old('gender', $employee->gender) === 'Female')
                            >
                                Female
                            </option>

                        </select>

                        @error('gender')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-12">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control"
                        >{{ old('address', $employee->address) }}</textarea>

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

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $employee->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            name="phone_number"
                            value="{{ old('phone_number', $employee->phone_number) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            NPWP
                        </label>

                        <input
                            type="text"
                            name="npwp"
                            value="{{ old('npwp', $employee->npwp) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            BPJS Health
                        </label>

                        <input
                            type="text"
                            name="bpjs_health"
                            value="{{ old('bpjs_health', $employee->bpjs_health) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            BPJS Employment
                        </label>

                        <input
                            type="text"
                            name="bpjs_employment"
                            value="{{ old('bpjs_employment', $employee->bpjs_employment) }}"
                            class="form-control"
                        >

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

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Division <span class="text-danger">*</span>
                        </label>

                        <select
                            name="division_id"
                            id="division_id"
                            class="form-select @error('division_id') is-invalid @enderror"
                        >

                            <option value="">
                                Select Division
                            </option>

                            @foreach($divisions as $division)

                                <option
                                    value="{{ $division->id }}"
                                    @selected(old('division_id', $employee->division_id) == $division->id)
                                >
                                    {{ $division->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('division_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Position <span class="text-danger">*</span>
                        </label>

                        <select
                            name="position_id"
                            id="position_id"
                            class="form-select @error('position_id') is-invalid @enderror"
                        >

                            <option value="">
                                Select Position
                            </option>

                            @foreach($positions as $position)

                                <option
                                    value="{{ $position->id }}"
                                    @selected(old('position_id', $employee->position_id) == $position->id)
                                >
                                    {{ $position->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('position_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Project / Placement
                        </label>

                        <select
                            name="project_id"
                            class="form-select"
                        >

                            <option value="">
                                Select Project
                            </option>

                            @foreach($projects as $project)

                                <option
                                    value="{{ $project->id }}"
                                    @selected(old('project_id', $employee->project_id) == $project->id)
                                >
                                    {{ $project->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- Cost Level --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Cost Level <span class="text-danger">*</span>
                        </label>

                        <select
                            name="cost_level_id"
                            class="form-select @error('cost_level_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                Select Cost Level
                            </option>

                            @foreach($costLevels as $costLevel)

                                <option
                                    value="{{ $costLevel->id }}"
                                    @selected(old('cost_level_id', $employee->cost_level_id) == $costLevel->id)
                                >
                                    {{ $costLevel->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('cost_level_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Report To
                        </label>

                        <select
                            name="report_to"
                            class="form-select"
                        >

                            <option value="">
                                No Manager
                            </option>

                            @foreach($managers as $manager)

                                <option
                                    value="{{ $manager->id }}"
                                    @selected(old('report_to', $employee->report_to) == $manager->id)
                                >
                                    {{ $manager->full_name }}
                                </option>

                            @endforeach

                        </select>

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

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Employee Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="employee_status"
                            id="employee_status"
                            class="form-select"
                        >

                            <option
                                value="Permanent"
                                @selected(old('employee_status', $employee->employee_status) === 'Permanent')
                            >
                                Permanent
                            </option>

                            <option
                                value="Contract"
                                @selected(old('employee_status', $employee->employee_status) === 'Contract')
                            >
                                Contract
                            </option>

                        </select>

                    </div>

                    <div class="col-md-4 contract-field">

                        <label class="form-label">
                            Contract Start Date
                        </label>

                        <input
                            type="date"
                            name="contract_start_date"
                            value="{{ old('contract_start_date', $employee->contract_start_date?->format('Y-m-d')) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4 contract-field">

                        <label class="form-label">
                            Contract End Date
                        </label>

                        <input
                            type="date"
                            name="contract_end_date"
                            value="{{ old('contract_end_date', $employee->contract_end_date?->format('Y-m-d')) }}"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            SPD Limit
                        </label>

                        <input
                            type="number"
                            name="spd_limit"
                            value="{{ old('spd_limit', $employee->spd_limit) }}"
                            min="0"
                            step="0.01"
                            class="form-control"
                        >

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Total Annual Leave
                        </label>

                        <input
                            type="number"
                            name="total_annual_leave"
                            value="{{ old('total_annual_leave', $employee->total_annual_leave) }}"
                            min="0"
                            max="366"
                            step="0.01"
                            class="form-control @error('total_annual_leave') is-invalid @enderror"
                        >

                        @error('total_annual_leave')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Annual leave entitlement.
                        </div>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Active Annual Leave
                        </label>

                        <div class="form-control bg-light">
                            {{ number_format($activeAnnualLeave) }} days
                        </div>

                        <div class="form-text">
                            Pending Manager + Approved.
                        </div>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label">
                            Remaining Annual Leave
                        </label>

                        <div class="form-control bg-light fw-semibold">
                            {{ number_format($remainingAnnualLeave) }} days
                        </div>

                        <div class="form-text">
                            Available annual leave balance.
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Photo --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h6 class="fw-bold mb-0">

                    <i class="bi bi-image me-2"></i>

                    Photo

                </h6>

            </div>

            <div class="card-body">

                @if($employee->photo)

                    <div class="mb-3">

                        <img
                            src="{{ asset('storage/' . $employee->photo) }}"
                            alt="{{ $employee->full_name }}"
                            class="rounded"
                            style="width: 120px; height: 120px; object-fit: cover;"
                        >

                    </div>

                @endif

                <label class="form-label">
                    Change Photo
                </label>

                <input
                    type="file"
                    name="photo"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="form-control @error('photo') is-invalid @enderror"
                >

                @error('photo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                <div class="form-text">
                    Maximum 2 MB. JPG, JPEG, PNG, or WEBP.
                </div>

            </div>

        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ route('employees.show', $employee) }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-dark"
            >
                <i class="bi bi-check-lg me-1"></i>
                Update Employee
            </button>

        </div>

    </form>

</div>

@endsection

@push('scripts')

<script>

    function toggleContractFields() {

        const status = document.getElementById('employee_status').value;

        const fields = document.querySelectorAll('.contract-field');

        fields.forEach(field => {

            field.style.display =
                status === 'Contract' ? '' : 'none';

        });

    }

    document
        .getElementById('employee_status')
        .addEventListener('change', toggleContractFields);

    toggleContractFields();

</script>

@endpush