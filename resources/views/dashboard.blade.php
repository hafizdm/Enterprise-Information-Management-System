@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')

<style>

    .activity-icon {
        width: 40px;
        height: 40px;

        min-width: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;
    }

</style>

@endpush

@section('content')

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="page-title mb-1">Dashboard</h1>

            <p class="page-description mb-0">
                Overview of your organization's information and activities.
            </p>
        </div>

        <div class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i>
            September 2026
        </div>

    </div>


    <!-- ========================================================= -->
    <!-- KPI CARDS -->
    <!-- ========================================================= -->

    <div class="row g-4 mb-4">

        <!-- Employees -->
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <p class="text-muted small mb-2">
                                Total Employees
                            </p>

                            <h3 class="fw-bold mb-2">
                                248
                            </h3>

                            <span class="text-success small">
                                <i class="bi bi-arrow-up"></i>
                                8.2%
                            </span>

                            <span class="text-muted small">
                                vs last month
                            </span>

                        </div>

                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">

                            <i class="bi bi-people fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Projects -->
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <p class="text-muted small mb-2">
                                Active Projects
                            </p>

                            <h3 class="fw-bold mb-2">
                                36
                            </h3>

                            <span class="text-success small">
                                <i class="bi bi-arrow-up"></i>
                                4.5%
                            </span>

                            <span class="text-muted small">
                                vs last month
                            </span>

                        </div>

                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">

                            <i class="bi bi-building fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Requests -->
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <p class="text-muted small mb-2">
                                Pending Requests
                            </p>

                            <h3 class="fw-bold mb-2">
                                42
                            </h3>

                            <span class="text-danger small">
                                <i class="bi bi-arrow-down"></i>
                                5.2%
                            </span>

                            <span class="text-muted small">
                                vs last month
                            </span>

                        </div>

                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">

                            <i class="bi bi-hourglass-split fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Documents -->
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <p class="text-muted small mb-2">
                                Total Documents
                            </p>

                            <h3 class="fw-bold mb-2">
                                1,284
                            </h3>

                            <span class="text-success small">
                                <i class="bi bi-arrow-up"></i>
                                12.4%
                            </span>

                            <span class="text-muted small">
                                vs last month
                            </span>

                        </div>

                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">

                            <i class="bi bi-file-earmark-text fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- CHARTS -->
    <!-- ========================================================= -->

    <div class="row g-4 mb-4">

        <!-- Request Overview -->

        <div class="col-12 col-xl-7">

            <div class="card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Request Overview
                            </h5>

                            <p class="text-muted small mb-0">
                                Request activity by status
                            </p>

                        </div>

                        <button class="btn btn-sm btn-light">
                            <i class="bi bi-three-dots"></i>
                        </button>

                    </div>

                    <div style="height: 300px;">
                        <canvas id="requestChart"></canvas>
                    </div>

                </div>

            </div>

        </div>


        <!-- Project Status -->

        <div class="col-12 col-xl-5">

            <div class="card h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Project Status
                            </h5>

                            <p class="text-muted small mb-0">
                                Current project distribution
                            </p>

                        </div>

                        <button class="btn btn-sm btn-light">
                            <i class="bi bi-three-dots"></i>
                        </button>

                    </div>

                    <div style="height: 300px;">
                        <canvas id="projectChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- EMPLOYEES BY DEPARTMENT -->
    <!-- ========================================================= -->

    <div class="row g-4 mb-4">

        <div class="col-12">

            <div class="card">

                <div class="card-body">

                    <div class="mb-4">

                        <h5 class="fw-bold mb-1">
                            Employees by Department
                        </h5>

                        <p class="text-muted small mb-0">
                            Employee distribution across departments
                        </p>

                    </div>

                    <div style="height: 320px;">
                        <canvas id="departmentChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- RECENT ACTIVITIES + QUICK ACTIONS -->
    <!-- ========================================================= -->

    <div class="row g-4">

        <!-- Recent Activities -->

        <div class="col-12 col-xl-7">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Recent Activities
                            </h5>

                            <p class="text-muted small mb-0">
                                Latest activities in EIMS
                            </p>

                        </div>

                        <a href="#" class="small text-decoration-none">
                            View all
                        </a>

                    </div>


                    <div class="list-group list-group-flush">

                        <div class="list-group-item px-0 py-3">

                            <div class="d-flex">

                                <div class="activity-icon bg-success bg-opacity-10 text-success me-3">
                                    <i class="bi bi-check-lg"></i>
                                </div>

                                <div>

                                    <div class="fw-semibold">
                                        Purchase Request approved
                                    </div>

                                    <div class="text-muted small">
                                        PR-2026-0012 was approved by Finance
                                    </div>

                                    <div class="text-muted small mt-1">
                                        10 minutes ago
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="list-group-item px-0 py-3">

                            <div class="d-flex">

                               <div class="activity-icon bg-primary bg-opacity-10 text-primary me-3">
                                    <i class="bi bi-person-plus"></i>
                                </div>

                                <div>

                                    <div class="fw-semibold">
                                        New employee added
                                    </div>

                                    <div class="text-muted small">
                                        Employee profile has been created
                                    </div>

                                    <div class="text-muted small mt-1">
                                        35 minutes ago
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="list-group-item px-0 py-3">

                            <div class="d-flex">

                              <div class="activity-icon bg-info bg-opacity-10 text-info me-3">
                                    <i class="bi bi-building"></i>
                                </div>

                                <div>

                                    <div class="fw-semibold">
                                        Project updated
                                    </div>

                                    <div class="text-muted small">
                                        Project information has been updated
                                    </div>

                                    <div class="text-muted small mt-1">
                                        1 hour ago
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="list-group-item px-0 py-3">

                            <div class="d-flex">

                               <div class="activity-icon bg-warning bg-opacity-10 text-warning me-3">
                                    <i class="bi bi-file-earmark-plus"></i>
                                </div>

                                <div>

                                    <div class="fw-semibold">
                                        New document uploaded
                                    </div>

                                    <div class="text-muted small">
                                        Company Policy 2026.pdf
                                    </div>

                                    <div class="text-muted small mt-1">
                                        2 hours ago
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Quick Actions -->

        <div class="col-12 col-xl-5">

            <div class="card">

                <div class="card-body">

                    <div class="mb-4">

                        <h5 class="fw-bold mb-1">
                            Quick Actions
                        </h5>

                        <p class="text-muted small mb-0">
                            Frequently used actions
                        </p>

                    </div>


                    <div class="d-grid gap-3">

                        <a href="{{ route('projects.create') }}"
                           class="btn btn-light text-start p-3">

                            <i class="bi bi-building text-primary me-2"></i>

                            Create New Project

                            <i class="bi bi-chevron-right float-end mt-1"></i>

                        </a>


                        <a href="{{ route('divisions.create') }}"
                           class="btn btn-light text-start p-3">

                            <i class="bi bi-diagram-3 text-primary me-2"></i>

                            Add Division

                            <i class="bi bi-chevron-right float-end mt-1"></i>

                        </a>


                        <a href="{{ route('positions.create') }}"
                           class="btn btn-light text-start p-3">

                            <i class="bi bi-person-badge text-primary me-2"></i>

                            Add Position

                            <i class="bi bi-chevron-right float-end mt-1"></i>

                        </a>


                        <button type="button"
                                class="btn btn-light text-start p-3">

                            <i class="bi bi-file-earmark-plus text-primary me-2"></i>

                            Create Request

                            <i class="bi bi-chevron-right float-end mt-1"></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('scripts')

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // =========================================================
    // REQUEST CHART
    // =========================================================

    new Chart(
        document.getElementById('requestChart'),
        {
            type: 'bar',

            data: {
                labels: [
                    'Pending',
                    'In Progress',
                    'Approved',
                    'Rejected',
                    'Completed'
                ],

                datasets: [{
                    label: 'Requests',

                    data: [
                        42,
                        28,
                        156,
                        12,
                        94
                    ],

                    borderWidth: 0,

                    borderRadius: 6
                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }
                    },

                    x: {
                        grid: {
                            display: false
                        }
                    }

                }

            }

        }
    );


    // =========================================================
    // PROJECT CHART
    // =========================================================

    new Chart(
        document.getElementById('projectChart'),
        {
            type: 'doughnut',

            data: {

                labels: [
                    'On Track',
                    'At Risk',
                    'Delayed',
                    'Completed'
                ],

                datasets: [{

                    data: [
                        24,
                        7,
                        5,
                        18
                    ],

                    borderWidth: 0

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '65%',

                plugins: {

                    legend: {
                        position: 'bottom'
                    }

                }

            }

        }
    );


    // =========================================================
    // DEPARTMENT CHART
    // =========================================================

    new Chart(
        document.getElementById('departmentChart'),
        {
            type: 'bar',

            data: {

                labels: [
                    'Operations',
                    'Engineering',
                    'IT',
                    'Finance',
                    'Human Capital',
                    'GA',
                    'Procurement'
                ],

                datasets: [{

                    label: 'Employees',

                    data: [
                        86,
                        42,
                        32,
                        28,
                        24,
                        21,
                        15
                    ],

                    borderWidth: 0,

                    borderRadius: 6

                }]

            },

            options: {

                indexAxis: 'y',

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    x: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }
                    },

                    y: {

                        grid: {
                            display: false
                        }

                    }

                }

            }

        }
    );

</script>

@endpush