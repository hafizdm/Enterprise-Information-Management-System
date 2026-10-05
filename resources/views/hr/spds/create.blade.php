@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-plus me-2"></i>
                Create SPD
            </h4>
            <p class="text-muted mb-0">
                Create a new business trip request for an employee.
            </p>
        </div>

        <div>
            <a href="{{ route('spds.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Please correct the following errors:
            </div>

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('spds.store') }}" method="POST">
        @csrf

        {{-- Employee Information --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person me-2"></i>
                    Employee Information
                </h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Employee --}}
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label fw-semibold">
                            Employee <span class="text-danger">*</span>
                        </label>

                        <select
                            name="employee_id"
                            id="employee_id"
                            class="form-select @error('employee_id') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Select Employee --</option>

                            @foreach ($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    data-manager="{{ $employee->manager?->full_name ?? 'Not assigned' }}"
                                    data-spd-limit="{{ number_format((float) $employee->spd_limit, 2, '.', '') }}"
                                    data-cost-level="{{ $employee->costLevel?->name ?? 'Not assigned' }}"
                                    data-meals-domestic="{{ (float) ($employee->costLevel?->meals_domestic ?? 0) }}"
                                    data-allowance-domestic="{{ (float) ($employee->costLevel?->allowance_domestic ?? 0) }}"
                                    data-meals-international="{{ (float) ($employee->costLevel?->meals_international ?? 0) }}"
                                    data-allowance-international="{{ (float) ($employee->costLevel?->allowance_international ?? 0) }}"
                                    @selected(old('employee_id') == $employee->id)
                                >
                                    {{ $employee->full_name }}
                                    @if ($employee->nik)
                                        — {{ $employee->nik }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('employee_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Manager --}}
                    <div class="col-md-6">
                        <label for="manager_display" class="form-label fw-semibold">
                            Manager
                        </label>

                        <input
                            type="text"
                            id="manager_display"
                            class="form-control bg-light"
                            value=""
                            readonly
                        >
                    </div>

                    {{-- SPD Limit --}}
                    <div class="col-md-4">
                        <label for="spd_limit_display" class="form-label fw-semibold">
                            Available SPD Limit
                        </label>

                        <input
                            type="text"
                            id="spd_limit_display"
                            class="form-control bg-light"
                            value=""
                            readonly
                        >
                    </div>

                    {{-- Cost Level --}}
                    <div class="col-md-4">
                        <label for="cost_level_display" class="form-label fw-semibold">
                            Cost Level
                        </label>

                        <input
                            type="text"
                            id="cost_level_display"
                            class="form-control bg-light"
                            value=""
                            readonly
                        >
                    </div>
                </div>

            </div>
        </div>


        {{-- Project Information --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-building me-2"></i>
                    Project Information
                </h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- SPD Project --}}
                    <div class="col-md-6">
                        <label for="project_id" class="form-label fw-semibold">
                            SPD Project <span class="text-danger">*</span>
                        </label>

                        <select
                            name="project_id"
                            id="project_id"
                            class="form-select @error('project_id') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Select Project --</option>

                            @foreach ($projects as $project)
                                <option
                                    value="{{ $project->id }}"
                                    data-cost-center="{{ $project->cost_center ?? 'Not assigned' }}"
                                    data-cost-control="{{ $project->approvalEmployee?->full_name ?? 'Not assigned' }}"
                                    data-location="{{ $project->location ?? 'Not assigned' }}"
                                    @selected(old('project_id') == $project->id)
                                >
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('project_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Select the project that will bear the cost of this SPD.
                        </small>
                    </div>

                    {{-- Cost Center --}}
                    <div class="col-md-3">
                        <label for="cost_center_display" class="form-label fw-semibold">
                            Cost Center
                        </label>

                        <input
                            type="text"
                            id="cost_center_display"
                            class="form-control bg-light"
                            value=""
                            readonly
                        >
                    </div>

                    {{-- Cost Control --}}
                    <div class="col-md-3">
                        <label for="cost_control_display" class="form-label fw-semibold">
                            Cost Control
                        </label>

                        <input
                            type="text"
                            id="cost_control_display"
                            class="form-control bg-light"
                            value=""
                            readonly
                        >
                    </div>

                </div>

            </div>
        </div>


        {{-- Travel Information --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-airplane me-2"></i>
                    Travel Information
                </h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Travel Type --}}
                    <div class="col-md-4">
                        <label for="travel_type" class="form-label fw-semibold">
                            Travel Type <span class="text-danger">*</span>
                        </label>

                        <select
                            name="travel_type"
                            id="travel_type"
                            class="form-select @error('travel_type') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Select Travel Type --</option>
                            <option value="domestic" @selected(old('travel_type') === 'domestic')>
                                Domestic
                            </option>
                            <option value="international" @selected(old('travel_type') === 'international')>
                                International
                            </option>
                        </select>

                        @error('travel_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Transportation --}}
                    <div class="col-md-4">
                        <label for="transportation" class="form-label fw-semibold">
                            Transportation <span class="text-danger">*</span>
                        </label>

                        <select
                            name="transportation"
                            id="transportation"
                            class="form-select @error('transportation') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Select Transportation --</option>
                            <option value="car" @selected(old('transportation') === 'car')>
                                Car
                            </option>
                            <option value="plane" @selected(old('transportation') === 'plane')>
                                Plane
                            </option>
                            <option value="ship" @selected(old('transportation') === 'ship')>
                                Ship
                            </option>
                            <option value="other" @selected(old('transportation') === 'other')>
                                Other
                            </option>
                        </select>

                        @error('transportation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Advance Payment --}}
                    <div class="col-md-4">
                        <label for="advance_payment" class="form-label fw-semibold">
                            Advance Payment <span class="text-danger">*</span>
                        </label>

                        <select
                            name="advance_payment"
                            id="advance_payment"
                            class="form-select @error('advance_payment') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Select --</option>
                            <option value="1" @selected(old('advance_payment') === '1')>
                                Yes
                            </option>
                            <option value="0" @selected(old('advance_payment') === '0')>
                                No
                            </option>
                        </select>

                        @error('advance_payment')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- From --}}
                    <div class="col-md-6">
                        <label for="from" class="form-label fw-semibold">
                            From <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="from"
                            id="from"
                            class="form-control @error('from') is-invalid @enderror"
                            value="{{ old('from') }}"
                            required
                        >

                        @error('from')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Destination --}}
                    <div class="col-md-6">
                        <label for="destination" class="form-label fw-semibold">
                            Destination <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="destination"
                            id="destination"
                            class="form-control @error('destination') is-invalid @enderror"
                            value="{{ old('destination') }}"
                            required
                        >

                        @error('destination')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Departure --}}
                    <div class="col-md-6">
                        <label for="date_departure" class="form-label fw-semibold">
                            Departure Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="date_departure"
                            id="date_departure"
                            class="form-control @error('date_departure') is-invalid @enderror"
                            value="{{ old('date_departure') }}"
                            required
                        >

                        @error('date_departure')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Return --}}
                    <div class="col-md-6">
                        <label for="date_return" class="form-label fw-semibold">
                            Return Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="date_return"
                            id="date_return"
                            class="form-control @error('date_return') is-invalid @enderror"
                            value="{{ old('date_return') }}"
                            required
                        >

                        @error('date_return')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Total Days --}}
                    <div class="col-md-4">
                        <label for="total_days_display" class="form-label fw-semibold">
                            Total Days
                        </label>

                        <input
                            type="text"
                            id="total_days_display"
                            class="form-control bg-light"
                            value="0"
                            readonly
                        >
                    </div>

                </div>

            </div>
        </div>


        {{-- Cost & Allowance --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-cash-stack me-2"></i>
                    Cost & Allowance
                </h6>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Meals Per Day --}}
                    <div class="col-md-3">
                        <label for="meals_per_day_display" class="form-label fw-semibold">
                            Meals / Day
                        </label>

                        <input
                            type="text"
                            id="meals_per_day_display"
                            class="form-control bg-light"
                            value="Rp 0"
                            readonly
                        >
                    </div>

                    {{-- Allowance Per Day --}}
                    <div class="col-md-3">
                        <label for="allowance_per_day_display" class="form-label fw-semibold">
                            Allowance / Day
                        </label>

                        <input
                            type="text"
                            id="allowance_per_day_display"
                            class="form-control bg-light"
                            value="Rp 0"
                            readonly
                        >
                    </div>

                    {{-- Local Transport --}}
                    <div class="col-md-3">
                        <label for="local_transport" class="form-label fw-semibold">
                            Local Transport
                        </label>

                        <input
                            type="number"
                            name="local_transport"
                            id="local_transport"
                            class="form-control @error('local_transport') is-invalid @enderror"
                            value="{{ old('local_transport', 0) }}"
                            min="0"
                            step="0.01"
                        >

                        @error('local_transport')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Contingencies --}}
                    <div class="col-md-3">
                        <label for="contingencies" class="form-label fw-semibold">
                            Contingencies
                        </label>

                        <input
                            type="number"
                            name="contingencies"
                            id="contingencies"
                            class="form-control @error('contingencies') is-invalid @enderror"
                            value="{{ old('contingencies', 0) }}"
                            min="0"
                            step="0.01"
                        >

                        @error('contingencies')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Total Meals --}}
                    <div class="col-md-4">
                        <label for="total_meals_display" class="form-label fw-semibold">
                            Total Meals
                        </label>

                        <input
                            type="text"
                            id="total_meals_display"
                            class="form-control bg-light"
                            value="Rp 0"
                            readonly
                        >
                    </div>

                    {{-- Total Allowance --}}
                    <div class="col-md-4">
                        <label for="total_allowance_display" class="form-label fw-semibold">
                            Total Allowance
                        </label>

                        <input
                            type="text"
                            id="total_allowance_display"
                            class="form-control bg-light"
                            value="Rp 0"
                            readonly
                        >
                    </div>

                    {{-- Balance Received --}}
                    <div class="col-md-4">
                        <label for="balance_received_display" class="form-label fw-semibold">
                            Balance Received
                        </label>

                        <input
                            type="text"
                            id="balance_received_display"
                            class="form-control bg-light fw-bold"
                            value="Rp 0"
                            readonly
                        >
                    </div>

                </div>

            </div>
        </div>


        {{-- Purpose & Note --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-card-text me-2"></i>
                    Purpose & Note
                </h6>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label for="purpose" class="form-label fw-semibold">
                        Purpose <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="purpose"
                        id="purpose"
                        rows="4"
                        class="form-control @error('purpose') is-invalid @enderror"
                        required
                    >{{ old('purpose') }}</textarea>

                    @error('purpose')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <label for="note" class="form-label fw-semibold">
                        Note
                    </label>

                    <textarea
                        name="note"
                        id="note"
                        rows="3"
                        class="form-control @error('note') is-invalid @enderror"
                    >{{ old('note') }}</textarea>

                    @error('note')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>
        </div>


        {{-- Actions --}}
        <div class="d-flex justify-content-end gap-2 mb-4">

            <a href="{{ route('spds.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>
                Cancel
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i>
                Create SPD
            </button>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const employeeSelect = document.getElementById('employee_id');
    const projectSelect = document.getElementById('project_id');

    const managerDisplay =
        document.getElementById('manager_display');

    const spdLimitDisplay =
        document.getElementById('spd_limit_display');

    const costLevelDisplay =
        document.getElementById('cost_level_display');

    const employeeProjectDisplay =
        document.getElementById('employee_project_display');

    const costCenterDisplay =
        document.getElementById('cost_center_display');

    const costControlDisplay =
        document.getElementById('cost_control_display');

    const projectLocationDisplay =
        document.getElementById('project_location_display');

    const travelTypeSelect =
        document.getElementById('travel_type');

    const dateDeparture =
        document.getElementById('date_departure');

    const dateReturn =
        document.getElementById('date_return');

    const totalDaysDisplay =
        document.getElementById('total_days_display');

    const mealsPerDayDisplay =
        document.getElementById('meals_per_day_display');

    const allowancePerDayDisplay =
        document.getElementById('allowance_per_day_display');

    const localTransport =
        document.getElementById('local_transport');

    const contingencies =
        document.getElementById('contingencies');

    const totalMealsDisplay =
        document.getElementById('total_meals_display');

    const totalAllowanceDisplay =
        document.getElementById('total_allowance_display');

    const balanceReceivedDisplay =
        document.getElementById('balance_received_display');


    function formatCurrency(value) {
        return 'Rp ' + Number(value || 0).toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }
        );
    }


    function updateEmployeeInformation() {

        const selectedOption =
            employeeSelect.options[employeeSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {

            managerDisplay.value = '';
            spdLimitDisplay.value = '';
            costLevelDisplay.value = '';
            employeeProjectDisplay.value = '';

            updateRates();

            return;
        }

        managerDisplay.value =
            selectedOption.dataset.manager || 'Not assigned';

        spdLimitDisplay.value =
            selectedOption.dataset.spdLimit || '0.00';

        costLevelDisplay.value =
            selectedOption.dataset.costLevel || 'Not assigned';

        /*
         * Employee master project is DISPLAY ONLY.
         *
         * IMPORTANT:
         * It must NOT change the SPD Project selection.
         *
         * The SPD Project is independently selected by HRD
         * because the business trip can be charged to another
         * project.
         */
        employeeProjectDisplay.value =
            'Employee master project is stored separately';

        updateRates();
    }


    function updateProjectInformation() {

        const selectedOption =
            projectSelect.options[projectSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {

            costCenterDisplay.value = '';
            costControlDisplay.value = '';
            projectLocationDisplay.value = '';

            return;
        }

        costCenterDisplay.value =
            selectedOption.dataset.costCenter || 'Not assigned';

        costControlDisplay.value =
            selectedOption.dataset.costControl || 'Not assigned';

        projectLocationDisplay.value =
            selectedOption.dataset.location || 'Not assigned';
    }


    function updateRates() {

        const selectedEmployee =
            employeeSelect.options[
                employeeSelect.selectedIndex
            ];

        if (!selectedEmployee || !selectedEmployee.value) {

            mealsPerDayDisplay.value = 'Rp 0';
            allowancePerDayDisplay.value = 'Rp 0';

            updateTotals();

            return;
        }

        const travelType =
            travelTypeSelect.value;

        let meals = 0;
        let allowance = 0;

        if (travelType === 'domestic') {

            meals =
                Number(
                    selectedEmployee.dataset.mealsDomestic || 0
                );

            allowance =
                Number(
                    selectedEmployee.dataset.allowanceDomestic || 0
                );

        } else if (travelType === 'international') {

            meals =
                Number(
                    selectedEmployee.dataset.mealsInternational || 0
                );

            allowance =
                Number(
                    selectedEmployee.dataset.allowanceInternational || 0
                );
        }

        mealsPerDayDisplay.value =
            formatCurrency(meals);

        allowancePerDayDisplay.value =
            formatCurrency(allowance);

        updateTotals();
    }


    function calculateTotalDays() {

        if (
            !dateDeparture.value ||
            !dateReturn.value
        ) {
            totalDaysDisplay.value = '0';
            return 0;
        }

        const departure =
            new Date(dateDeparture.value);

        const returnDate =
            new Date(dateReturn.value);

        if (
            Number.isNaN(departure.getTime()) ||
            Number.isNaN(returnDate.getTime())
        ) {
            totalDaysDisplay.value = '0';
            return 0;
        }

        const difference =
            returnDate.getTime() -
            departure.getTime();

        const totalDays =
            Math.floor(
                difference /
                (1000 * 60 * 60 * 24)
            ) + 1;

        if (totalDays < 1) {
            totalDaysDisplay.value = '0';
            return 0;
        }

        totalDaysDisplay.value =
            totalDays.toString();

        return totalDays;
    }


    function updateTotals() {

        const totalDays =
            calculateTotalDays();

        const selectedEmployee =
            employeeSelect.options[
                employeeSelect.selectedIndex
            ];

        if (
            !selectedEmployee ||
            !selectedEmployee.value ||
            totalDays <= 0
        ) {
            totalMealsDisplay.value = 'Rp 0';
            totalAllowanceDisplay.value = 'Rp 0';
            balanceReceivedDisplay.value = 'Rp 0';

            return;
        }

        const travelType =
            travelTypeSelect.value;

        let meals = 0;
        let allowance = 0;

        if (travelType === 'domestic') {

            meals =
                Number(
                    selectedEmployee.dataset.mealsDomestic || 0
                );

            allowance =
                Number(
                    selectedEmployee.dataset.allowanceDomestic || 0
                );

        } else if (travelType === 'international') {

            meals =
                Number(
                    selectedEmployee.dataset.mealsInternational || 0
                );

            allowance =
                Number(
                    selectedEmployee.dataset.allowanceInternational || 0
                );
        }

        const totalMeals =
            meals * totalDays;

        const totalAllowance =
            allowance * totalDays;

        const localTransportValue =
            Number(localTransport.value || 0);

        const contingenciesValue =
            Number(contingencies.value || 0);

        const balanceReceived =
            totalMeals +
            totalAllowance +
            localTransportValue +
            contingenciesValue;

        totalMealsDisplay.value =
            formatCurrency(totalMeals);

        totalAllowanceDisplay.value =
            formatCurrency(totalAllowance);

        balanceReceivedDisplay.value =
            formatCurrency(balanceReceived);
    }


    /*
     * Employee changes:
     * Update employee-related information only.
     *
     * Do NOT change projectSelect.value here.
     */
    employeeSelect.addEventListener(
        'change',
        updateEmployeeInformation
    );


    /*
     * Project changes:
     * Update Cost Center, Cost Control and Project Location.
     */
    projectSelect.addEventListener(
        'change',
        updateProjectInformation
    );


    travelTypeSelect.addEventListener(
        'change',
        updateRates
    );

    dateDeparture.addEventListener(
        'change',
        updateTotals
    );

    dateReturn.addEventListener(
        'change',
        updateTotals
    );

    localTransport.addEventListener(
        'input',
        updateTotals
    );

    contingencies.addEventListener(
        'input',
        updateTotals
    );


    /*
     * Initial page load.
     *
     * This is important when validation fails and Laravel
     * sends the user back with old('employee_id') and
     * old('project_id').
     */
    updateEmployeeInformation();
    updateProjectInformation();
    updateRates();

});
</script>

@endsection
