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
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            margin-top: 5px;
            position: static !important;
            transform: none !important;
            display: none;
            width: calc(100% - 16px);
            box-shadow: none;
            border: none;
            background: transparent;
            overflow: hidden;
            max-width: 100%;
            margin-left: 8px;
            margin-right: 8px;
            padding: 4px 0;
        }
        .dropdown.show .dropdown-menu {
            display: block;
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
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 2px 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 4px;
            padding: 8px 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: calc(100% - 16px);
            box-sizing: border-box;
        }
        .dropdown-item:hover,
        .dropdown-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(3px);
        }
        .dropdown-toggle::after {
            margin-left: auto;
        }
        @yield('additional-styles')
    </style>
    @yield('head')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-code-slash fs-3 text-white me-2"></i>
                    <h5 class="text-white mb-0">Developer Portal</h5>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('developer.dashboard') ? 'active' : '' }}" href="{{ route('developer.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('developer.surveys*') ? 'active' : '' }}" href="{{ route('developer.surveys') }}">
                        <i class="bi bi-clipboard-data me-2"></i> Surveys
                    </a>
                    <a class="nav-link {{ request()->routeIs('developer.admins*') ? 'active' : '' }}" href="{{ route('developer.admins') }}">
                        <i class="bi bi-people me-2"></i> Admins
                    </a>
                    <a class="nav-link {{ request()->routeIs('developer.users*') ? 'active' : '' }}" href="{{ route('developer.users') }}">
                        <i class="bi bi-person-check me-2"></i> Users
                    </a>
                    <!-- User Logs Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('developer.logs*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-journal-text me-2"></i> User Logs
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item {{ request()->routeIs('developer.logs.index') ? 'active' : '' }}" href="{{ route('developer.logs.index') }}">
                                <i class="bi bi-speedometer2 me-2"></i> User Logs Dashboard
                            </a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('developer.logs.login-activity') ? 'active' : '' }}" href="{{ route('developer.logs.login-activity') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login Logs
                            </a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('developer.logs.survey-responses') ? 'active' : '' }}" href="{{ route('developer.logs.survey-responses') }}">
                                <i class="bi bi-clipboard-data me-2"></i> Response Logs
                            </a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('developer.logs.user-activity') ? 'active' : '' }}" href="{{ route('developer.logs.user-activity') }}">
                                <i class="bi bi-activity me-2"></i> User Activity Logs
                            </a></li>
                        </ul>
                    </div>

                    <hr class="text-white-50">
                    <form action="{{ route('developer.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>