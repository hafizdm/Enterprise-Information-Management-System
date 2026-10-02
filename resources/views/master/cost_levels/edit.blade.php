@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Edit Cost Level</h4>

        <p class="text-muted mb-0">
            Update cost level information and travel rates
        </p>
    </div>

    <a
        href="{{ route('cost-levels.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>

</div>

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            action="{{ route('cost-levels.update', $costLevel) }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            {{-- Cost Level Information --}}
            <div class="mb-4">

                <h6 class="fw-bold mb-3">
                    Cost Level Information
                </h6>

                <div class="row g-3">

                    {{-- Cost Level Name --}}
                    <div class="col-md-8">

                        <label class="form-label">
                            Cost Level Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $costLevel->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter cost level name"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Status
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="is_active"
                            class="form-select @error('is_active') is-invalid @enderror"
                            required
                        >
                            <option
                                value="1"
                                @selected(old('is_active', $costLevel->is_active) == 1)
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(old('is_active', $costLevel->is_active) == 0)
                            >
                                Inactive
                            </option>
                        </select>

                        @error('is_active')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

            <hr class="my-4">

            {{-- Domestic Travel --}}
            <div class="mb-4">

                <h6 class="fw-bold mb-3">
                    Domestic Travel
                </h6>

                <div class="row g-3">

                    {{-- Meals Domestic --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Meals Domestic (IDR)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="meals_domestic"
                                value="{{ old('meals_domestic', $costLevel->meals_domestic) }}"
                                class="form-control @error('meals_domestic') is-invalid @enderror"
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

                    </div>

                    {{-- Allowance Domestic --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Allowance Domestic (IDR)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="allowance_domestic"
                                value="{{ old('allowance_domestic', $costLevel->allowance_domestic) }}"
                                class="form-control @error('allowance_domestic') is-invalid @enderror"
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

                    </div>

                </div>

            </div>

            <hr class="my-4">

            {{-- International Travel --}}
            <div class="mb-4">

                <h6 class="fw-bold mb-3">
                    International Travel
                </h6>

                <div class="row g-3">

                    {{-- Meals International --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Meals International (USD)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                $
                            </span>

                            <input
                                type="number"
                                name="meals_international"
                                value="{{ old('meals_international', $costLevel->meals_international) }}"
                                class="form-control @error('meals_international') is-invalid @enderror"
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

                    </div>

                    {{-- Allowance International --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Allowance International (USD)
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                $
                            </span>

                            <input
                                type="number"
                                name="allowance_international"
                                value="{{ old('allowance_international', $costLevel->allowance_international) }}"
                                class="form-control @error('allowance_international') is-invalid @enderror"
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

                </div>

            </div>

            <hr class="my-4">

            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2">

                <a
                    href="{{ route('cost-levels.index') }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Update Cost Level
                </button>

            </div>

        </form>

    </div>

</div>

@endsection