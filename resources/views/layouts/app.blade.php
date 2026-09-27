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

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>

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
        | Mobile
        |--------------------------------------------------------------------------
        */

        @media (max-width: 991.98px) {

            .sidebar {
                transform: translateX(-100%);

                transition: transform 0.2s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 20px;
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


            <!-- Right Navbar -->

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

                        <li>
                            <a
                                class="dropdown-item"
                                href="#"
                            >
                                <i class="bi bi-person me-2"></i>
                                Profile
                            </a>
                        </li>

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

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="dropdown-item">
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

    <aside
        id="sidebar"
        class="sidebar"
    >

        <!-- Dashboard -->

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


        <!-- Master Data -->

        <div class="sidebar-section">
            Master Data
        </div>

        <nav class="nav flex-column">

            <a
                href="{{ route('divisions.index') }}"
                class="nav-link {{ request()->is('divisions*') ? 'active' : '' }}"
            >

                <i class="bi bi-diagram-3"></i>

                Division

            </a>


            <a
                href="{{ route('positions.index') }}"
                class="nav-link {{ request()->is('positions*') ? 'active' : '' }}"
            >
                <i class="bi bi-person-badge"></i>
                Position
            </a>


            <a
                href="{{ route('projects.index') }}"
                class="nav-link {{ request()->is('projects*') ? 'active' : '' }}"
            >
                <i class="bi bi-building"></i>
                Project
            </a>
        </nav>

        <div class="sidebar-section">
            HR Menu
        </div>

        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#hrMenu"
            role="button"
            aria-expanded="false"
            aria-controls="hrMenu">

                <span>
                    <i class="bi bi-people me-2"></i>
                    HR
                </span>

                <i class="bi bi-chevron-down small"></i>
            </a>

            <div class="collapse" id="hrMenu">
                <ul class="nav flex-column ms-4">

                    @can('employee.view-any')
                        <li class="nav-item">
                            <a href="{{ route('employees.index') }}"
                            class="nav-link">
                                <i class="bi bi-person me-2"></i>
                                Employee
                            </a>
                        </li>
                    @endcan

                    <li class="nav-item">
                        <a href="#" class="nav-link text-muted">
                            <i class="bi bi-calendar-x me-2"></i>
                            Leave
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link text-muted">
                            <i class="bi bi-calendar-check me-2"></i>
                            Attendance
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link text-muted">
                            <i class="bi bi-clock me-2"></i>
                            Timesheet
                        </a>
                    </li>

                </ul>
            </div>
        </li>


        <!-- Transaction -->

        <div class="sidebar-section">
            Transaction
        </div>

        <nav class="nav flex-column">

            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-calendar-check"></i>

                Leave

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-airplane"></i>

                Travel

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-cart"></i>

                Purchase Request

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-check2-square"></i>

                Approval

            </a>

        </nav>


        <!-- Reports -->

        <div class="sidebar-section">
            Reports
        </div>

        <nav class="nav flex-column">

            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-bar-chart"></i>

                Reports

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-file-earmark-text"></i>

                Documents

            </a>

        </nav>


        <!-- System -->

        <div class="sidebar-section">
            System
        </div>

        <nav class="nav flex-column">

            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-person-gear"></i>

                Users & Roles

            </a>


            <a
                href="#"
                class="nav-link"
            >

                <i class="bi bi-clock-history"></i>

                Audit Log

            </a>

        </nav>

    </aside>


    <!-- ========================================================= -->
    <!-- MAIN CONTENT -->
    <!-- ========================================================= -->

    <main class="main-content">

        <div class="content-wrapper">

            @yield('content')

        </div>

    </main>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>


    <script>

        function toggleSidebar()
        {
            document
                .getElementById('sidebar')
                .classList
                .toggle('show');
        }

    </script>

    @stack('scripts')

</body>

</html>