@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">


<div>
    <h4 class="mb-1">Project Detail</h4>

    <p class="text-muted mb-0">
        View project information
    </p>
</div>

<div class="d-flex gap-2">

    <a
        href="{{ route('projects.edit', $project) }}"
        class="btn btn-warning"
    >
        <i class="bi bi-pencil"></i>
        Edit
    </a>

    <a
        href="{{ route('projects.index') }}"
        class="btn btn-secondary"
    >
        Back
    </a>

</div>


</div>

<div class="card border-0 shadow-sm">


<div class="card-body">

    <div class="row mb-3">

        <div class="col-md-3">
            <strong>Project Name</strong>
        </div>

        <div class="col-md-9">
            {{ $project->name }}
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-3">
            <strong>Location</strong>
        </div>

        <div class="col-md-9">
            {{ $project->location }}
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-3">
            <strong>Cost Center</strong>
        </div>

        <div class="col-md-9">
            {{ $project->cost_center }}
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-3">
            <strong>Approval Document</strong>
        </div>

        <div class="col-md-9">
            {{ $project->approvalEmployee?->full_name ?? '-' }}
        </div>

    </div>

    <div class="row">

        <div class="col-md-3">
            <strong>Created At</strong>
        </div>

        <div class="col-md-9">
            {{ $project->created_at->format('d M Y H:i') }}
        </div>

    </div>

</div>


</div>

@endsection
