@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Edit User</h1>
            <p class="text-muted mb-0">
                Update login account information.
            </p>
        </div>

        <a
            href="{{ route('users.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>


    <div class="card">

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('users.update', $user) }}"
            >

                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- Employee --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Employee
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $user->employee?->full_name ?? 'Not linked to employee' }}"
                            disabled
                        >

                        <small class="text-muted">
                            Employee cannot be changed from User Management.
                        </small>

                    </div>


                    {{-- Username --}}
                    <div class="col-md-6">

                        <label
                            for="username"
                            class="form-label"
                        >
                            Username <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="{{ old('username', $user->username) }}"
                            class="form-control @error('username') is-invalid @enderror"
                            required
                        >

                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Roles --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Roles <span class="text-danger">*</span>
                        </label>

                        <div class="border rounded p-3">

                            @foreach($roles as $role)

                                <div class="form-check mb-2">

                                    <input
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->name }}"
                                        id="role_{{ $role->id }}"
                                        class="form-check-input @error('roles') is-invalid @enderror"
                                        @checked(
                                            in_array(
                                                $role->name,
                                                old(
                                                    'roles',
                                                    $user->roles->pluck('name')->toArray()
                                                )
                                            )
                                        )
                                    >

                                    <label
                                        for="role_{{ $role->id }}"
                                        class="form-check-label"
                                    >
                                        {{ $role->name }}
                                    </label>

                                </div>

                            @endforeach

                        </div>

                        @error('roles')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('roles.*')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            You can assign multiple roles to one user.
                        </small>

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
                        >

                            <option
                                value="1"
                                @selected(
                                    old('is_active', $user->is_active ? '1' : '0') === '1'
                                )
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(
                                    old('is_active', $user->is_active ? '1' : '0') === '0'
                                )
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


                    {{-- Password --}}
                    <div class="col-md-6">

                        <label
                            for="password"
                            class="form-label"
                        >
                            New Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                        >

                        <small class="text-muted">
                            Leave blank if you do not want to change the password.
                        </small>

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Password Confirmation --}}
                    <div class="col-md-6">

                        <label
                            for="password_confirmation"
                            class="form-label"
                        >
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control"
                            autocomplete="new-password"
                        >

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="{{ route('users.show', $user) }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection