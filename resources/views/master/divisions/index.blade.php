@extends('layouts.app')

@section('title', 'Master Division - EIMS')

@section('content')

<div class="container-fluid px-3 px-md-4">

    {{-- =========================================
        PAGE HEADER
    ========================================== --}}
    <div class="d-flex flex-column flex-md-row
                justify-content-between align-items-md-center
                gap-3 mb-4">

        <div>
            <h1 class="page-title mb-1">
                Master Division
            </h1>

            <p class="page-description text-muted mb-0">
                Manage company divisions
            </p>
        </div>

        <div>
            <a href="{{ route('divisions.create') }}"
               class="btn btn-primary px-3">
                <i class="bi bi-plus-lg me-1"></i>
                Add Division
            </a>
        </div>

    </div>


    {{-- =========================================
        SUCCESS MESSAGE
    ========================================== --}}
    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show
                    border-0 shadow-sm mb-4"
             role="alert">

            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>

        </div>

    @endif


    {{-- =========================================
        DIVISION TABLE
    ========================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            @if ($divisions->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        {{-- Table Header --}}
                        <thead class="table-light">

                            <tr>
                                <th class="ps-3 ps-md-4 py-3"
                                    style="width: 75px;">
                                    No.
                                </th>

                                <th class="py-3"
                                    style="min-width: 130px;">
                                    Code
                                </th>

                                <th class="py-3"
                                    style="min-width: 180px;">
                                    Name
                                </th>

                                <th class="py-3"
                                    style="min-width: 220px;">
                                    Description
                                </th>

                                <th class="py-3"
                                    style="min-width: 120px;">
                                    Status
                                </th>

                                <th class="text-end pe-3 pe-md-4 py-3"
                                    style="min-width: 140px;">
                                    Action
                                </th>
                            </tr>

                        </thead>


                        {{-- Table Body --}}
                        <tbody>

                            @foreach ($divisions as $index => $division)

                                <tr>

                                    {{-- Number --}}
                                    <td class="ps-3 ps-md-4 py-3 text-muted">
                                        {{ method_exists($divisions, 'firstItem') && $divisions->firstItem() !== null
                                            ? $divisions->firstItem() + $index
                                            : $index + 1 }}
                                    </td>


                                    {{-- Code --}}
                                    <td class="py-3">

                                        <span class="fw-semibold text-dark">
                                            {{ $division->code }}
                                        </span>

                                    </td>


                                    {{-- Name --}}
                                    <td class="py-3">

                                        <span class="fw-semibold text-dark">
                                            {{ $division->name }}
                                        </span>

                                    </td>


                                    {{-- Description --}}
                                    <td class="py-3">

                                        @if ($division->description)

                                            <span class="text-muted division-description">
                                                {{ $division->description }}
                                            </span>

                                        @else

                                            <span class="text-muted">—</span>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="py-3">

                                        @if ($division->is_active)

                                            <span class="badge rounded-pill
                                                         bg-success-subtle text-success
                                                         px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Active
                                            </span>

                                        @else

                                            <span class="badge rounded-pill
                                                         bg-secondary-subtle text-secondary
                                                         px-3 py-2">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Inactive
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end pe-3 pe-md-4 py-3">

                                        <div class="d-inline-flex align-items-center gap-1">

                                            {{-- View --}}
                                            <a
                                                href="{{ route('divisions.show', $division) }}"
                                                class="btn btn-sm btn-outline-secondary division-action-btn"
                                                title="View division"
                                                aria-label="View division"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>


                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('divisions.edit', $division) }}"
                                                class="btn btn-sm btn-outline-primary division-action-btn"
                                                title="Edit division"
                                                aria-label="Edit division"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>


                                            {{-- Delete --}}
                                            <form
                                                action="{{ route('divisions.destroy', $division) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this division?');"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger division-action-btn"
                                                    title="Delete division"
                                                    aria-label="Delete division"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- Empty State --}}
                <div class="text-center py-5 px-3">

                    <div class="division-empty-icon mb-3">
                        <i class="bi bi-diagram-3"></i>
                    </div>

                    <h5 class="fw-semibold mb-2">
                        No Division Found
                    </h5>

                    <p class="text-muted mb-4">
                        There are currently no division records.
                    </p>

                    <a href="{{ route('divisions.create') }}"
                       class="btn btn-primary px-3">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add First Division
                    </a>

                </div>

            @endif

        </div>


        {{-- =========================================
            PAGINATION
        ========================================== --}}
        @if (method_exists($divisions, 'hasPages') && $divisions->hasPages())

            <div class="card-footer bg-white border-top px-3 px-md-4 py-3">

                <div class="d-flex justify-content-end align-items-center">

                    <nav class="division-pagination"
                         aria-label="Division pagination">

                        {{ $divisions->onEachSide(1)->links('pagination::bootstrap-5') }}

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
    /* Table */
    .table > :not(caption) > * > * {
        vertical-align: middle;
    }

    .table thead th {
        white-space: nowrap;
        font-size: 0.875rem;
        font-weight: 600;
        color: #495057;
        border-bottom-width: 1px;
    }

    .table tbody td {
        border-color: #e9ecef;
    }

    .table tbody tr:last-child td {
        border-bottom: 0;
    }

    /* Description */
    .division-description {
        display: inline-block;
        max-width: 360px;
        overflow-wrap: anywhere;
    }

    /* Action buttons */
    .division-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 7px;
        transition: all 0.2s ease;
    }

    .division-action-btn i {
        font-size: 0.95rem;
    }

    /* Empty state */
    .division-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 76px;
        height: 76px;
        border-radius: 18px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 2.25rem;
    }

    /* Pagination */
    .division-pagination nav > div:first-child {
        display: none;
    }

    .division-pagination nav > div:last-child {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .division-pagination .pagination {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    .division-pagination .page-item {
        margin: 0;
    }

    .division-pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px !important;
        color: #495057;
        background: #fff;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .division-pagination .page-link:hover {
        color: #0d6efd;
        background: #f0f6ff;
        border-color: #b6d4fe;
    }

    .division-pagination .page-item.active .page-link {
        color: #fff;
        background: #0d6efd;
        border-color: #0d6efd;
        font-weight: 600;
    }

    .division-pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
    }

    .division-pagination .page-link:focus {
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }

    /* Responsive */
    @media (max-width: 576px) {

        .division-pagination {
            max-width: 100%;
        }

        .division-pagination .pagination {
            gap: 4px;
        }

        .division-pagination .page-link {
            min-width: 34px;
            height: 34px;
            padding: 0 9px;
            font-size: 13px;
        }

        .division-description {
            max-width: 200px;
        }

    }
</style>

@endsection
