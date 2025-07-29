<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Developer Portal')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar {
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        .sidebar.collapsed .dropdown-menu {
            display: none !important;
        }
        .sidebar.collapsed .dropdown-toggle::after {
            display: none;
        }
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }
        .sidebar.collapsed .nav-link i {
            margin-right: 0 !important;
        }
        .sidebar.collapsed .d-flex {
            justify-content: center;
        }
        .sidebar.collapsed .d-flex i {
            margin-right: 0 !important;
        }
        .sidebar.collapsed .nav-link:hover {
            transform: none !important;
        }

        .sidebar.collapsed .nav-link.active {
            transform: none !important;
        }
        .sidebar.collapsed .nav {
            margin-top: 80px !important;
        }
        .sidebar:not(.collapsed) .nav {
            margin-top: 80px !important;
        }
        .dropdown-item {
                padding-left: 2.5rem !important;
            }
            .dropdown-menu {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                max-height: 0 !important;
                overflow: hidden !important;
                transition: max-height 0.4s ease-in-out !important;
                display: block !important;
            }
            .dropdown-menu.show {
                max-height: 300px !important;
            }
        .hamburger-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .hamburger-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 250px;
            width: calc(100vw - 250px);
            max-width: calc(100vw - 250px);
            overflow-x: hidden;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .main-content.expanded {
            margin-left: 70px;
            width: calc(100vw - 70px);
            max-width: calc(100vw - 70px);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .card {
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            color: white;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .stat-card-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            border: 2px solid #27ae60;
            box-shadow: 0 0 30px rgba(39, 174, 96, 0.3);
            color: white;
        }
        .stat-card-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border: 2px solid #f39c12;
            box-shadow: 0 0 30px rgba(243, 156, 18, 0.3);
            color: white;
        }
        .stat-card-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: 2px solid #3498db;
            box-shadow: 0 0 30px rgba(52, 152, 219, 0.3);
            color: white;
        }
        .container-fluid {
            background: transparent;
        }
        .table {
            color: white;
        }
        .table-dark {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .btn {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.1);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .breadcrumb {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }
        .breadcrumb-item.active {
            color: white;
        }
        .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        /* Dropdown Menu Styling */
        .dropdown-menu {
            background: transparent !important;
            backdrop-filter: none !important;
            border: none !important;
            border-radius: 8px;
            margin: 0 !important;
            position: static !important;
            transform: none !important;
            display: block !important;
            width: auto;
            min-width: 200px;
            box-shadow: none !important;
            max-height: 0 !important;
            overflow: hidden !important;
            transition: max-height 0.4s ease-in-out !important;
            padding: 0;
        }
        .dropdown.show .dropdown-menu {
            max-height: 300px !important;
        }
        .dropdown {
            position: relative;
            overflow: visible;
        }
        .sidebar {
            overflow-y: auto;
            overflow-x: hidden;
        }
        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 6px;
            margin: 0 !important;
            padding: 8px 12px 8px 2.5rem !important;
            white-space: normal;
            word-wrap: break-word;
            box-sizing: border-box;
            position: relative;
            display: block;
            width: 100%;
        }
        .dropdown-item:hover,
        .dropdown-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }
        .dropdown-toggle::after {
            margin-left: auto;
        }
        @yield('additional-styles')
    </style>
    @yield('head')
</head>
<body>
    <!-- Hamburger Menu Button -->
    <button class="hamburger-btn" id="sidebarToggle">
        <i class="bi bi-list fs-5"></i>
    </button>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="sidebar p-3 collapsed" id="sidebar">
                <nav class="nav flex-column mt-5">
                    <a class="nav-link {{ request()->routeIs('developer.dashboard') ? 'active' : '' }}" href="{{ route('developer.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i><span class="sidebar-text">Dashboard</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('developer.surveys*') ? 'active' : '' }}" href="{{ route('developer.surveys') }}">
                        <i class="bi bi-clipboard-data me-2"></i><span class="sidebar-text">Surveys</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('developer.admins*') ? 'active' : '' }}" href="{{ route('developer.admins') }}">
                        <i class="bi bi-people me-2"></i><span class="sidebar-text">Admins</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('developer.users*') ? 'active' : '' }}" href="{{ route('developer.users') }}">
                        <i class="bi bi-person-check me-2"></i><span class="sidebar-text">Users</span>
                    </a>
                    <!-- User Logs Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('developer.logs*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-journal-text me-2"></i><span class="sidebar-text">User Logs</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-dark">
                            <a class="dropdown-item {{ request()->routeIs('developer.logs.index') ? 'active' : '' }}" href="{{ route('developer.logs.index') }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard Logs
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('developer.logs.login-activity') ? 'active' : '' }}" href="{{ route('developer.logs.login-activity') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login Logs
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('developer.logs.survey-responses') ? 'active' : '' }}" href="{{ route('developer.logs.survey-responses') }}">
                                <i class="bi bi-clipboard-data me-2"></i> Response Logs
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('developer.logs.user-activity') ? 'active' : '' }}" href="{{ route('developer.logs.user-activity') }}">
                                <i class="bi bi-activity me-2"></i> Activity Logs
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('developer.logs.page-visits') ? 'active' : '' }}" href="{{ route('developer.logs.page-visits') }}">
                                <i class="bi bi-eye me-2"></i> Page Visit Logs
                            </a>
                        </div>
                    </div>

                    <hr class="text-white-50">
                    <form action="{{ route('developer.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i><span class="sidebar-text">Logout</span>
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content p-4 expanded" id="mainContent">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Close any open dropdowns when collapsing
                if (sidebar.classList.contains('collapsed')) {
                    const openDropdowns = sidebar.querySelectorAll('.dropdown.show');
                    openDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.remove('show');
                        }
                    });
                }
            });
            
            // Handle dropdown toggle when sidebar is collapsed
            const dropdownToggles = sidebar.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    if (sidebar.classList.contains('collapsed')) {
                        e.preventDefault();
                        e.stopPropagation();
                        // Expand sidebar to show dropdown
                        sidebar.classList.remove('collapsed');
                        mainContent.classList.remove('expanded');
                        // Allow the dropdown to open after sidebar expansion
                        setTimeout(() => {
                            const dropdown = toggle.closest('.dropdown');
                            const bootstrapDropdown = new bootstrap.Dropdown(toggle);
                            bootstrapDropdown.show();
                        }, 300); // Wait for transition to complete
                    }
                });
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>