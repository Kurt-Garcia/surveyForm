<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#667eea">
    <meta name="color-scheme" content="light">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FDC Feedback Form</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    
    <!-- Theme Fonts -->
    @if(isset($activeTheme) && $activeTheme)
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $activeTheme->heading_font) }}:wght@400;500;600;700&family={{ str_replace(' ', '+', $activeTheme->body_font) }}:wght@400;500;600&display=swap" rel="stylesheet">
    @endif

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      <!-- DataTables JS -->
      <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
      <!-- DataTables Buttons JS -->
      <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
      <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Additional head content from child views -->
    @stack('head')
</head>
<body>
    <!-- Theme CSS Variables -->
    @if(isset($activeTheme) && $activeTheme)
    <style>
        :root {
            --primary-color: {{ $activeTheme->primary_color }};
            --secondary-color: {{ $activeTheme->secondary_color }};
            --accent-color: {{ $activeTheme->accent_color }};
            --background-color: {{ $activeTheme->background_color }};
            --text-color: {{ $activeTheme->text_color }};
            --heading-font: '{{ $activeTheme->heading_font }}', sans-serif;
            --body-font: '{{ $activeTheme->body_font }}', sans-serif;
            
            @php
                // Helper function to convert hex to RGB
                $hexToRgb = function($hex) {
                    $hex = ltrim($hex, '#');
                    if (strlen($hex) == 3) {
                        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                    }
                     $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                    return "$r, $g, $b";
                };
            @endphp
            
            /* RGB versions for rgba() usage */
            --primary-color-rgb: {{ $hexToRgb($activeTheme->primary_color) }};
            --secondary-color-rgb: {{ $hexToRgb($activeTheme->secondary_color) }};
            --accent-color-rgb: {{ $hexToRgb($activeTheme->accent_color) }};
            --background-color-rgb: {{ $hexToRgb($activeTheme->background_color) }};
            --text-color-rgb: {{ $hexToRgb($activeTheme->text_color) }};
            
            /* Derived variables */
            --primary-gradient: linear-gradient(135deg, {{ $activeTheme->primary_color }}, {{ $activeTheme->secondary_color }});
            --card-bg-color: #ffffff;
            --border-color: rgba(0, 0, 0, 0.125);
            --input-bg: #ffffff;
            --input-border: #ced4da;
            --input-focus-border: {{ $activeTheme->primary_color }};
            --btn-primary-bg: {{ $activeTheme->primary_color }};
            --btn-primary-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.08);
        }
        
        body {
            font-family: var(--body-font);
            background-color: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
        }
        
        .btn-primary {
            background-color: var(--btn-primary-bg);
            border-color: var(--btn-primary-bg);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .border-primary {
            border-color: var(--primary-color) !important;
        }
        
        {{ isset($activeTheme) && $activeTheme ? ($activeTheme->custom_css ?? '') : '' }}
    </style>
    @else
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --accent-color: #28a745;
            --background-color: #f8f9fa;
            --text-color: #212529;
            --heading-font: 'Nunito', sans-serif;
            --body-font: 'Nunito', sans-serif;
            --primary-color-rgb: 0, 123, 255;
            --secondary-color-rgb: 108, 117, 125;
            --accent-color-rgb: 40, 167, 69;
            --background-color-rgb: 248, 249, 250;
            --text-color-rgb: 33, 37, 41;
        }
        
        body {
            font-family: var(--body-font);
            background-color: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
        }
    </style>
    @endif
    
    <!-- Sidebar Styles -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: rgba(var(--primary-color-rgb), 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(var(--primary-color-rgb), 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
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
        
        .logo-container {
            position: fixed;
            top: 0px;
            left: 90px;
            transition: all 0.3s ease;
            margin: 0;
            padding: 0;
        }
        
        .logo-container a {
            margin: 0;
            padding: 0;
        }
        
        .logo-container img {
            margin: 0;
            padding: 0;
        }
        
        .sidebar.collapsed .logo-container {
            display: none;
        }
        
        .sidebar .nav {
            margin-top: 100px;
        }
        
        .hamburger-btn {
            position: fixed;
            top: 30px;
            left: 15px;
            z-index: 1001;
            background: rgba(var(--primary-color-rgb), 0.1);
            border: 1px solid rgba(var(--primary-color-rgb), 0.2);
            color: var(--primary-color);
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .hamburger-btn:hover {
            background: rgba(var(--primary-color-rgb), 0.2);
            color: var(--primary-color);
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100vw - 250px);
            max-width: calc(100vw - 250px);
            overflow-x: hidden;
            transition: all 0.3s ease;
            box-sizing: border-box;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 70px;
            width: calc(100vw - 70px);
            max-width: calc(100vw - 70px);
        }
        
        .sidebar .nav-link {
            color: var(--text-color);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 12px 16px;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(var(--primary-color-rgb), 0.15);
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(var(--primary-color-rgb), 0.2);
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            padding: 20px 16px;
            margin-bottom: 10px;
        }
        
        .logo-container .logo {
            transition: all 0.3s ease;
        }
        
        .brand-text {
            margin-left: 12px;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .dropdown-menu {
            background: transparent !important;
            border: none !important;
            border-radius: 0;
            margin: 0 !important;
            width: auto;
            min-width: 200px;
            box-shadow: none !important;
            padding: 8px 0;
        }
        
        .dropdown-item {
            color: var(--text-color);
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 2px 8px;
            padding: 8px 12px 8px 2.5rem !important;
            display: flex;
            align-items: center;
        }
        
        .dropdown-item:hover,
        .dropdown-item.active {
            background-color: rgba(var(--primary-color-rgb), 0.15);
            color: var(--primary-color);
        }
        
        .dropdown-item i {
            margin-right: 8px;
            width: 16px;
            text-align: center;
        }
        
        .user-dropdown {
            margin-top: auto;
            border-top: 1px solid rgba(var(--primary-color-rgb), 0.1);
            padding-top: 10px;
        }
        
        /* Mobile First Responsive Design */
        @media (max-width: 576px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 1050;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100vw;
                max-width: 100vw;
                padding: 0 10px;
            }
            
            .hamburger-btn {
                background: var(--primary-color);
                color: white;
                top: 15px;
                left: 10px;
                padding: 6px 10px;
                font-size: 0.9rem;
                z-index: 1051;
            }
            
            .logo-container {
                padding: 15px 12px;
            }
            
            .logo-container img {
                max-width: 60px;
                max-height: 45px;
            }
            
            .card {
                margin: 10px 0;
                border-radius: 10px;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .breadcrumb {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
        
        @media (min-width: 577px) and (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100vw;
                max-width: 100vw;
                padding: 0 15px;
            }
            
            .hamburger-btn {
                background: var(--primary-color);
                color: white;
                top: 20px;
                left: 15px;
                z-index: 1051;
            }
        }
        
        @media (min-width: 769px) and (max-width: 992px) {
            .sidebar {
                width: 220px;
            }
            
            .main-content {
                margin-left: 220px;
                width: calc(100vw - 220px);
                max-width: calc(100vw - 220px);
            }
            
            .main-content.expanded {
                margin-left: 70px;
                width: calc(100vw - 70px);
                max-width: calc(100vw - 70px);
            }
            
            .logo-container {
                padding: 18px 14px;
            }
        }
        
        @media (min-width: 993px) and (max-width: 1200px) {
            .sidebar {
                width: 240px;
            }
            
            .main-content {
                margin-left: 240px;
                width: calc(100vw - 240px);
                max-width: calc(100vw - 240px);
            }
            
            .main-content.expanded {
                margin-left: 70px;
                width: calc(100vw - 70px);
                max-width: calc(100vw - 70px);
            }
        }
        
        @media (min-width: 1201px) {
             .sidebar {
                 width: 250px;
             }
             
             .main-content {
                 margin-left: 250px;
                 width: calc(100vw - 250px);
                 max-width: calc(100vw - 250px);
             }
             
             .main-content.expanded {
                 margin-left: 70px;
                 width: calc(100vw - 70px);
                 max-width: calc(100vw - 70px);
             }
         }
         
         @media (min-width: 1400px) {
             .sidebar {
                 width: 280px;
             }
             
             .main-content {
                 margin-left: 280px;
                 width: calc(100vw - 280px);
                 max-width: calc(100vw - 280px);
             }
             
             .main-content.expanded {
                 margin-left: 70px;
                 width: calc(100vw - 70px);
                 max-width: calc(100vw - 70px);
             }
             
             .logo-container {
                 padding: 20px 18px;
             }
             
             .logo-container img {
                 max-width: 100px;
                 max-height: 70px;
             }
         }
         
         /* Mobile Overlay */
         .mobile-overlay {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0, 0, 0, 0.5);
             z-index: 999;
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
             backdrop-filter: blur(2px);
         }
         
         .mobile-overlay.show {
             opacity: 1;
             visibility: visible;
         }
         
         @media (min-width: 769px) {
             .mobile-overlay {
                 display: none;
             }
         }
    </style>
    
    <!-- Hamburger Menu Button -->
    <button class="hamburger-btn" id="sidebarToggle">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <div id="app" class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="sidebar p-3" id="sidebar">
                <!-- Logo Section -->
                <div class="logo-container">
                    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center justify-content-center text-decoration-none">
                        @php
                            $activeLogo = \App\Models\Logo::where('is_active', true)->first();
                        @endphp
                        @if($activeLogo)
                            <img src="{{ asset('storage/' . $activeLogo->file_path) }}" alt="Logo" class="logo" style="min-width: 60px; max-width: 90px; min-height: 50px; max-height: 60px; object-fit: contain;">
                        @else
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" style="min-width: 60px; max-width: 70px; min-height: 50px; max-height: 60px; object-fit: contain;">
                        @endif
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i><span class="sidebar-text">Dashboard</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.surveys.*') ? 'active' : '' }}" href="{{ route('admin.surveys.index') }}">
                        <i class="bi bi-clipboard-data"></i><span class="sidebar-text">Surveys</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.admins.*') || request()->routeIs('admin.check-*') ? 'active' : '' }}" href="{{ route('admin.admins.create') }}">
                        <i class="bi bi-people"></i><span class="sidebar-text">Admins</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.sites.*') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                        <i class="bi bi-person-check"></i><span class="sidebar-text">Surveyors</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                        <i class="bi bi-person-lines-fill"></i><span class="sidebar-text">Customers</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}" href="{{ route('admin.themes.index') }}">
                        <i class="bi bi-palette"></i><span class="sidebar-text">Themes</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.logos.*') ? 'active' : '' }}" href="{{ route('admin.logos.index') }}">
                        <i class="bi bi-image"></i><span class="sidebar-text">Logo</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.translations.*') ? 'active' : '' }}" href="{{ route('admin.translations.index') }}">
                        <i class="bi bi-translate"></i><span class="sidebar-text">Translations</span>
                    </a>
                    
                    @auth
                    <!-- User Section -->
                    <div class="user-dropdown mt-auto">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i><span class="sidebar-text">{{ Auth::user()->name }}</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person"></i>{{ __('Profile') }}
                                </a>
                                <a class="dropdown-item" href="{{ session('is_admin') ? route('admin.logout') : route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i>{{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ session('is_admin') ? route('admin.logout') : route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                    
                    @guest
                        @if (Route::has('login'))
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i><span class="sidebar-text">{{ __('Login') }}</span>
                        </a>
                        @endif
                    @endguest
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content p-0" id="mainContent">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery first, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
      <!-- DataTables JS -->
      <script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>
        <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
     <!-- DataTables Export Buttons -->
     <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
     <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Stack for page-specific scripts -->
    @stack('scripts')

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

     <!-- DATATABLES SCRIPTS -->
     <script>
        document.addEventListener('DOMContentLoaded', function () {
          // This will be used for dynamically created tables with this ID
          if (document.getElementById('myDataTable')) {
            new DataTable('#myDataTable');
          }
          
          // Only initialize tables that don't have page-specific initialization
          // Removed customersTable from here since it has custom initialization in its own page
        });
    </script>
      
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            // Sidebar toggle is now handled in handleResponsiveSidebar function
            
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
                            // Trigger click event to open dropdown
                            toggle.click();
                        }, 350); // Wait for transition to complete
                    }
                    // When sidebar is not collapsed, let Bootstrap handle the dropdown normally
                });
            });
            
            // Enhanced mobile and responsive sidebar handling
             function handleResponsiveSidebar() {
                 const isMobile = window.innerWidth <= 768;
                 const isTablet = window.innerWidth > 768 && window.innerWidth <= 992;
                 
                 if (isMobile) {
                     // Mobile behavior - sidebar should be hidden by default
                     sidebar.classList.remove('collapsed');
                     sidebar.classList.remove('show'); // Ensure it starts hidden
                     mainContent.classList.remove('expanded');
                     mobileOverlay.classList.remove('show');
                     
                     // Override the toggle behavior for mobile
                      sidebarToggle.onclick = function() {
                          sidebar.classList.toggle('show');
                          mobileOverlay.classList.toggle('show');
                      };
                 } else {
                     // Desktop/tablet behavior - sidebar should be visible and collapsible
                     sidebar.classList.remove('show');
                     mobileOverlay.classList.remove('show');
                     
                     // Set initial desktop state
                     if (!sidebar.classList.contains('collapsed')) {
                         sidebar.classList.add('collapsed');
                         mainContent.classList.add('expanded');
                     }
                     
                     // Restore normal toggle behavior
                     sidebarToggle.onclick = function() {
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
                     };
                 }
             }
            
            // Initial setup
            handleResponsiveSidebar();
            
            // Handle window resize
            window.addEventListener('resize', function() {
                handleResponsiveSidebar();
            });
            
            // Close sidebar when clicking outside on mobile
             document.addEventListener('click', function(e) {
                 if (window.innerWidth <= 768 && 
                     !sidebar.contains(e.target) && 
                     !sidebarToggle.contains(e.target) && 
                     sidebar.classList.contains('show')) {
                     sidebar.classList.remove('show');
                     mobileOverlay.classList.remove('show');
                 }
             });
             
             // Close sidebar when clicking on mobile overlay
             mobileOverlay.addEventListener('click', function() {
                 if (window.innerWidth <= 768) {
                     sidebar.classList.remove('show');
                     mobileOverlay.classList.remove('show');
                 }
             });
            
            // Handle touch events for mobile swipe
            let touchStartX = 0;
            let touchEndX = 0;
            
            document.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            });
            
            document.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });
            
            function handleSwipe() {
                if (window.innerWidth <= 768) {
                    const swipeDistance = touchEndX - touchStartX;
                    const minSwipeDistance = 100;
                    
                    // Swipe right to open sidebar
                     if (swipeDistance > minSwipeDistance && touchStartX < 50) {
                         sidebar.classList.add('show');
                         mobileOverlay.classList.add('show');
                     }
                     // Swipe left to close sidebar
                     else if (swipeDistance < -minSwipeDistance && sidebar.classList.contains('show')) {
                         sidebar.classList.remove('show');
                         mobileOverlay.classList.remove('show');
                     }
                }
            }
        });

        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <style>
      /* Card and form styling for sidebar layout */
      .card {
        border: none;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(var(--primary-color-rgb), 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
      }
      
      .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
      }
      
      .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
      }
      
      .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      }
      
      .form-control {
        border-radius: 8px;
        border: 1px solid rgba(var(--primary-color-rgb), 0.2);
        transition: all 0.3s ease;
      }
      
      .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
      }
      
      .table {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px;
        overflow: hidden;
      }
      
      .breadcrumb {
        background: rgba(var(--primary-color-rgb), 0.05);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
      }
      
      .breadcrumb-item a {
        color: var(--primary-color);
        text-decoration: none;
      }
      
      .breadcrumb-item.active {
        color: var(--text-color);
      }
      
      /* Alert styling */
      .alert {
        border-radius: 10px;
        border: none;
        backdrop-filter: blur(10px);
        margin-bottom: 1rem;
      }
      
      /* Badge styling */
      .badge {
        border-radius: 6px;
      }
      
      /* Responsive table improvements */
      .table-responsive {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      }
      
      /* Responsive form improvements */
      @media (max-width: 576px) {
        .card {
          border-radius: 10px;
          margin-bottom: 1rem;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
          padding: 0.75rem 1rem;
          font-size: 1rem;
        }
        
        .card-body {
          padding: 1rem;
        }
        
        .btn {
          padding: 0.5rem 1rem;
          font-size: 0.9rem;
          margin-bottom: 0.5rem;
          width: 100%;
        }
        
        .btn-group {
          flex-direction: column;
          width: 100%;
        }
        
        .btn-group .btn {
          border-radius: 8px !important;
          margin-bottom: 0.25rem;
        }
        
        .form-control {
          font-size: 16px; /* Prevents zoom on iOS */
        }
        
        .input-group {
          flex-direction: column;
        }
        
        .input-group .form-control {
          margin-bottom: 0.5rem;
        }
        
        .table {
          font-size: 0.85rem;
        }
        
        .table th,
        .table td {
          padding: 0.5rem 0.25rem;
          white-space: nowrap;
        }
        
        .breadcrumb {
          padding: 0.5rem 0.75rem;
          font-size: 0.85rem;
        }
        
        .breadcrumb-item {
          font-size: 0.85rem;
        }
      }
      
      @media (min-width: 577px) and (max-width: 768px) {
        .card {
          margin-bottom: 1.25rem;
        }
        
        .btn-group .btn {
          font-size: 0.9rem;
        }
        
        .table {
          font-size: 0.9rem;
        }
      }
      
      @media (min-width: 769px) and (max-width: 992px) {
        .btn-group .btn {
          padding: 0.5rem 0.75rem;
        }
      }
      
      /* Container and layout improvements */
      .container-fluid {
        padding: 0;
      }
      
      .main-content {
        padding: 1rem;
        min-height: 100vh;
        overflow-x: auto;
      }
      
      @media (max-width: 576px) {
        .main-content {
          padding: 0.5rem;
        }
        
        .row {
          margin: 0 -0.5rem;
        }
        
        .col, [class*="col-"] {
          padding: 0 0.5rem;
        }
      }

      /* Password Toggle Button Styling - Global */
      .password-input-group {
        position: relative;
      }

      .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
      }

      .password-toggle-btn:hover {
        color: var(--primary-color, #007bff);
        background-color: rgba(0, 123, 255, 0.1);
      }

      .password-toggle-btn:focus {
        outline: none;
        color: var(--primary-color, #007bff);
        background-color: rgba(0, 123, 255, 0.15);
      }

      .password-toggle-btn:active {
        transform: translateY(-50%) scale(0.95);
      }

      /* Adjust padding for password inputs to accommodate toggle button */
      .password-input-group .form-control {
        padding-right: 50px !important;
      }

      /* Handle input-group with icons and password toggle */
      .input-group .password-input-group {
        position: relative;
        flex: 1;
      }

      .input-group .password-input-group .password-toggle-btn {
        right: 12px;
      }

      .input-group .password-input-group .form-control {
        padding-right: 50px !important;
      }
    </style>

    <script>
      // Global Password Toggle Functionality
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize password toggles
        initializePasswordToggles();
        
        // Re-initialize when new content is loaded dynamically
        if (typeof window.reinitializePasswordToggles === 'undefined') {
          window.reinitializePasswordToggles = initializePasswordToggles;
        }
      });

      function initializePasswordToggles() {
        document.querySelectorAll('.password-toggle-btn').forEach(button => {
          // Remove existing event listeners to prevent duplicates
          button.replaceWith(button.cloneNode(true));
        });

        // Add event listeners to all password toggle buttons
        document.querySelectorAll('.password-toggle-btn').forEach(button => {
          button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const eyeIcon = this.querySelector('i');
            
            if (targetInput) {
              if (targetInput.type === 'password') {
                targetInput.type = 'text';
                eyeIcon.className = 'bi bi-eye-slash';
              } else {
                targetInput.type = 'password';
                eyeIcon.className = 'bi bi-eye';
              }
            }
          });
        });
      }
    </script>
    
    @yield('scripts')
</body>
</html>
