@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container-fluid">


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="h3 mb-1">Leave Request</h1>
        <p class="text-muted mb-0">
            Submit your leave request.
        </p>
    </div>

</div>

<div class="card">

    <div class="card-body">

        <form
            action="{{ route('leave-requests.store') }}"
            method="POST"
        >

            @csrf

            {{-- Leave Type --}}

            <div class="mb-3">

                <label for="leave_type" class="form-label">
                    Leave Type
                </label>

                <select
                    name="leave_type"
                    id="leave_type"
                    class="form-select @error('leave_type') is-invalid @enderror"
                >

                    <option value="">
                        -- Select Leave Type --
                    </option>

                    <option value="annual" {{ old('leave_type') == 'annual' ? 'selected' : '' }}>
                        Annual Leave
                    </option>

                    <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>
                        Sick Leave
                    </option>

                    <option value="hajj" {{ old('leave_type') == 'hajj' ? 'selected' : '' }}>
                        Hajj Leave
                    </option>

                    <option value="site" {{ old('leave_type') == 'site' ? 'selected' : '' }}>
                        Site Leave
                    </option>

                    <option value="demobilization" {{ old('leave_type') == 'demobilization' ? 'selected' : '' }}>
                        Demobilization Leave
                    </option>

                </select>

                @error('leave_type')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- First Date --}}

            <div class="mb-3">

                <label for="first_date" class="form-label">
                    First Date
                </label>

                <input
                    type="date"
                    name="first_date"
                    id="first_date"
                    class="form-control @error('first_date') is-invalid @enderror"
                    value="{{ old('first_date') }}"
                >

                @error('first_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Last Date --}}

            <div class="mb-3">

                <label for="last_date" class="form-label">
                    Last Date
                </label>

                <input
                    type="date"
                    name="last_date"
                    id="last_date"
                    class="form-control @error('last_date') is-invalid @enderror"
                    value="{{ old('last_date') }}"
                >

                @error('last_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Total Days --}}

            <div class="mb-3">

                <label for="total_days" class="form-label">
                    Total Leave Days
                </label>

                <input
                    type="number"
                    id="total_days"
                    class="form-control"
                    readonly
                >

                <div class="form-text">
                    Saturday and Sunday are not counted.
                </div>

            </div>


            {{-- Reason --}}

            <div class="mb-3">

                <label for="reason" class="form-label">
                    Reason / Description
                </label>

                <textarea
                    name="reason"
                    id="reason"
                    rows="4"
                    class="form-control @error('reason') is-invalid @enderror"
                    placeholder="Enter your reason or description"
                >{{ old('reason') }}</textarea>

                @error('reason')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Actions --}}

            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Submit Request
                </button>

                <a
                    href="{{ url()->previous() }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>


</div>

<script>

    function calculateLeaveDays() {

        const firstDate = document.getElementById('first_date').value;
        const lastDate = document.getElementById('last_date').value;
        const totalDays = document.getElementById('total_days');

        if (!firstDate || !lastDate) {
            totalDays.value = '';
            return;
        }

        const start = new Date(firstDate);
        const end = new Date(lastDate);

        if (start > end) {
            totalDays.value = '';
            return;
        }

        let total = 0;

        const current = new Date(start);

        while (current <= end) {

            const day = current.getDay();

            // Monday = 1
            // Tuesday = 2
            // Wednesday = 3
            // Thursday = 4
            // Friday = 5
            // Saturday = 6
            // Sunday = 0

            if (day !== 0 && day !== 6) {
                total++;
            }

            current.setDate(current.getDate() + 1);
        }

        totalDays.value = total;
    }


    document.getElementById('first_date').addEventListener(
        'change',
        calculateLeaveDays
    );

    document.getElementById('last_date').addEventListener(
        'change',
        calculateLeaveDays
    );

</script>

@endsection
