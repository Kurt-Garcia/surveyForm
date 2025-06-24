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
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
        }
        .nav-link {
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
    @endif
    
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    @php
                        $activeLogo = \App\Models\Logo::where('is_active', true)->first();
                    @endphp
                    @if($activeLogo)
                        <img src="{{ asset('storage/' . $activeLogo->file_path) }}" alt="Logo" class="logo" style="min-width: 50px; max-width: 150px; min-height: 30px; max-height: 35px; object-fit: contain;">
                    @else
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" style="min-width: 50px; max-width: 150px; min-height: 30px; max-height: 35px; object-fit: contain;">
                    @endif
                </a>
                <button class="navbar-toggler" type="button" aria-label="Toggle navigation" id="offcanvasToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileOffcanvas" aria-labelledby="mobileOffcanvasLabel">
                  <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileOffcanvasLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.surveys.index') }}">Surveys</a>
                        </li>
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('password.change') }}">
                                    {{ __('Change Password') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                  </div>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.surveys.index') }}">Surveys</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('password.change') }}">
                                        {{ __('Change Password') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-0">
            @yield('content')
        </main>
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
      
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Ensure proper navbar layout on page load
        const navbar = document.querySelector('.navbar');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        if (window.innerWidth >= 768) {
          if (navbarCollapse) {
            navbarCollapse.style.display = 'flex';
            navbarCollapse.style.justifyContent = 'space-between';
          }
        }
        
        // Initialize offcanvas for mobile only
        var offcanvasToggle = document.getElementById('offcanvasToggle');
        var offcanvasEl = document.getElementById('mobileOffcanvas');
        if (offcanvasToggle && offcanvasEl) {
          offcanvasToggle.addEventListener('click', function() {
            var bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
            bsOffcanvas.toggle();
          });
        }
        
        // Handle window resize to maintain proper layout
        window.addEventListener('resize', function() {
          const navbarCollapse = document.querySelector('.navbar-collapse');
          if (window.innerWidth >= 768 && navbarCollapse) {
            navbarCollapse.style.display = 'flex';
            navbarCollapse.style.justifyContent = 'space-between';
          }
        });
      });

      $(document).ready(function() {
        $('.select2').select2();
      });
    </script>
    <style>
        .nav-link, .dropdown-item {
        font-family: var(--body-font);
        color: var(--text-color);
      }
      
      .nav-link:hover, .dropdown-item:hover {
        color: var(--primary-color);
      }
      
      .dropdown-menu {
        border-color: var(--border-color);
        box-shadow: 0 2px 4px var(--shadow-color);
      }
      
      /* Fix navbar glitch caused by mobile/desktop interference */
      .navbar {
        position: relative;
      }
      
      .navbar-collapse {
        justify-content: space-between;
      }
      
      .navbar-nav.me-auto {
        margin-right: auto;
      }
      
      .navbar-nav.ms-auto {
        margin-left: auto;
      }
      
      /* Ensure mobile offcanvas doesn't affect desktop layout */
      .offcanvas {
        position: fixed;
        top: 0;
        bottom: 0;
        z-index: 1045;
        width: 280px;
      }
      
      /* Proper responsive navbar handling */
      @media (max-width: 767.98px) {
        .navbar-nav {
          text-align: center;
        }
        .offcanvas { 
          width: 280px; 
          max-width: 80vw; 
        }
        
        /* Hide desktop navbar on mobile */
        .navbar-collapse {
          display: none !important;
        }
      }
      
      @media (min-width: 768px) {
        .navbar-toggler { 
          display: none !important; 
        }
        .navbar-collapse { 
          display: flex !important; 
          justify-content: space-between !important;
        }
        .offcanvas { 
          display: none !important; 
        }
        
        /* Ensure desktop navbar stays properly aligned */
        .navbar-expand-md .navbar-collapse {
          flex-basis: auto;
          flex-grow: 1;
        }
        
        .navbar-expand-md .navbar-nav {
          flex-direction: row;
        }
        
        .navbar-expand-md .navbar-nav.me-auto {
          margin-right: auto !important;
        }
        
        .navbar-expand-md .navbar-nav.ms-auto {
          margin-left: auto !important;
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
