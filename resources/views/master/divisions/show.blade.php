@extends('layouts.app')

@section('title', 'Division Detail - EIMS')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="page-title">
            Division Detail
        </h1>

        <p class="page-description mb-0">
            View division information
        </p>

    </div>


    <a
        href="{{ route('divisions.index') }}"
        class="btn btn-secondary"
    >

        <i class="bi bi-arrow-left me-1"></i>

        Back

    </a>

</div>


<div class="card">

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-3 text-muted">
                ID
            </div>

            <div class="col-md-9">
                {{ $division->id }}
            </div>

        </div>


        <div class="row mb-3">

            <div class="col-md-3 text-muted">
                Division Code
            </div>

            <div class="col-md-9">

                <strong>
                    {{ $division->code }}
                </strong>

            </div>

        </div>


        <div class="row mb-3">

            <div class="col-md-3 text-muted">
                Division Name
            </div>

            <div class="col-md-9">
                {{ $division->name }}
            </div>

        </div>


        <div class="row mb-3">

            <div class="col-md-3 text-muted">
                Description
            </div>

            <div class="col-md-9">
                {{ $division->description ?: '-' }}
            </div>

        </div>


        <div class="row mb-3">

            <div class="col-md-3 text-muted">
                Status
            </div>

            <div class="col-md-9">

                @if ($division->is_active)

                    <span class="badge text-bg-success">
                        Active
                    </span>

                @else

                    <span class="badge text-bg-secondary">
                        Inactive
                    </span>

                @endif

            </div>

        </div>


        <div class="row mb-3">

            <div class="col-md-3 text-muted">
                Created At
            </div>

            <div class="col-md-9">
                {{ $division->created_at->format('d M Y H:i') }}
            </div>

        </div>


        <div class="row">

            <div class="col-md-3 text-muted">
                Updated At
            </div>

            <div class="col-md-9">
                {{ $division->updated_at->format('d M Y H:i') }}
            </div>

        </div>

    </div>

</div>


<div class="mt-3">

    <a
        href="{{ route('divisions.edit', $division) }}"
        class="btn btn-warning"
    >

        <i class="bi bi-pencil me-1"></i>

        Edit Division

    </a>

</div>

@endsection