@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Add Employee</h4>
        <p class="text-muted mb-0">Create a new employee record</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the following errors:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('employees.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        {{-- Personal Information --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Personal Information</h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="nik"
                            class="form-control @error('nik') is-invalid @enderror"
                            value="{{ old('nik') }}"
                            required
                        >

                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="full_name"
                            class="form-control @error('full_name') is-invalid @enderror"
                            value="{{ old('full_name') }}"
                            required
                        >

                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Birth Place</label>
                        <input
                            type="text"
                            name="birth_place"
                            class="form-control"
                            value="{{ old('birth_place') }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Birth Date</label>
                        <input
                            type="date"
                            name="birth_date"
                            class="form-control"
                            value="{{ old('birth_date') }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Religion <span class="text-danger">*</span></label>

                        <select name="religion" class="form-select" required>
                            <option value="">Select Religion</option>

                            @foreach ([
                                'Islam',
                                'Hindu',
                                'Buddha',
                                'Kristen',
                                'Katolik',
                                'Kong Hu Cu'
                            ] as $religion)

                                <option
                                    value="{{ $religion }}"
                                    @selected(old('religion') === $religion)
                                >
                                    {{ $religion }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>

                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>

                            <option value="Male" @selected(old('gender') === 'Male')>
                                Male
                            </option>

                            <option value="Female" @selected(old('gender') === 'Female')>
                                Female
                            </option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address</label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control"
                        >{{ old('address') }}</textarea>
                    </div>

                </div>

            </div>
        </div>

        {{-- Contact --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Contact & Identification</h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>

                        <input
                            type="text"
                            name="phone_number"
                            class="form-control"
                            value="{{ old('phone_number') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">NPWP</label>

                        <input
                            type="text"
                            name="npwp"
                            class="form-control"
                            value="{{ old('npwp') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">BPJS Health</label>

                        <input
                            type="text"
                            name="bpjs_health"
                            class="form-control"
                            value="{{ old('bpjs_health') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">BPJS Employment</label>

                        <input
                            type="text"
                            name="bpjs_employment"
                            class="form-control"
                            value="{{ old('bpjs_employment') }}"
                        >
                    </div>

                </div>

            </div>
        </div>

        {{-- Organization --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Organization</h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">
                            Division <span class="text-danger">*</span>
                        </label>

                       <select
                            name="division_id"
                            id="division_id"
                            class="form-select"
                            required
                        >
                            <option value="">Select Division</option>

                            @foreach ($divisions as $division)
                                <option
                                    value="{{ $division->id }}"
                                    @selected(old('division_id') == $division->id)
                                >
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Position <span class="text-danger">*</span>
                        </label>

                        <select
                            name="position_id"
                            id="position_id"
                            class="form-select"
                            required
                            disabled
                        >
                            <option value="">Select Position</option>

                            @foreach ($positions as $position)
                                <option
                                    value="{{ $position->id }}"
                                    data-division-id="{{ $position->division_id }}"
                                    @selected(old('position_id') == $position->id)
                                >
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Project</label>

                        <select
                            name="project_id"
                            class="form-select"
                        >
                            <option value="">No Project</option>

                            @foreach ($projects as $project)
                                <option
                                    value="{{ $project->id }}"
                                    @selected(old('project_id') == $project->id)
                                >
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Report To</label>

                        <select
                            name="report_to"
                            class="form-select"
                        >
                            <option value="">No Manager</option>

                            @foreach ($managers as $manager)
                                <option
                                    value="{{ $manager->id }}"
                                    @selected(old('report_to') == $manager->id)
                                >
                                    {{ $manager->full_name }} — {{ $manager->nik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </div>
        </div>

        {{-- Employment --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Employment</h6>
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
                            required
                        >
                            <option value="">Select Status</option>

                            <option
                                value="Contract"
                                @selected(old('employee_status') === 'Contract')
                            >
                                Contract
                            </option>

                            <option
                                value="Permanent"
                                @selected(old('employee_status') === 'Permanent')
                            >
                                Permanent
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 contract-field">
                        <label class="form-label">Contract Start Date</label>

                        <input
                            type="date"
                            name="contract_start_date"
                            class="form-control"
                            value="{{ old('contract_start_date') }}"
                        >
                    </div>

                    <div class="col-md-4 contract-field">
                        <label class="form-label">Contract End Date</label>

                        <input
                            type="date"
                            name="contract_end_date"
                            class="form-control"
                            value="{{ old('contract_end_date') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            SPD Limit
                        </label>

                        <input
                            type="number"
                            name="spd_limit"
                            class="form-control"
                            min="0"
                            step="0.01"
                            value="{{ old('spd_limit', 0) }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Total Annual Leave
                        </label>

                        <input
                            type="number"
                            name="total_annual_leave"
                            class="form-control"
                            min="0"
                            max="366"
                            step="0.5"
                            value="{{ old('total_annual_leave', 12) }}"
                        >
                    </div>

                </div>

            </div>
        </div>

        {{-- Photo & Account --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Photo & Login Account</h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Photo</label>

                        <input
                            type="file"
                            name="photo"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                        <small class="text-muted">
                            Maximum 2 MB.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="create_account"
                                value="1"
                                id="create_account"
                                @checked(old('create_account'))
                            >

                            <label
                                class="form-check-label"
                                for="create_account"
                            >
                                Create Login Account
                            </label>

                        </div>

                        <small class="text-muted">
                            Username will use the employee NIK.
                            Temporary password will be generated for first login.
                        </small>
                    </div>

                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ route('employees.index') }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-dark"
            >
                <i class="bi bi-check-lg me-1"></i>
                Save Employee
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
            field.style.display = status === 'Contract' ? '' : 'none';
        });
    }

    document
        .getElementById('employee_status')
        .addEventListener('change', toggleContractFields);

    toggleContractFields();

        const divisionSelect = document.getElementById('division_id');
    const positionSelect = document.getElementById('position_id');

    function filterPositions() {
        const divisionId = divisionSelect.value;
        const currentPosition = positionSelect.value;

        positionSelect.value = '';

        Array.from(positionSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.divisionId !== divisionId;
        });

        if (currentPosition) {
            const selectedOption = positionSelect.querySelector(
                `option[value="${currentPosition}"]`
            );

            if (selectedOption && !selectedOption.hidden) {
                positionSelect.value = currentPosition;
            }
        }

        positionSelect.disabled = !divisionId;
    }

    divisionSelect.addEventListener('change', filterPositions);

    filterPositions();
</script>
@endpush