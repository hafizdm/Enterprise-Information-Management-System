@extends('layouts.app')

@section('title', 'Add Division - EIMS')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="page-title">
            Add Division
        </h1>

        <p class="page-description mb-0">
            Create a new company division
        </p>

    </div>

</div>


<div class="card">

    <div class="card-body p-4">

        <form
            action="{{ route('divisions.store') }}"
            method="POST"
        >

            @csrf


            <div class="row">

                <div class="col-md-6 mb-3">

                    <label
                        for="code"
                        class="form-label"
                    >
                        Division Code
                    </label>

                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code') }}"
                        placeholder="Example: IT"
                        maxlength="20"
                        required
                    >

                    @error('code')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-md-6 mb-3">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Division Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Example: Information Technology"
                        maxlength="100"
                        required
                    >

                    @error('name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-12 mb-3">

                    <label
                        for="description"
                        class="form-label"
                    >
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="4"
                        placeholder="Enter division description..."
                    >{{ old('description') }}</textarea>

                    @error('description')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-12 mb-3">

                    <div class="form-check">

                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="form-check-input"
                            id="is_active"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >

                        <label
                            class="form-check-label"
                            for="is_active"
                        >
                            Active
                        </label>

                    </div>

                </div>

            </div>


            <div class="d-flex gap-2 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-check-lg me-1"></i>

                    Save Division

                </button>


                <a
                    href="{{ route('divisions.index') }}"
                    class="btn btn-secondary"
                >

                    Cancel

                </a>

            </div>

        </form>

    </div>

</div>

@endsection