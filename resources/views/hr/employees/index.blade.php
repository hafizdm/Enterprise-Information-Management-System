@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">Employees</h4>

            <p class="text-muted mb-0">
                Manage employee information
            </p>
        </div>

        @can('employee.create')
            <a href="{{ route('employees.create') }}"
               class="btn btn-dark">
                <i class="bi bi-plus-lg me-1"></i>
                Add Employee
            </a>
        @endcan

    </div>


    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('employees.index') }}">

                <div class="row g-2">

                    <div class="col-md-10">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by NIK, name, or email..."
                            value="{{ $search }}"
                        >

                    </div>

                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-outline-dark w-100"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Employee Table --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">NIK</th>

                            <th>Name</th>

                            <th>Division</th>

                            <th>Position</th>

                            <th>Project</th>

                            <th>Status</th>

                            <th>Account</th>

                            <th class="text-end px-4">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($employees as $employee)

                            <tr>

                                <td class="px-4">
                                    {{ $employee->nik }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $employee->full_name }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $employee->email }}
                                    </small>
                                </td>

                                <td>
                                    {{ $employee->division?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $employee->position?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $employee->project?->name ?? '-' }}
                                </td>

                                <td>

                                    @if ($employee->employee_status === 'Permanent')

                                        <span class="badge bg-success">
                                            Permanent
                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">
                                            Contract
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if ($employee->user)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            No Account
                                        </span>

                                    @endif

                                </td>

                                <td class="text-end px-4">

                                    <a
                                        href="{{ route('employees.show', $employee) }}"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        View
                                    </a>

                                    @can('update', $employee)

                                        <a
                                            href="{{ route('employees.edit', $employee) }}"
                                            class="btn btn-sm btn-outline-dark"
                                        >
                                            Edit
                                        </a>

                                    @endcan

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-5 text-muted"
                                >
                                    No employees found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if ($employees->hasPages())

            <div class="card-footer bg-white border-0 py-3">

                {{ $employees->links() }}

            </div>

        @endif

    </div>

</div>

@endsection