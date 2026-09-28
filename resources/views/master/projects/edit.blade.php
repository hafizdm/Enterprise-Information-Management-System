@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h4 class="mb-1">Edit Project</h4>


<p class="text-muted mb-0">
    Update project master data
</p>


</div>

<div class="card border-0 shadow-sm">


<div class="card-body">

    <form
        action="{{ route('projects.update', $project) }}"
        method="POST"
    >

        @csrf
        @method('PUT')

        {{-- Project Name --}}
        <div class="mb-3">

            <label for="name" class="form-label">
                Project Name
            </label>

            <input
                type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $project->name) }}"
                required
            >

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Location --}}
        <div class="mb-3">

            <label for="location" class="form-label">
                Location
            </label>

            <input
                type="text"
                name="location"
                id="location"
                class="form-control @error('location') is-invalid @enderror"
                value="{{ old('location', $project->location) }}"
                required
            >

            @error('location')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Approval Document --}}
        <div class="mb-3">

            <label for="approval_employee_id" class="form-label">
                Approval Document
            </label>

            <select
                name="approval_employee_id"
                id="approval_employee_id"
                class="form-select @error('approval_employee_id') is-invalid @enderror"
                required
            >
                <option value="">Select Employee</option>

                @foreach($employees as $employee)
                    <option
                        value="{{ $employee->id }}"
                        {{ old('approval_employee_id', $project->approval_employee_id) == $employee->id ? 'selected' : '' }}
                    >
                        {{ $employee->full_name }}
                    </option>
                @endforeach

            </select>

            @error('approval_employee_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>


        {{-- Action --}}
        <div class="d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-check-lg"></i>
                Update
            </button>

            <a
                href="{{ route('projects.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</div>


</div>

@endsection
