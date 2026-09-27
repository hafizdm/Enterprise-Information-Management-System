@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Positions</h1>
            <p class="text-muted mb-0">
                Manage employee positions
            </p>
        </div>

        <a href="{{ route('positions.create') }}" class="btn btn-primary">
            + Add Position
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Position</th>
                            <th>Division</th>
                            <th>Description</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($positions as $position)

                            <tr>

                                <td>
                                    {{ $positions->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $position->name }}
                                    </strong>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $position->division->name }}
                                    </span>
                                </td>

                                <td>
                                    {{ $position->description ?? '-' }}
                                </td>

                                <td>

                                    <a href="{{ route('positions.edit', $position) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('positions.destroy', $position) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus position ini?')">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada position.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

          <div class="position-pagination-wrapper">

                {{-- Information --}}
                <div class="text-muted small">
                    Showing
                    <strong>{{ $positions->firstItem() ?? 0 }}</strong>
                    to
                    <strong>{{ $positions->lastItem() ?? 0 }}</strong>
                    of
                    <strong>{{ $positions->total() }}</strong>
                    positions
                </div>

                {{-- Pagination --}}
                @if($positions->hasPages())
                    <nav aria-label="Positions pagination">
                        <ul class="pagination position-pagination mb-0">

                            {{-- Previous --}}
                            @if($positions->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link"
                                    href="{{ $positions->previousPageUrl() }}"
                                    aria-label="Previous">
                                        ‹
                                    </a>
                                </li>
                            @endif


                            {{-- Page Numbers --}}
                            @for($page = 1; $page <= $positions->lastPage(); $page++)

                                @if($page == $positions->currentPage())

                                    <li class="page-item active">
                                        <span class="page-link">
                                            {{ $page }}
                                        </span>
                                    </li>

                                @else

                                    <li class="page-item">
                                        <a class="page-link"
                                        href="{{ $positions->url($page) }}">
                                            {{ $page }}
                                        </a>
                                    </li>

                                @endif

                            @endfor


                            {{-- Next --}}
                            @if($positions->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link"
                                    href="{{ $positions->nextPageUrl() }}"
                                    aria-label="Next">
                                        ›
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">›</span>
                                </li>
                            @endif

                        </ul>
                    </nav>
                @endif

            </div>

          </div>
    </div>

</div>

@endsection

<style>
    .position-pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        width: 100%;
    }

    .position-pagination {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .position-pagination .page-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-size: 14px;
        border: 1px solid #dee2e6;
        color: #0d6efd;
        background-color: #fff;
    }

    .position-pagination .page-item:first-child .page-link {
        border-radius: 6px 0 0 6px;
    }

    .position-pagination .page-item:last-child .page-link {
        border-radius: 0 6px 6px 0;
    }

    .position-pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .position-pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #f8f9fa;
        pointer-events: none;
    }

    .position-pagination .page-link:hover {
        background-color: #f1f5f9;
        color: #0d6efd;
    }

    .position-pagination .page-item.active .page-link:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    @media (max-width: 576px) {
        .position-pagination-wrapper {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

