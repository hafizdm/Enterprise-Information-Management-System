@extends('layouts.app')

@section('title', 'Master Position - EIMS')

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
                Master Position
            </h1>

            <p class="page-description text-muted mb-0">
                Manage employee positions
            </p>
        </div>

        <div>
            <a href="{{ route('positions.create') }}"
               class="btn btn-primary px-3">
                <i class="bi bi-plus-lg me-1"></i>
                Add Position
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
        POSITION TABLE
    ========================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

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
                                style="min-width: 180px;">
                                Position
                            </th>

                            <th class="py-3"
                                style="min-width: 160px;">
                                Division
                            </th>

                            <th class="py-3"
                                style="min-width: 220px;">
                                Description
                            </th>

                            <th class="text-end pe-3 pe-md-4 py-3"
                                style="min-width: 120px;">
                                Action
                            </th>
                        </tr>

                    </thead>


                    {{-- Table Body --}}
                    <tbody>

                        @forelse ($positions as $index => $position)

                            <tr>

                                {{-- Number --}}
                                <td class="ps-3 ps-md-4 py-3 text-muted">

                                    {{ $positions->firstItem() !== null
                                        ? $positions->firstItem() + $index
                                        : $index + 1 }}

                                </td>


                                {{-- Position --}}
                                <td class="py-3">

                                    <span class="fw-semibold text-dark">
                                        {{ $position->name }}
                                    </span>

                                </td>


                                {{-- Division --}}
                                <td class="py-3">

                                    @if ($position->division)

                                        <span class="badge rounded-pill
                                                     bg-primary-subtle text-primary
                                                     px-3 py-2">
                                            {{ $position->division->name }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Description --}}
                                <td class="py-3">

                                    @if ($position->description)

                                        <span class="text-muted position-description">
                                            {{ $position->description }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="text-end pe-3 pe-md-4 py-3">

                                    <div class="d-inline-flex align-items-center gap-1">

                        
                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('positions.edit', $position) }}"
                                            class="btn btn-sm btn-outline-primary position-action-btn"
                                            title="Edit position"
                                            aria-label="Edit position"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('positions.destroy', $position) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this position?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger position-action-btn"
                                                title="Delete position"
                                                aria-label="Delete position"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            {{-- Empty State --}}
                            <tr>

                                <td colspan="5"
                                    class="text-center py-5 px-3">

                                    <div class="position-empty-icon mb-3">
                                        <i class="bi bi-person-badge"></i>
                                    </div>

                                    <h5 class="fw-semibold mb-2">
                                        No Position Found
                                    </h5>

                                    <p class="text-muted mb-4">
                                        There are currently no position records.
                                    </p>

                                    <a href="{{ route('positions.create') }}"
                                       class="btn btn-primary px-3">

                                        <i class="bi bi-plus-lg me-1"></i>
                                        Add First Position

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =========================================
            PAGINATION
        ========================================== --}}
        @if ($positions->hasPages())

            <div class="card-footer bg-white border-top
                        px-3 px-md-4 py-3">

                <div class="d-flex justify-content-end align-items-center">

                    <nav class="position-pagination"
                         aria-label="Position pagination">

                        {{ $positions->onEachSide(1)->links('pagination::bootstrap-5') }}

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
    .position-description {
        display: inline-block;
        max-width: 360px;
        overflow-wrap: anywhere;
    }

    /* Action Buttons */
    .position-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 7px;
        transition: all 0.2s ease;
    }

    .position-action-btn i {
        font-size: 0.95rem;
    }

    /* Empty State */
    .position-empty-icon {
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
    .position-pagination nav > div:first-child {
        display: none;
    }

    .position-pagination nav > div:last-child {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .position-pagination .pagination {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin: 0;
    }

    .position-pagination .page-item {
        margin: 0;
    }

    .position-pagination .page-link {
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

    .position-pagination .page-link:hover {
        color: #0d6efd;
        background: #f0f6ff;
        border-color: #b6d4fe;
    }

    .position-pagination .page-item.active .page-link {
        color: #fff;
        background: #0d6efd;
        border-color: #0d6efd;
        font-weight: 600;
    }

    .position-pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
    }

    .position-pagination .page-link:focus {
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }

    /* Responsive */
    @media (max-width: 576px) {

        .position-pagination {
            max-width: 100%;
        }

        .position-pagination .pagination {
            gap: 4px;
        }

        .position-pagination .page-link {
            min-width: 34px;
            height: 34px;
            padding: 0 9px;
            font-size: 13px;
        }

        .position-description {
            max-width: 200px;
        }

    }
</style>

@endsection
