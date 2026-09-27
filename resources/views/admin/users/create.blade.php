@extends('layouts.app')

@section('content')

<div class="container-fluid">


{{-- Header --}}
<div class="mb-4">
    <h1 class="h3 mb-1">Add User</h1>
    <p class="text-muted mb-0">
        Create a login account for an existing employee.
    </p>
</div>


<div class="card">

    <div class="card-body">

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="row g-3">

                {{-- Employee --}}
                <div class="col-md-6">

                    <label for="employee_id" class="form-label">
                        Employee <span class="text-danger">*</span>
                    </label>

                    <select
                        name="employee_id"
                        id="employee_id"
                        class="form-select @error('employee_id') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Select Employee --</option>

                        @foreach($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                @selected(old('employee_id') == $employee->id)
                            >
                                {{ $employee->full_name }}
                                — {{ $employee->nik }}
                            </option>

                        @endforeach

                    </select>

                    @error('employee_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Username --}}
                <div class="col-md-6">

                    <label for="username" class="form-label">
                        Username <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
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
                                    @checked(in_array($role->name, old('roles', [])))
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



                {{-- Password --}}
                <div class="col-md-6">

                    <label for="password" class="form-label">
                        Password <span class="text-danger">*</span>
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                    >

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Status --}}
                <div class="col-md-6">

                    <label for="is_active" class="form-label">
                        Status
                    </label>

                    <select
                        name="is_active"
                        id="is_active"
                        class="form-select @error('is_active') is-invalid @enderror"
                    >
                        <option value="1" @selected(old('is_active', '1') == '1')>
                            Active
                        </option>

                        <option value="0" @selected(old('is_active') === '0')>
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


            {{-- Buttons --}}
            <div class="d-flex justify-content-end gap-2 mt-4">

                <a
                    href="{{ route('users.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    Create User
                </button>

            </div>

        </form>

    </div>

</div>
```

</div>

@endsection
