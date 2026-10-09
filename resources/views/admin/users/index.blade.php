@extends('layouts.app')

@section('content')

<div class="container-fluid px-3 px-md-4">

    {{-- =========================================
        PAGE HEADER
    ========================================== --}}
    <div class="d-flex flex-column flex-md-row
                justify-content-between align-items-md-center
                gap-3 mb-4">

        <div>
            <h1 class="h3 fw-bold mb-1">User Management</h1>
            <p class="text-muted mb-0">
                Manage EIMS user accounts and access.
            </p>
        </div>

        <div>
            <a href="{{ route('users.create') }}"
               class="btn btn-primary px-3">
                <i class="bi bi-plus-lg me-1"></i>
                Add User
            </a>
        </div>

    </div>


    {{-- =========================================
        SEARCH
    ========================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body p-3 p-md-4">

            <form method="GET"
                  action="{{ route('users.index') }}">

                <div class="row g-2 align-items-center">

                    <div class="col-12 col-md-6 col-lg-5">

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-muted"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                class="form-control"
                                placeholder="Search username, employee name, or NIK..."
                                aria-label="Search users"
                            >

                        </div>

                    </div>

                    <div class="col-auto">

                        <button type="submit"
                                class="btn btn-primary px-3">
                            Search
                        </button>

                    </div>

                    @if($search)

                        <div class="col-auto">

                            <a href="{{ route('users.index') }}"
                               class="btn btn-outline-secondary px-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset
                            </a>

                        </div>

                    @endif

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================
        USER TABLE
    ========================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="px-3 px-md-4 py-3">
                                Employee
                            </th>

                            <th class="py-3">
                                Username
                            </th>

                            <th class="py-3">
                                Role
                            </th>

                            <th class="py-3">
                                Status
                            </th>

                            <th class="text-end px-3 px-md-4 py-3">
                                Action
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($users as $user)

                            <tr>

                                {{-- Employee --}}
                                <td class="px-3 px-md-4 py-3">

                                    @if($user->employee)

                                        <div class="fw-semibold text-dark">
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
                                <td class="py-3">

                                    <span class="fw-medium">
                                        {{ $user->username }}
                                    </span>

                                </td>


                                {{-- Role --}}
                                <td class="py-3">

                                    @forelse($user->roles as $role)

                                        <span class="badge rounded-pill
                                                     bg-primary-subtle text-primary
                                                     fw-semibold me-1 mb-1">
                                            {{ $role->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted small">
                                            No role
                                        </span>

                                    @endforelse

                                </td>


                                {{-- Status --}}
                                <td class="py-3">

                                    @if($user->is_active)

                                        <span class="badge rounded-pill
                                                     bg-success-subtle text-success
                                                     px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Active
                                        </span>

                                    @else

                                        <span class="badge rounded-pill
                                                     bg-danger-subtle text-danger
                                                     px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                {{-- Action --}}
                                <td class="text-end px-3 px-md-4 py-3">

                                    <div class="d-inline-flex gap-1">

                                        <a
                                            href="{{ route('users.show', $user) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="View user"
                                            aria-label="View user"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a
                                            href="{{ route('users.edit', $user) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit user"
                                            aria-label="Edit user"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center py-5">

                                    <div class="mb-2">
                                        <i class="bi bi-people
                                                  text-secondary"
                                           style="font-size: 2.5rem;"></i>
                                    </div>

                                    <h6 class="fw-semibold mb-1">
                                        No users found
                                    </h6>

                                    <p class="text-muted small mb-0">

                                        @if($search)
                                            No users match
                                            "{{ $search }}".
                                        @else
                                            No user accounts have been created yet.
                                        @endif

                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================================
            PAGINATION FOOTER
        ========================================== --}}
        @if($users->hasPages())

            <div class="card-footer bg-white border-top px-3 px-md-4 py-3">

                <div class="d-flex flex-column flex-lg-row
                            justify-content-between align-items-center gap-3">

                    {{-- Record Information --}}
                    <div class="text-muted small text-center text-lg-start">

                        @if($users->total() > 0)

                            Showing
                            <span class="fw-semibold text-dark">
                                {{ $users->firstItem() }}
                            </span>

                            to

                            <span class="fw-semibold text-dark">
                                {{ $users->lastItem() }}
                            </span>

                            of

                            <span class="fw-semibold text-dark">
                                {{ $users->total() }}
                            </span>

                            users

                        @endif

                    </div>


                    {{-- Previous / Page Numbers / Next --}}
                    <nav class="users-pagination"
                         aria-label="User pagination">

                        {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}

                    </nav>

                </div>

            </div>

        @endif

    </div>

</div>


{{-- =========================================
    PAGE STYLES
========================================== --}}
<style>
    /* Pagination container */
    .users-pagination nav > div:first-child {
        display: none;
    }

    .users-pagination nav > div:last-child {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .users-pagination .pagination {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    /* Remove Bootstrap's joined button appearance */
    .users-pagination .page-item {
        margin: 0;
    }

    .users-pagination .page-link {
        display: flex;
        justify-content: center;
        align-items: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px !important;
        background: #fff;
        color: #495057;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    /* Hover */
    .users-pagination .page-link:hover {
        color: #0d6efd;
        background: #f0f6ff;
        border-color: #b6d4fe;
    }

    /* Active page */
    .users-pagination .page-item.active .page-link {
        color: #fff;
        background: #0d6efd;
        border-color: #0d6efd;
        font-weight: 600;
    }

    /* Disabled Previous / Next */
    .users-pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
    }

    /* Focus */
    .users-pagination .page-link:focus {
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }

    /* Responsive */
    @media (max-width: 576px) {

        .users-pagination {
            max-width: 100%;
        }

        .users-pagination .pagination {
            gap: 4px;
        }

        .users-pagination .page-link {
            min-width: 34px;
            height: 34px;
            padding: 0 9px;
            font-size: 13px;
        }

    }
</style>

@endsection
