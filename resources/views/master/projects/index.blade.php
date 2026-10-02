@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Project</h4>
        <p class="text-muted mb-0">
            Manage project master data
        </p>
    </div>


<a href="{{ route('projects.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i>
    Add Project
</a>


</div>

@if(session('success')) <div class="alert alert-success alert-dismissible fade show" role="alert">
{{ session('success') }}


    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
    ></button>
</div>


@endif

<div class="card border-0 shadow-sm">


<div class="card-body">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th width="60">#</th>
                    <th>Project Name</th>
                    <th>Location</th>
                    <th>Cost Center</th>
                    <th>Approval Document</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($projects as $project)

                    <tr>

                        <td>
                            {{ $projects->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <strong>
                                {{ $project->name }}
                            </strong>
                        </td>

                        <td>
                            {{ $project->location }}
                        </td>

                        <td>
                            {{ $project->cost_center }}
                        </td>

                        <td>
                            {{ $project->approvalEmployee?->full_name ?? '-' }}
                        </td>

                        <td>

                            <a
                                href="{{ route('projects.show', $project) }}"
                                class="btn btn-sm btn-outline-info"
                                title="View"
                            >
                                <i class="bi bi-eye"></i>
                            </a>

                            <a
                                href="{{ route('projects.edit', $project) }}"
                                class="btn btn-sm btn-outline-warning"
                                title="Edit"
                            >
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form
                                action="{{ route('projects.destroy', $project) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus project ini?')"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Belum ada data project.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($projects->hasPages())
        <div class="mt-3">
            {{ $projects->links() }}
        </div>
    @endif

</div>


</div>

@endsection
