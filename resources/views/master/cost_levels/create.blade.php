@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Create Cost Level</h4>

        <p class="text-muted mb-0">
            Add a new cost level
        </p>
    </div>

    <a
        href="{{ route('cost-levels.index') }}"
        class="btn btn-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>

</div>

<div class="card shadow-sm">

    <div class="card-body">

        <form
            action="{{ route('cost-levels.store') }}"
            method="POST"
        >

            @csrf

            <div class="row g-3">

                {{-- Cost Level Name --}}
                <div class="col-md-6">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Cost Level Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Example: Level 1"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Status --}}
                <div class="col-md-6">

                    <label
                        for="is_active"
                        class="form-label"
                    >
                        Status
                    </label>

                    <select
                        name="is_active"
                        id="is_active"
                        class="form-select @error('is_active') is-invalid @enderror"
                        required
                    >
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>

                    @error('is_active')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Domestic Section --}}
                <div class="col-12 mt-4">

                    <h6 class="fw-semibold mb-3">
                        Domestic Travel
                    </h6>

                </div>

                {{-- Meals Domestic --}}
                <div class="col-md-6">

                    <label
                        for="meals_domestic"
                        class="form-label"
                    >
                        Meals Domestic (IDR)
                    </label>

                    <input
                        type="number"
                        name="meals_domestic"
                        id="meals_domestic"
                        class="form-control @error('meals_domestic') is-invalid @enderror"
                        value="{{ old('meals_domestic', 0) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                    @error('meals_domestic')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Allowance Domestic --}}
                <div class="col-md-6">

                    <label
                        for="allowance_domestic"
                        class="form-label"
                    >
                        Allowance Domestic (IDR)
                    </label>

                    <input
                        type="number"
                        name="allowance_domestic"
                        id="allowance_domestic"
                        class="form-control @error('allowance_domestic') is-invalid @enderror"
                        value="{{ old('allowance_domestic', 0) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                    @error('allowance_domestic')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- International Section --}}
                <div class="col-12 mt-4">

                    <h6 class="fw-semibold mb-3">
                        International Travel
                    </h6>

                </div>

                {{-- Meals International --}}
                <div class="col-md-6">

                    <label
                        for="meals_international"
                        class="form-label"
                    >
                        Meals International (USD)
                    </label>

                    <input
                        type="number"
                        name="meals_international"
                        id="meals_international"
                        class="form-control @error('meals_international') is-invalid @enderror"
                        value="{{ old('meals_international', 0) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                    @error('meals_international')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Allowance International --}}
                <div class="col-md-6">

                    <label
                        for="allowance_international"
                        class="form-label"
                    >
                        Allowance International (USD)
                    </label>

                    <input
                        type="number"
                        name="allowance_international"
                        id="allowance_international"
                        class="form-control @error('allowance_international') is-invalid @enderror"
                        value="{{ old('allowance_international', 0) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                    @error('allowance_international')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="{{ route('cost-levels.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Save Cost Level
                </button>

            </div>

        </form>

    </div>

</div>

@endsection