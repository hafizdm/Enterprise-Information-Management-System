<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'EIMS')
    </title>


    <!-- ========================================================= -->
    <!-- Bootstrap 5 -->
    <!-- ========================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ========================================================= -->
    <!-- Bootstrap Icons -->
    <!-- ========================================================= -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | General
        |--------------------------------------------------------------------------
        */

        body {
            background-color: #f5f7fb;
        }


        /*
        |--------------------------------------------------------------------------
        | Navbar
        |--------------------------------------------------------------------------
        */

        .main-navbar {
            height: 64px;

            background: #ffffff;

            border-bottom: 1px solid #e5e7eb;
        }


        .brand {
            font-size: 20px;

            font-weight: 700;

            color: #2563eb;

            text-decoration: none;
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        */

        .sidebar {
            position: fixed;

            top: 64px;
            left: 0;
            bottom: 0;

            width: 250px;

            background: #ffffff;

            border-right: 1px solid #e5e7eb;

            overflow-y: auto;

            padding: 20px 15px;
        }


        .sidebar-section {
            font-size: 11px;

            font-weight: 700;

            color: #9ca3af;

            text-transform: uppercase;

            margin-top: 20px;

            margin-bottom: 8px;

            padding-left: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar Navigation Link
        |--------------------------------------------------------------------------
        */

        .sidebar .nav-link {
            display: flex;

            align-items: center;

            gap: 10px;

            color: #4b5563;

            padding: 10px 12px;

            margin-bottom: 3px;

            border-radius: 7px;

            font-size: 14px;
        }


        .sidebar .nav-link:hover {
            background-color: #f3f4f6;

            color: #2563eb;
        }


        .sidebar .nav-link.active {
            background-color: #eff6ff;

            color: #2563eb;

            font-weight: 600;
        }


        .sidebar .nav-link i {
            font-size: 17px;

            width: 20px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar Disabled Link
        |--------------------------------------------------------------------------
        */

        .sidebar .nav-link.text-muted {
            color: #9ca3af !important;

            cursor: default;
        }


        .sidebar .nav-link.text-muted:hover {
            background-color: transparent;

            color: #9ca3af !important;
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar Group Button
        |--------------------------------------------------------------------------
        */

        .sidebar .nav-group-button {
            display: flex;

            align-items: center;

            justify-content: space-between;

            width: 100%;

            border: none;

            background: transparent;

            color: #4b5563;

            padding: 10px 12px;

            margin-bottom: 3px;

            border-radius: 7px;

            font-size: 14px;

            text-align: left;

            cursor: pointer;
        }


        .sidebar .nav-group-button:hover {
            background-color: #f3f4f6;

            color: #2563eb;
        }


        .sidebar .nav-group-label {
            display: flex;

            align-items: center;

            gap: 10px;
        }


        .sidebar .nav-group-label i {
            font-size: 17px;

            width: 20px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar Group Arrow
        |--------------------------------------------------------------------------
        */

        .sidebar .nav-group-arrow {
            font-size: 12px;

            transition: transform 0.2s ease;
        }


        .sidebar .nav-group-button[aria-expanded="true"]
        .nav-group-arrow {
            transform: rotate(90deg);
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar Submenu
        |--------------------------------------------------------------------------
        */

        .sidebar .submenu {
            padding-left: 20px;

            margin-bottom: 5px;
        }


        .sidebar .submenu .nav-link {
            font-size: 13px;

            padding: 8px 12px;
        }


        .sidebar .submenu .nav-link i {
            font-size: 15px;
        }


        /*
        |--------------------------------------------------------------------------
        | Main Content
        |--------------------------------------------------------------------------
        */

        .main-content {
            margin-left: 250px;
            padding-top: 64px;
            min-height: 100vh;
            min-width: 0;
            box-sizing: border-box;
        }


        .content-wrapper {
            min-width: 0;
            box-sizing: border-box;
        }


        .main-navbar {
            z-index: 1050;
        }


        .main-navbar .container-fluid,
        .main-navbar .d-flex {
            min-width: 0;
        }


        .main-navbar .dropdown-menu {
            z-index: 1060;
        }


        .content-wrapper {
            padding: 30px;
        }


        /*
        |--------------------------------------------------------------------------
        | Page Header
        |--------------------------------------------------------------------------
        */

        .page-title {
            font-size: 26px;

            font-weight: 700;

            margin-bottom: 5px;
        }


        .page-description {
            color: #6b7280;

            margin-bottom: 25px;
        }


        /*
        |--------------------------------------------------------------------------
        | Cards
        |--------------------------------------------------------------------------
        */

        .card {
            border: none;

            border-radius: 10px;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.04);
        }


        /*
        |--------------------------------------------------------------------------
        | Responsive Layout
        |--------------------------------------------------------------------------
        */

        .sidebar-backdrop {
            display: none;
        }


        @media (max-width: 991.98px) {

            .sidebar {
                position: fixed;

                top: 64px;
                left: 0;
                bottom: 0;

                width: min(280px, 85vw);

                z-index: 1040;

                background: #ffffff;

                transform: translateX(-100%);

                transition: transform 0.25s ease;

                overflow-y: auto;

                box-shadow: none;
            }


            .sidebar.show {
                transform: translateX(0);

                box-shadow: 4px 0 16px rgba(0, 0, 0, 0.08);
            }


            .sidebar-backdrop.show {
                display: block;

                position: fixed;

                inset: 64px 0 0;

                z-index: 1035;

                background: rgba(15, 23, 42, 0.35);
            }


            .main-content {
                margin-left: 0;

                width: 100%;

                min-width: 0;

                padding-top: 64px;
            }


            .content-wrapper {
                width: 100%;

                min-width: 0;

                padding: 20px 16px;

                box-sizing: border-box;
            }


            .main-navbar .container-fluid {
                padding-left: 12px !important;

                padding-right: 12px !important;

                gap: 8px;
            }


            .main-navbar .d-flex.align-items-center.gap-3 {
                gap: 8px !important;

                min-width: 0;
            }


            .main-navbar .dropdown-toggle {
                max-width: 45vw;

                overflow: hidden;

                text-overflow: ellipsis;

                white-space: nowrap;
            }


            .page-title {
                font-size: 22px;

                overflow-wrap: anywhere;
            }


            .page-description {
                overflow-wrap: anywhere;
            }

        }


        @media (max-width: 575.98px) {

            .content-wrapper {
                padding: 16px 12px;
            }


            .main-navbar .btn {
                padding: 8px 10px;
            }


            .brand {
                font-size: 19px;
            }

        }

    </style>


    @stack('styles')

</head>


<body>


<!-- ========================================================= -->
<!-- TOP NAVBAR -->
<!-- ========================================================= -->

<nav class="navbar fixed-top main-navbar">

    <div class="container-fluid px-4">


        <!-- ===================================================== -->
        <!-- LEFT -->
        <!-- ===================================================== -->

        <div class="d-flex align-items-center">


            <!-- Mobile Sidebar Button -->

            <button
                class="btn btn-light d-lg-none me-2"
                type="button"
                onclick="toggleSidebar()"
            >

                <i class="bi bi-list fs-5"></i>

            </button>


            <!-- Brand -->

            <a
                href="{{ url('/') }}"
                class="brand"
            >
                EIMS
            </a>

        </div>


        <!-- ===================================================== -->
        <!-- RIGHT -->
        <!-- ===================================================== -->

        <div class="d-flex align-items-center gap-3">


            <!-- Notification -->

            <button
                class="btn btn-light position-relative"
                type="button"
            >

                <i class="bi bi-bell"></i>

            </button>


            <!-- User -->

            <div class="dropdown">

                <button
                    class="btn btn-light dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                >

                    <i class="bi bi-person-circle me-1"></i>

                    {{ auth()->user()->username }}

                </button>


                <ul class="dropdown-menu dropdown-menu-end">


                    <!-- Profile -->

                    <li>

                        <a
                            class="dropdown-item"
                            href="#"
                        >

                            <i class="bi bi-person me-2"></i>

                            Profile

                        </a>

                    </li>


                    <!-- Settings -->

                    <li>

                        <a
                            class="dropdown-item"
                            href="#"
                        >

                            <i class="bi bi-gear me-2"></i>

                            Settings

                        </a>

                    </li>


                    <li>

                        <hr class="dropdown-divider">

                    </li>


                    <!-- Logout -->

                    <li>

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item"
                            >

                                <i class="bi bi-box-arrow-right me-2"></i>

                                Logout

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</nav>


<!-- ========================================================= -->
<!-- SIDEBAR -->
<!-- ========================================================= -->

<div
    id="sidebarBackdrop"
    class="sidebar-backdrop"
    onclick="toggleSidebar(false)"
></div>


<aside
    id="sidebar"
    class="sidebar"
>


    <!-- ===================================================== -->
    <!-- GENERAL -->
    <!-- ===================================================== -->

    <div class="sidebar-section">
        General
    </div>


    <nav class="nav flex-column">

        <a
            href="{{ url('/') }}"
            class="nav-link {{ request()->is('/') ? 'active' : '' }}"
        >

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>

    </nav>


    <!-- ===================================================== -->
    <!-- MASTER DATA -->
    <!-- SYSTEM ADMINISTRATOR ONLY -->
    <!-- ===================================================== -->

    @if(auth()->user()->hasRole('System Administrator'))

        <div class="sidebar-section">
            Master Data
        </div>


        <nav class="nav flex-column">


            <!-- Division -->

            <a
                href="{{ route('divisions.index') }}"
                class="nav-link {{ request()->is('divisions*') ? 'active' : '' }}"
            >

                <i class="bi bi-diagram-3"></i>

                Division

            </a>


            <!-- Position -->

            <a
                href="{{ route('positions.index') }}"
                class="nav-link {{ request()->is('positions*') ? 'active' : '' }}"
            >

                <i class="bi bi-person-badge"></i>

                Position

            </a>


            <!-- Project -->

            <a
                href="{{ route('projects.index') }}"
                class="nav-link {{ request()->is('projects*') ? 'active' : '' }}"
            >

                <i class="bi bi-building"></i>

                Project

            </a>


            <!-- Cost Levels -->

            <a
                href="{{ route('cost-levels.index') }}"
                class="nav-link {{ request()->is('cost-levels*') ? 'active' : '' }}"
            >

                <i class="bi bi-cash-stack"></i>

                Cost Levels

            </a>

        </nav>

    @endif


    <!-- ===================================================== -->
    <!-- HR -->
    <!-- ===================================================== -->

    @if(
        auth()->user()->hasRole('Employee') ||
        auth()->user()->hasRole('Employee Approval') ||
        auth()->user()->hasRole('HRD')
    )

        <div class="sidebar-section">
            HR
        </div>


        <nav class="nav flex-column">


            <!-- ================================================= -->
            <!-- EMPLOYEE -->
            <!-- HRD ONLY -->
            <!-- ================================================= -->

            @if(auth()->user()->hasRole('HRD'))

                <a
                    href="{{ route('employees.index') }}"
                    class="nav-link {{ request()->is('employees*') ? 'active' : '' }}"
                >

                    <i class="bi bi-person"></i>

                    Employee

                </a>

            @endif


            <!-- ================================================= -->
            <!-- LEAVE -->
            <!-- ================================================= -->

            @php

                $leaveOpen =
                    request()->is('leave-requests*') ||
                    request()->is('leave-monitoring*') ||
                    request()->is('leave-approvals*');

            @endphp


            <button
                type="button"
                class="nav-group-button"
                data-bs-toggle="collapse"
                data-bs-target="#leaveSubmenu"
                aria-expanded="{{ $leaveOpen ? 'true' : 'false' }}"
            >

                <span class="nav-group-label">

                    <i class="bi bi-calendar-check"></i>

                    Leave

                </span>


                <i class="bi bi-chevron-right nav-group-arrow"></i>

            </button>


            <div
                id="leaveSubmenu"
                class="collapse submenu {{ $leaveOpen ? 'show' : '' }}"
            >


                <!-- ================================================= -->
                <!-- MY LEAVE -->
                <!-- Employee / Employee Approval -->
                <!-- ================================================= -->

                @if(
                    auth()->user()->hasRole('Employee') ||
                    auth()->user()->hasRole('Employee Approval')
                )

                    <a
                        href="{{ route('leave-requests.index') }}"
                        class="nav-link {{ request()->is('leave-requests*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-calendar-check"></i>

                        My Leave

                    </a>

                @endif


                <!-- ================================================= -->
                <!-- LEAVE MONITORING -->
                <!-- HRD ONLY -->
                <!-- ================================================= -->

                @if(auth()->user()->hasRole('HRD'))

                    <a
                        href="{{ route('leave-monitoring.index') }}"
                        class="nav-link {{ request()->is('leave-monitoring*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-eye"></i>

                        Leave Monitoring

                    </a>

                @endif


                <!-- ================================================= -->
                <!-- LEAVE APPROVAL -->
                <!-- EMPLOYEE APPROVAL ONLY -->
                <!-- ================================================= -->

                @if(auth()->user()->hasRole('Employee Approval'))

                    <a
                        href="{{ route('leave-approvals.index') }}"
                        class="nav-link {{ request()->is('leave-approvals*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-check2-square"></i>

                        Leave Approval

                    </a>

                @endif

            </div>


            <!-- ================================================= -->
            <!-- SPD -->
            <!-- ================================================= -->

            @php

                $spdOpen =
                    request()->is('spds*') ||
                    request()->is('spd-approvals*') ||
                    request()->is('spd-reports*') ||
                    request()->is('spd-report-approvals*');

            @endphp


            <button
                type="button"
                class="nav-group-button"
                data-bs-toggle="collapse"
                data-bs-target="#spdSubmenu"
                aria-expanded="{{ $spdOpen ? 'true' : 'false' }}"
            >

                <span class="nav-group-label">

                    <i class="bi bi-file-earmark-text"></i>

                    SPD

                </span>


                <i class="bi bi-chevron-right nav-group-arrow"></i>

            </button>


            <div
                id="spdSubmenu"
                class="collapse submenu {{ $spdOpen ? 'show' : '' }}"
            >


                <!-- ================================================= -->
                <!-- ADD SPD -->
                <!-- HRD ONLY -->
                <!-- ================================================= -->

                @if(auth()->user()->hasRole('HRD'))

                    <a
                        href="{{ route('spds.index') }}"
                        class="nav-link {{ request()->is('spds*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-file-earmark-plus"></i>

                        Add SPD

                    </a>

                @endif


                <!-- ================================================= -->
                <!-- SPD APPROVAL -->
                <!-- EMPLOYEE APPROVAL ONLY -->
                <!-- ================================================= -->

                @if(auth()->user()->hasRole('Employee Approval'))

                    <a
                        href="{{ route('spd.approvals.index') }}"
                        class="nav-link {{ request()->is('spd-approvals*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-file-earmark-check"></i>

                        SPD Approval

                    </a>

                @endif


                <!-- ================================================= -->
                <!-- SPD REPORT -->
                <!-- EMPLOYEE ONLY -->
                <!-- ================================================= -->

                @if(auth()->user()->hasRole('Employee'))

                    <a
                        href="{{ route('spd-reports.index') }}"
                        class="nav-link {{ request()->is('spd-reports*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-file-earmark-bar-graph"></i>

                        SPD Report

                    </a>

                @endif


                <!-- ================================================= -->
                <!-- SPD REPORT APPROVAL -->
                <!-- EMPLOYEE APPROVAL ONLY -->
                <!-- ================================================= -->

                @if(auth()->user()->hasRole('Employee Approval'))

                    <a
                        href="{{ route('spd-report-approvals.index') }}"
                        class="nav-link {{ request()->is('spd-report-approvals*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-file-earmark-check-fill"></i>

                        SPD Report Approval

                    </a>

                @endif

            </div>


            <!-- ================================================= -->
            <!-- ATTENDANCE -->
            <!-- FUTURE -->
            <!-- ================================================= -->

            <a
                href="#"
                class="nav-link text-muted"
            >

                <i class="bi bi-calendar-check"></i>

                Attendance

            </a>


            <!-- ================================================= -->
            <!-- TIMESHEET -->
            <!-- FUTURE -->
            <!-- ================================================= -->

            <a
                href="#"
                class="nav-link text-muted"
            >

                <i class="bi bi-clock"></i>

                Timesheet

            </a>

        </nav>

    @endif


    <!-- ========================================================= -->
    <!-- TRANSACTION -->
    <!-- TEMPORARILY HIDDEN -->
    <!-- ========================================================= -->


    <!-- ========================================================= -->
    <!-- REPORTS -->
    <!-- TEMPORARILY HIDDEN -->
    <!-- ========================================================= -->


    <!-- ========================================================= -->
    <!-- SYSTEM -->
    <!-- SYSTEM ADMINISTRATOR ONLY -->
    <!-- ========================================================= -->

    @if(auth()->user()->hasRole('System Administrator'))

        <div class="sidebar-section">
            System
        </div>


        <nav class="nav flex-column">


            <!-- ================================================= -->
            <!-- USERS & ROLES -->
            <!-- ================================================= -->

            @can('user.view')

                <a
                    href="{{ route('users.index') }}"
                    class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                >

                    <i class="bi bi-person-gear"></i>

                    Users & Roles

                </a>

            @endcan


            <!-- ================================================= -->
            <!-- AUDIT LOG -->
            <!-- FUTURE -->
            <!-- ================================================= -->

            <a
                href="#"
                class="nav-link text-muted"
            >

                <i class="bi bi-clock-history"></i>

                Audit Log

            </a>

        </nav>

    @endif

</aside>


<!-- ========================================================= -->
<!-- MAIN CONTENT -->
<!-- ========================================================= -->

<main class="main-content">

    <div class="content-wrapper">

        @yield('content')

    </div>

</main>


<!-- ========================================================= -->
<!-- BOOTSTRAP JS -->
<!-- ========================================================= -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>

    function toggleSidebar(forceState) {

        const sidebar =
            document.getElementById('sidebar');

        const backdrop =
            document.getElementById('sidebarBackdrop');

        if (!sidebar || !backdrop) {
            return;
        }

        const shouldOpen =
            typeof forceState === 'boolean'
                ? forceState
                : !sidebar.classList.contains('show');

        sidebar.classList.toggle(
            'show',
            shouldOpen
        );

        backdrop.classList.toggle(
            'show',
            shouldOpen
        );

        document.body.style.overflow =
            shouldOpen
                ? 'hidden'
                : '';
    }


    window.addEventListener(
        'resize',
        function () {

            if (window.innerWidth > 991.98) {
                toggleSidebar(false);
            }

        }
    );

</script>


@stack('scripts')

</body>

</html>

