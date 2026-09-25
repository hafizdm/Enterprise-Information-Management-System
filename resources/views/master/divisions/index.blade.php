@extends('layouts.app')

@section('title', 'Master Division - EIMS')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="page-title">
            Master Division
        </h1>

        <p class="page-description mb-0">
            Manage company divisions
        </p>

    </div>


    <a
        href="{{ route('divisions.create') }}"
        class="btn btn-primary"
    >

        <i class="bi bi-plus-lg me-1"></i>

        Add Division

    </a>

</div>


@if (session('success'))

    <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
    >

        <i class="bi bi-check-circle me-2"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


<div class="card">

    <div class="card-body p-0">

        @if ($divisions->count() > 0)

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="ps-4">
                                No
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end pe-4">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($divisions as $index => $division)

                            <tr>

                                <td class="ps-4">

                                    {{ $index + 1 }}

                                </td>


                                <td>

                                    <strong>
                                        {{ $division->code }}
                                    </strong>

                                </td>


                                <td>

                                    {{ $division->name }}

                                </td>


                                <td>

                                    {{ $division->description ?: '-' }}

                                </td>


                                <td>

                                    @if ($division->is_active)

                                        <span class="badge text-bg-success">

                                            Active

                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">

                                            Inactive

                                        </span>

                                    @endif

                                </td>


                                <td class="text-end pe-4">

                                    <div class="btn-group">

                                        <a
                                            href="{{ route('divisions.show', $division) }}"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                        >

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        <a
                                            href="{{ route('divisions.edit', $division) }}"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Edit"
                                        >

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        <form
                                            action="{{ route('divisions.destroy', $division) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this division?');"
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

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">

                <i class="bi bi-diagram-3 fs-1 text-secondary"></i>

                <h5 class="mt-3">
                    No Division Found
                </h5>

                <p class="text-muted">
                    There are currently no division records.
                </p>

                <a
                    href="{{ route('divisions.create') }}"
                    class="btn btn-primary"
                >

                    <i class="bi bi-plus-lg me-1"></i>

                    Add First Division

                </a>

            </div>

        @endif

    </div>

</div>

@endsection