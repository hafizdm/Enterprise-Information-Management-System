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

        {{-- Horizontal scroll on smaller screens --}}
        <div class="table-responsive">

            <table
                class="table table-hover align-middle mb-0"
                style="min-width: 1250px;"
            >

                <thead class="table-light">

                    <tr>

                        <th
                            class="ps-4 text-nowrap"
                            style="min-width: 220px;"
                        >
                            Employee Name
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 150px;"
                        >
                            NIK
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 240px;"
                        >
                            Email
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 170px;"
                        >
                            Division
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 180px;"
                        >
                            Position
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 220px;"
                        >
                            Project
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 130px;"
                        >
                            Status
                        </th>

                        <th
                            class="text-nowrap"
                            style="min-width: 130px;"
                        >
                            Account
                        </th>

                        <th
                            class="text-end pe-4 text-nowrap"
                            style="min-width: 160px;"
                        >
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($employees as $employee)

                        <tr>

                            {{-- Employee Name --}}
                            <td class="ps-4">

                                <span class="fw-semibold text-dark text-nowrap">
                                    {{ $employee->full_name }}
                                </span>

                            </td>


                            {{-- NIK --}}
                            <td>

                                <span class="text-dark text-nowrap">
                                    {{ $employee->nik }}
                                </span>

                            </td>


                            {{-- Email --}}
                            <td>

                                @if($employee->email)

                                    <span class="text-dark text-nowrap">
                                        {{ $employee->email }}
                                    </span>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- Division --}}
                            <td>

                                <span class="text-dark text-nowrap">
                                    {{ $employee->division?->name ?? '-' }}
                                </span>

                            </td>


                            {{-- Position --}}
                            <td>

                                <span class="text-dark text-nowrap">
                                    {{ $employee->position?->name ?? '-' }}
                                </span>

                            </td>


                            {{-- Project --}}
                            <td>

                                @if($employee->project)

                                    <span class="badge bg-light text-dark border text-nowrap">
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

                                    <span class="badge bg-success-subtle text-success border border-success-subtle text-nowrap">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Permanent
                                    </span>

                                @else

                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle text-nowrap">
                                        <i class="bi bi-clock me-1"></i>
                                        Contract
                                    </span>

                                @endif

                            </td>


                            {{-- Account --}}
                            <td>

                                @if ($employee->user)

                                    <span class="badge bg-success-subtle text-success border border-success-subtle text-nowrap">
                                        <i class="bi bi-person-check me-1"></i>
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-light text-secondary border text-nowrap">
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
                                        class="btn btn-sm btn-outline-primary text-nowrap"
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
                                            class="btn btn-sm btn-outline-secondary text-nowrap"
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
                                colspan="9"
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


   {{-- =========================================================
    PAGINATION
========================================================= --}}
@if ($employees->hasPages())

    <div class="card-footer bg-white border-top py-3 px-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

            {{-- =================================================
                PAGINATION INFORMATION
            ================================================== --}}
            <div class="text-muted small text-center text-md-start">

                Showing

                <strong class="text-dark">
                    {{ $employees->firstItem() }}
                </strong>

                to

                <strong class="text-dark">
                    {{ $employees->lastItem() }}
                </strong>

                of

                <strong class="text-dark">
                    {{ $employees->total() }}
                </strong>

                employees

            </div>


            {{-- =================================================
                PAGINATION NAVIGATION
            ================================================== --}}
            <nav
                aria-label="Employee pagination"
                class="employee-pagination"
            >

                <ul class="pagination mb-0">

                    {{-- Previous --}}
                    @if ($employees->onFirstPage())

                        <li class="page-item disabled">

                            <span class="page-link">

                                <i class="bi bi-chevron-left"></i>

                            </span>

                        </li>

                    @else

                        <li class="page-item">

                            <a
                                class="page-link"
                                href="{{ $employees->previousPageUrl() }}"
                                aria-label="Previous"
                            >

                                <i class="bi bi-chevron-left"></i>

                            </a>

                        </li>

                    @endif


                    {{-- Page Numbers --}}
                    @foreach ($employees->getUrlRange(1, $employees->lastPage()) as $page => $url)

                        @if ($page == $employees->currentPage())

                            <li class="page-item active">

                                <span class="page-link">
                                    {{ $page }}
                                </span>

                            </li>

                        @else

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="{{ $url }}"
                                >
                                    {{ $page }}
                                </a>

                            </li>

                        @endif

                    @endforeach


                    {{-- Next --}}
                    @if ($employees->hasMorePages())

                        <li class="page-item">

                            <a
                                class="page-link"
                                href="{{ $employees->nextPageUrl() }}"
                                aria-label="Next"
                            >

                                <i class="bi bi-chevron-right"></i>

                            </a>

                        </li>

                    @else

                        <li class="page-item disabled">

                            <span class="page-link">

                                <i class="bi bi-chevron-right"></i>

                            </span>

                        </li>

                    @endif

                </ul>

            </nav>

        </div>

    </div>

@endif


</div>


</div>

@endsection

@push('styles')

<style>

    /*
    |--------------------------------------------------------------------------
    | Employee Pagination
    |--------------------------------------------------------------------------
    */

    .employee-pagination .pagination {

        display: flex;

        align-items: center;

        gap: 4px;

        margin: 0;

    }


    /*
    |--------------------------------------------------------------------------
    | Page Link
    |--------------------------------------------------------------------------
    */

    .employee-pagination .page-link {

        width: 38px;

        height: 38px;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 0;

        border: 1px solid #dee2e6;

        border-radius: 8px !important;

        background: #ffffff;

        color: #495057;

        font-size: 14px;

        font-weight: 500;

        line-height: 1;

        text-decoration: none;

        transition: all 0.15s ease;

    }


    /*
    |--------------------------------------------------------------------------
    | Hover
    |--------------------------------------------------------------------------
    */

    .employee-pagination .page-link:hover {

        background-color: #f8f9fa;

        border-color: #ced4da;

        color: #212529;

    }


    /*
    |--------------------------------------------------------------------------
    | Active
    |--------------------------------------------------------------------------
    */

    .employee-pagination .page-item.active .page-link {

        background-color: #0d6efd;

        border-color: #0d6efd;

        color: #ffffff;

    }


    /*
    |--------------------------------------------------------------------------
    | Disabled
    |--------------------------------------------------------------------------
    */

    .employee-pagination .page-item.disabled .page-link {

        background-color: #ffffff;

        border-color: #e9ecef;

        color: #adb5bd;

        pointer-events: none;

    }


    /*
    |--------------------------------------------------------------------------
    | Focus
    |--------------------------------------------------------------------------
    */

    .employee-pagination .page-link:focus {

        box-shadow: none;

    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767.98px) {

        .card-footer {

            padding-left: 16px !important;

            padding-right: 16px !important;

        }


        .employee-pagination {

            width: 100%;

            display: flex;

            justify-content: center;

        }


        .employee-pagination .pagination {

            justify-content: center;

        }


        .employee-pagination .page-link {

            width: 36px;

            height: 36px;

            font-size: 13px;

        }

    }

</style>

@endpush