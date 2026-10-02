@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Cost Level</h4>

        <p class="text-muted mb-0">
            Manage employee travel cost levels and daily rates.
        </p>
    </div>

    @can('cost-level.create')
        <a
            href="{{ route('cost-levels.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Add Cost Level
        </a>
    @endcan

</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>
@endif

@if($costLevels->count())

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="px-4">Cost Level</th>
                            <th>Meals Domestic</th>
                            <th>Allowance Domestic</th>
                            <th>Meals International</th>
                            <th>Allowance International</th>
                            <th>Status</th>
                            <th class="text-end px-4">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($costLevels as $costLevel)

                            <tr>

                                <td class="px-4 fw-semibold">
                                    {{ $costLevel->name }}
                                </td>

                                <td>
                                    Rp {{ number_format($costLevel->meals_domestic, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($costLevel->allowance_domestic, 0, ',', '.') }}
                                </td>

                                <td>
                                    ${{ number_format($costLevel->meals_international, 2, '.', ',') }}
                                </td>

                                <td>
                                    ${{ number_format($costLevel->allowance_international, 2, '.', ',') }}
                                </td>

                                <td>

                                    @if($costLevel->is_active)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td class="text-end px-4">

                                    <div class="d-flex justify-content-end gap-2">

                                        @can('cost-level.view')

                                            <a
                                                href="{{ route('cost-levels.show', $costLevel) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="View"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </a>

                                        @endcan

                                        @can('cost-level.update')

                                            <a
                                                href="{{ route('cost-levels.edit', $costLevel) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                        @endcan

                                        @can('cost-level.delete')

                                            <form
                                                action="{{ route('cost-levels.destroy', $costLevel) }}"
                                                method="POST"
                                                class="d-inline"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this Cost Level?');"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </form>

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="mt-3">
        {{ $costLevels->links() }}
    </div>

@else

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <i class="bi bi-layers fs-1 text-muted"></i>

            <h5 class="mt-3">
                No Cost Levels Found
            </h5>

            <p class="text-muted mb-3">
                No cost levels have been created yet.
            </p>

            @can('cost-level.create')

                <a
                    href="{{ route('cost-levels.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Cost Level
                </a>

            @endcan

        </div>

    </div>

@endif

@endsection