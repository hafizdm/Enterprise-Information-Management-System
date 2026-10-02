@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Create SPD</h4>

        <p class="text-muted mb-0">
            Create a business trip request for an employee
        </p>
    </div>

    <a
        href="{{ route('spds.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>

</div>

<form
    action="{{ route('spds.store') }}"
    method="POST"
>

    @csrf

    {{-- ========================================================= --}}
    {{-- Employee Information --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h6 class="mb-0">
                <i class="bi bi-person-vcard me-2"></i>
                Employee Information
            </h6>

        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- Employee --}}
                <div class="col-md-6">

                    <label
                        for="employee_id"
                        class="form-label"
                    >
                        Employee
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="employee_id"
                        id="employee_id"
                        class="form-select @error('employee_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Employee
                        </option>

                        @foreach ($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                data-manager="{{ $employee->manager?->full_name ?? 'No manager assigned' }}"
                                data-spd-limit="{{ $employee->spd_limit }}"
                                data-project="{{ $employee->project?->name ?? 'No project assigned' }}"
                                data-project-id="{{ $employee->project?->id ?? '' }}"
                                data-cost-center="{{ $employee->project?->cost_center ?? 'Not assigned' }}"
                                data-cost-control="{{ $employee->project?->approvalEmployee?->full_name ?? 'Not assigned' }}"
                                data-cost-level="{{ $employee->costLevel?->name ?? 'No Cost Level assigned' }}"
                                data-meals-domestic="{{ $employee->costLevel?->meals_domestic ?? 0 }}"
                                data-allowance-domestic="{{ $employee->costLevel?->allowance_domestic ?? 0 }}"
                                data-meals-international="{{ $employee->costLevel?->meals_international ?? 0 }}"
                                data-allowance-international="{{ $employee->costLevel?->allowance_international ?? 0 }}"
                                @selected(old('employee_id') == $employee->id)
                            >
                                {{ $employee->full_name }}
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

                    <label class="form-label">
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
                <div class="col-md-6">

                    <label class="form-label">
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

                {{-- Project --}}
                <div class="col-md-6">

                    <label class="form-label">
                        Project
                    </label>

                    <input
                        type="text"
                        id="project_display"
                        class="form-control bg-light"
                        value=""
                        readonly
                    >

                </div>

                {{-- Cost Center --}}
                <div class="col-md-6">

                    <label class="form-label">
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
                <div class="col-md-6">

                    <label class="form-label">
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

                {{-- Cost Level --}}
                <div class="col-md-6">

                    <label class="form-label">
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


    {{-- ========================================================= --}}
    {{-- Business Trip --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h6 class="mb-0">
                <i class="bi bi-airplane me-2"></i>
                Business Trip Information
            </h6>

        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- Travel Type --}}
                <div class="col-md-6">

                    <label
                        for="travel_type"
                        class="form-label"
                    >
                        Travel Type
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="travel_type"
                        id="travel_type"
                        class="form-select @error('travel_type') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Travel Type
                        </option>

                        <option
                            value="domestic"
                            @selected(old('travel_type') === 'domestic')
                        >
                            Domestic
                        </option>

                        <option
                            value="international"
                            @selected(old('travel_type') === 'international')
                        >
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
                <div class="col-md-6">

                    <label
                        for="transportation"
                        class="form-label"
                    >
                        Transportation
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="transportation"
                        id="transportation"
                        class="form-select @error('transportation') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Transportation
                        </option>

                        <option
                            value="car"
                            @selected(old('transportation') === 'car')
                        >
                            Car
                        </option>

                        <option
                            value="plane"
                            @selected(old('transportation') === 'plane')
                        >
                            Plane
                        </option>

                        <option
                            value="ship"
                            @selected(old('transportation') === 'ship')
                        >
                            Ship
                        </option>

                        <option
                            value="other"
                            @selected(old('transportation') === 'other')
                        >
                            Other
                        </option>

                    </select>

                    @error('transportation')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                {{-- From --}}
                <div class="col-md-6">

                    <label
                        for="from"
                        class="form-label"
                    >
                        From
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="from"
                        id="from"
                        class="form-control @error('from') is-invalid @enderror"
                        value="{{ old('from') }}"
                        placeholder="e.g. Jakarta"
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

                    <label
                        for="destination"
                        class="form-label"
                    >
                        Destination
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="destination"
                        id="destination"
                        class="form-control @error('destination') is-invalid @enderror"
                        value="{{ old('destination') }}"
                        placeholder="e.g. Sumbawa Barat"
                        required
                    >

                    @error('destination')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                {{-- Date Departure --}}
                <div class="col-md-4">

                    <label
                        for="date_departure"
                        class="form-label"
                    >
                        Date Departure
                        <span class="text-danger">*</span>
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

                {{-- Date Return --}}
                <div class="col-md-4">

                    <label
                        for="date_return"
                        class="form-label"
                    >
                        Date Return
                        <span class="text-danger">*</span>
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

                    <label
                        for="total_days_display"
                        class="form-label"
                    >
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

                {{-- Purpose --}}
                <div class="col-md-12">

                    <label
                        for="purpose"
                        class="form-label"
                    >
                        Purpose
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="purpose"
                        id="purpose"
                        rows="3"
                        class="form-control @error('purpose') is-invalid @enderror"
                        placeholder="Describe the purpose of this business trip..."
                        required
                    >{{ old('purpose') }}</textarea>

                    @error('purpose')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                {{-- Note --}}
                <div class="col-md-12">

                    <label
                        for="note"
                        class="form-label"
                    >
                        Note
                    </label>

                    <textarea
                        name="note"
                        id="note"
                        rows="2"
                        class="form-control @error('note') is-invalid @enderror"
                        placeholder="Additional information..."
                    >{{ old('note') }}</textarea>

                    @error('note')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Allowance & Payment --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h6 class="mb-0">
                <i class="bi bi-cash-stack me-2"></i>
                Allowance & Payment
            </h6>

        </div>

        <div class="card-body">

            <div class="row g-3">

                {{-- Meals --}}
                <div class="col-md-6">

                    <label
                        for="meals_per_day"
                        class="form-label"
                    >
                        Meals / Day
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="meals_per_day"
                        id="meals_per_day"
                        class="form-control bg-light @error('meals_per_day') is-invalid @enderror"
                        value="0"
                        min="0"
                        step="0.01"
                        readonly
                    >

                    <div class="form-text">
                        Automatically determined from Employee Cost Level and Travel Type.
                    </div>

                    @error('meals_per_day')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                {{-- Allowance --}}
                <div class="col-md-6">

                    <label
                        for="allowance_per_day"
                        class="form-label"
                    >
                        Allowance / Day
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="allowance_per_day"
                        id="allowance_per_day"
                        class="form-control bg-light @error('allowance_per_day') is-invalid @enderror"
                        value="0"
                        min="0"
                        step="0.01"
                        readonly
                    >

                    <div class="form-text">
                        Automatically determined from Employee Cost Level and Travel Type.
                    </div>

                    @error('allowance_per_day')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                {{-- Local Transport --}}
                <div class="col-md-6">

                    <label
                        for="local_transport"
                        class="form-label"
                    >
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
                <div class="col-md-6">

                    <label
                        for="contingencies"
                        class="form-label"
                    >
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

                {{-- Advance Payment --}}
                <div class="col-md-6">

                    <label
                        for="advance_payment"
                        class="form-label"
                    >
                        Advance Payment
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="advance_payment"
                        id="advance_payment"
                        class="form-select @error('advance_payment') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select
                        </option>

                        <option
                            value="1"
                            @selected(old('advance_payment') === '1')
                        >
                            Yes
                        </option>

                        <option
                            value="0"
                            @selected(old('advance_payment') === '0')
                        >
                            No
                        </option>

                    </select>

                    @error('advance_payment')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

                {{-- Balance Received --}}
                <div class="col-md-6">

                    <label class="form-label">
                        Balance Received
                    </label>

                    <input
                        type="text"
                        id="balance_received_display"
                        class="form-control bg-light fw-semibold"
                        value="0.00"
                        readonly
                    >

                    <div class="form-text">
                        Calculated automatically.
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Actions --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-end gap-2">

        <a
            href="{{ route('spds.index') }}"
            class="btn btn-outline-secondary"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="btn btn-primary"
        >
            <i class="bi bi-check-lg me-1"></i>
            Create SPD
        </button>

    </div>

</form>


{{-- ============================================================= --}}
{{-- JavaScript --}}
{{-- ============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const employeeSelect =
        document.getElementById('employee_id');

    const travelTypeSelect =
        document.getElementById('travel_type');

    const managerDisplay =
        document.getElementById('manager_display');

    const spdLimitDisplay =
        document.getElementById('spd_limit_display');

    const projectDisplay =
        document.getElementById('project_display');

    const costCenterDisplay =
        document.getElementById('cost_center_display');

    const costControlDisplay =
        document.getElementById('cost_control_display');

    const costLevelDisplay =
        document.getElementById('cost_level_display');

    const dateDeparture =
        document.getElementById('date_departure');

    const dateReturn =
        document.getElementById('date_return');

    const totalDaysDisplay =
        document.getElementById('total_days_display');

    const mealsPerDay =
        document.getElementById('meals_per_day');

    const allowancePerDay =
        document.getElementById('allowance_per_day');

    const localTransport =
        document.getElementById('local_transport');

    const contingencies =
        document.getElementById('contingencies');

    const balanceReceivedDisplay =
        document.getElementById('balance_received_display');


    /*
    |--------------------------------------------------------------------------
    | Format Number
    |--------------------------------------------------------------------------
    */

    function formatNumber(value) {

        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);

    }


    /*
    |--------------------------------------------------------------------------
    | Employee Information
    |--------------------------------------------------------------------------
    */

    function updateEmployeeInformation() {

        const selectedOption =
            employeeSelect.options[
                employeeSelect.selectedIndex
            ];

        if (
            !selectedOption ||
            !selectedOption.value
        ) {

            managerDisplay.value = '';
            spdLimitDisplay.value = '';
            projectDisplay.value = '';
            costCenterDisplay.value = '';
            costControlDisplay.value = '';
            costLevelDisplay.value = '';

            mealsPerDay.value = '0';
            allowancePerDay.value = '0';

            updateBalance();

            return;
        }

        managerDisplay.value =
            selectedOption.dataset.manager || '';

        spdLimitDisplay.value =
            selectedOption.dataset.spdLimit || '0';

        projectDisplay.value =
            selectedOption.dataset.project || '';

        costCenterDisplay.value =
            selectedOption.dataset.costCenter || '';

        costControlDisplay.value =
            selectedOption.dataset.costControl || '';

        costLevelDisplay.value =
            selectedOption.dataset.costLevel || '';

        updateRates();

    }


    /*
    |--------------------------------------------------------------------------
    | Cost Level Rates
    |--------------------------------------------------------------------------
    */

    function updateRates() {

        const selectedEmployee =
            employeeSelect.options[
                employeeSelect.selectedIndex
            ];

        if (
            !selectedEmployee ||
            !selectedEmployee.value
        ) {

            mealsPerDay.value = '0';
            allowancePerDay.value = '0';

            updateBalance();

            return;
        }

        const travelType =
            travelTypeSelect.value;

        if (!travelType) {

            mealsPerDay.value = '0';
            allowancePerDay.value = '0';

            updateBalance();

            return;
        }

        let meals = 0;
        let allowance = 0;

        if (travelType === 'domestic') {

            meals =
                parseFloat(
                    selectedEmployee.dataset.mealsDomestic
                ) || 0;

            allowance =
                parseFloat(
                    selectedEmployee.dataset.allowanceDomestic
                ) || 0;

        }

        if (travelType === 'international') {

            meals =
                parseFloat(
                    selectedEmployee.dataset.mealsInternational
                ) || 0;

            allowance =
                parseFloat(
                    selectedEmployee.dataset.allowanceInternational
                ) || 0;

        }

        mealsPerDay.value =
            meals.toFixed(2);

        allowancePerDay.value =
            allowance.toFixed(2);

        updateBalance();

    }


    /*
    |--------------------------------------------------------------------------
    | Total Days
    |--------------------------------------------------------------------------
    */

    function updateTotalDays() {

        if (
            !dateDeparture.value ||
            !dateReturn.value
        ) {

            totalDaysDisplay.value = '0';

            updateBalance();

            return;
        }

        const departure =
            new Date(
                dateDeparture.value + 'T00:00:00'
            );

        const returnDate =
            new Date(
                dateReturn.value + 'T00:00:00'
            );

        if (returnDate < departure) {

            totalDaysDisplay.value = '0';

            updateBalance();

            return;
        }

        const difference =
            returnDate.getTime() -
            departure.getTime();

        const totalDays =
            Math.floor(
                difference /
                (1000 * 60 * 60 * 24)
            ) + 1;

        totalDaysDisplay.value =
            totalDays;

        updateBalance();

    }


    /*
    |--------------------------------------------------------------------------
    | Balance Received
    |--------------------------------------------------------------------------
    */

    function updateBalance() {

        const totalDays =
            parseFloat(
                totalDaysDisplay.value
            ) || 0;

        const meals =
            parseFloat(
                mealsPerDay.value
            ) || 0;

        const allowance =
            parseFloat(
                allowancePerDay.value
            ) || 0;

        const local =
            parseFloat(
                localTransport.value
            ) || 0;

        const contingency =
            parseFloat(
                contingencies.value
            ) || 0;

        const balance =
            (meals * totalDays)
            +
            (allowance * totalDays)
            +
            local
            +
            contingency;

        balanceReceivedDisplay.value =
            formatNumber(balance);

    }


    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    employeeSelect.addEventListener(
        'change',
        updateEmployeeInformation
    );

    travelTypeSelect.addEventListener(
        'change',
        updateRates
    );

    dateDeparture.addEventListener(
        'change',
        updateTotalDays
    );

    dateReturn.addEventListener(
        'change',
        updateTotalDays
    );

    localTransport.addEventListener(
        'input',
        updateBalance
    );

    contingencies.addEventListener(
        'input',
        updateBalance
    );


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    updateEmployeeInformation();

    updateRates();

    updateTotalDays();

});

</script>

@endpush

@endsection