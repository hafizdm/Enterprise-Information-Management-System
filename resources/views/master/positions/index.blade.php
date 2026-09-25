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

            <div class="mt-3">
                {{ $positions->links() }}
            </div>

        </div>
    </div>

</div>

@endsection