@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">User Details</h1>
            <p class="text-muted mb-0">
                View login account information.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('users.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

            <a
                href="{{ route('users.edit', $user) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

        </div>

    </div>


    {{-- Account Information --}}
    <div class="card mb-4">

        <div class="card-header">
            <strong>Account Information</strong>
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- Username --}}
                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Username
                    </label>

                    <div class="fw-semibold">
                        {{ $user->username }}
                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Status
                    </label>

                    <div>

                        @if($user->is_active)

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Inactive
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Password Status --}}
                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Password Status
                    </label>

                    <div>

                        @if($user->must_change_password)

                            <span class="badge bg-warning text-dark">
                                Must Change Password
                            </span>

                        @else

                            <span class="badge bg-success">
                                Password Set
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Employee --}}
                <div class="col-md-6">

                    <label class="form-label text-muted">
                        Employee
                    </label>

                    <div class="fw-semibold">

                        @if($user->employee)

                            {{ $user->employee->full_name }}

                        @else

                            <span class="text-muted">
                                Not linked to employee
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Employee Information --}}
    @if($user->employee)

        <div class="card mb-4">

            <div class="card-header">
                <strong>Employee Information</strong>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4">

                        <label class="form-label text-muted">
                            NIK
                        </label>

                        <div>
                            {{ $user->employee->nik }}
                        </div>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label text-muted">
                            Full Name
                        </label>

                        <div>
                            {{ $user->employee->full_name }}
                        </div>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label text-muted">
                            Email
                        </label>

                        <div>
                            {{ $user->employee->email ?: '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- Roles --}}
    <div class="card">

        <div class="card-header">
            <strong>Roles</strong>
        </div>

        <div class="card-body">

            @forelse($user->roles as $role)

                <span class="badge bg-primary me-1 mb-1">
                    {{ $role->name }}
                </span>

            @empty

                <span class="text-muted">
                    No roles assigned.
                </span>

            @endforelse

        </div>

    </div>

</div>

@endsection