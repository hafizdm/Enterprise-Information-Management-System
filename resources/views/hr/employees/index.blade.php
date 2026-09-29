@extends('layouts.app')

@section('content')

<div class="container-fluid">


{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

    <div>

        <div class="d-flex align-items-center gap-2 mb-1">

            <h4 class="fw-bold mb-0">
                Employees
            </h4>

            <span class="badge bg-light text-dark border">
                {{ $employees->total() }} Employees
            </span>

        </div>

        <p class="text-muted mb-0">
            Manage employee information and account access.
        </p>

    </div>


    @can('employee.create')

        <a
            href="{{ route('employees.create') }}"
            class="btn btn-dark"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Add Employee
        </a>

    @endcan

</div>


{{-- Search & Filter --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('employees.index') }}"
        >

            <div class="row g-3 align-items-end">

                <div class="col-lg-9">

                    <label
                        for="employeeSearch"
                        class="form-label small fw-semibold"
                    >
                        Search Employee
                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        <input
                            type="text"
                            id="employeeSearch"
                            name="search"
                            class="form-control"
                            placeholder="Search by NIK, name, or email..."
                            value="{{ $search }}"
                        >

                    </div>

                </div>


                <div class="col-lg-3">

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-dark flex-grow-1"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        @if($search)

                            <a
                                href="{{ route('employees.index') }}"
                                class="btn btn-outline-secondary"
                                title="Clear search"
                            >
                                <i class="bi bi-x-lg"></i>
                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- Employee Table --}}
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0 py-3">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

            <div>

                <h6 class="fw-semibold mb-1">
                    Employee Directory
                </h6>

                <small class="text-muted">
                    Employee master data
                </small>

            </div>


            @if($search)

                <div class="small text-muted">

                    Search result for:
                    <span class="fw-semibold text-dark">
                        "{{ $search }}"
                    </span>

                </div>

            @endif

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="ps-4">
                            Employee
                        </th>

                        <th>
                            Division
                        </th>

                        <th>
                            Position
                        </th>

                        <th>
                            Project
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Account
                        </th>

                        <th class="text-end pe-4">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($employees as $employee)

                        <tr>

                            {{-- Employee --}}
                            <td class="ps-4">

                                <div class="d-flex align-items-center">

                                    <div>

                                        <div class="fw-semibold">
                                            {{ $employee->full_name }}
                                        </div>

                                        <div class="small text-muted">

                                            NIK:
                                            {{ $employee->nik }}

                                            @if($employee->email)

                                                <span class="mx-1">
                                                    •
                                                </span>

                                                {{ $employee->email }}

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- Division --}}
                            <td>

                                <span class="text-dark">
                                    {{ $employee->division?->name ?? '-' }}
                                </span>

                            </td>


                            {{-- Position --}}
                            <td>

                                <span class="text-dark">
                                    {{ $employee->position?->name ?? '-' }}
                                </span>

                            </td>


                            {{-- Project --}}
                            <td>

                                @if($employee->project)

                                    <span class="badge bg-light text-dark border">
                                        {{ $employee->project->name }}
                                    </span>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- Employee Status --}}
                            <td>

                                @if ($employee->employee_status === 'Permanent')

                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Permanent
                                    </span>

                                @else

                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                        <i class="bi bi-clock me-1"></i>
                                        Contract
                                    </span>

                                @endif

                            </td>


                            {{-- Account --}}
                            <td>

                                @if ($employee->user)

                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-person-check me-1"></i>
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-person-x me-1"></i>
                                        No Account
                                    </span>

                                @endif

                            </td>


                            {{-- Action --}}
                            <td class="text-end pe-4">

                                <div class="d-flex justify-content-end gap-1">

                                    <a
                                        href="{{ route('employees.show', $employee) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View employee"
                                    >
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-lg-inline ms-1">
                                            View
                                        </span>
                                    </a>


                                    @can('update', $employee)

                                        <a
                                            href="{{ route('employees.edit', $employee) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Edit employee"
                                        >
                                            <i class="bi bi-pencil"></i>
                                            <span class="d-none d-lg-inline ms-1">
                                                Edit
                                            </span>
                                        </a>

                                    @endcan

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5"
                            >

                                <div class="mb-3">

                                    <i
                                        class="bi bi-people text-muted"
                                        style="font-size: 2.5rem;"
                                    ></i>

                                </div>

                                <h6 class="fw-semibold mb-1">
                                    No employees found
                                </h6>

                                <p class="text-muted small mb-0">

                                    @if($search)

                                        No employees match your search criteria.

                                    @else

                                        There are no employee records available yet.

                                    @endif

                                </p>

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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

                <small class="text-muted">

                    Showing
                    <strong>
                        {{ $employees->firstItem() }}
                    </strong>

                    to

                    <strong>
                        {{ $employees->lastItem() }}
                    </strong>

                    of

                    <strong>
                        {{ $employees->total() }}
                    </strong>

                    employees

                </small>


                <div>
                    {{ $employees->links() }}
                </div>

            </div>

        </div>

    @endif

</div>


</div>

@endsection
