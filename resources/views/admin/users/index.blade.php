@extends('layouts.app')

@section('content')

<div class="container-fluid">


    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">User Management</h1>
            <p class="text-muted mb-0">
                Manage EIMS user accounts and access.
            </p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add User
        </a>
    </div>


    {{-- Search --}}
    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('users.index') }}">

                <div class="row g-2">

                    <div class="col-md-6">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Search username, employee name, or NIK..."
                        >
                    </div>

                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>
                    </div>

                    @if($search)
                        <div class="col-md-auto">
                            <a
                                href="{{ route('users.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Reset
                            </a>
                        </div>
                    @endif

                </div>

            </form>

        </div>
    </div>


    {{-- User Table --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Employee</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end px-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($users as $user)

                            <tr>

                                {{-- Employee --}}
                                <td class="px-4">

                                    @if($user->employee)

                                        <div class="fw-semibold">
                                            {{ $user->employee->full_name }}
                                        </div>

                                        <small class="text-muted">
                                            NIK: {{ $user->employee->nik }}
                                        </small>

                                    @else

                                        <span class="text-muted">
                                            No employee linked
                                        </span>

                                    @endif

                                </td>


                                {{-- Username --}}
                                <td>
                                    <span class="fw-medium">
                                        {{ $user->username }}
                                    </span>
                                </td>


                                {{-- Role --}}
                                <td>

                                    @forelse($user->roles as $role)

                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $role->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted">
                                            No role
                                        </span>

                                    @endforelse

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($user->is_active)

                                        <span class="badge bg-success-subtle text-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-danger-subtle text-danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Action --}}
                                <td class="text-end px-4">

                                    <a
                                        href="{{ route('users.show', $user) }}"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="View"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center py-5">

                                    <div class="text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-2"></i>

                                        @if($search)
                                            No users found for
                                            <strong>"{{ $search }}"</strong>.
                                        @else
                                            No user accounts found.
                                        @endif
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if($users->hasPages())

            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
