@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Cost Level Detail</h4>

        <p class="text-muted mb-0">
            View cost level information
        </p>
    </div>

    <div class="d-flex gap-2">

        @can('cost-level.update')
            <a
                href="{{ route('cost-levels.edit', $costLevel) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>
        @endcan

        <a
            href="{{ route('cost-levels.index') }}"
            class="btn btn-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>

</div>

<div class="card shadow-sm">

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-6">

                <h6 class="text-muted mb-1">
                    Cost Level
                </h6>

                <div class="fw-semibold">
                    {{ $costLevel->name }}
                </div>

            </div>

            <div class="col-md-6">

                <h6 class="text-muted mb-1">
                    Status
                </h6>

                @if ($costLevel->is_active)
                    <span class="badge bg-success">
                        Active
                    </span>
                @else
                    <span class="badge bg-secondary">
                        Inactive
                    </span>
                @endif

            </div>

            <div class="col-md-6">

                <h6 class="text-muted mb-1">
                    Meals - Domestic
                </h6>

                <div class="fw-semibold">
                    Rp {{ number_format($costLevel->meals_domestic, 0, ',', '.') }}
                </div>

            </div>

            <div class="col-md-6">

                <h6 class="text-muted mb-1">
                    Allowance - Domestic
                </h6>

                <div class="fw-semibold">
                    Rp {{ number_format($costLevel->allowance_domestic, 0, ',', '.') }}
                </div>

            </div>

            <div class="col-md-6">

                <h6 class="text-muted mb-1">
                    Meals - International
                </h6>

                <div class="fw-semibold">
                    $ {{ number_format($costLevel->meals_international, 2, '.', ',') }}
                </div>

            </div>

            <div class="col-md-6">

                <h6 class="text-muted mb-1">
                    Allowance - International
                </h6>

                <div class="fw-semibold">
                    $ {{ number_format($costLevel->allowance_international, 2, '.', ',') }}
                </div>

            </div>

        </div>

    </div>

</div>

@endsection