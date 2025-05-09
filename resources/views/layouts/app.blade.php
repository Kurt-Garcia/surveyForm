<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FDC Feedback Form</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    
    <!-- Theme Fonts -->
    @if($activeTheme)
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $activeTheme->heading_font) }}:wght@400;500;600;700&family={{ str_replace(' ', '+', $activeTheme->body_font) }}:wght@400;500;600&display=swap" rel="stylesheet">
    @endif

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <!-- Theme CSS Variables -->
    @if($activeTheme)
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
        
        {{ $activeTheme->custom_css ?? '' }}
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
                        <img src="{{ asset('storage/' . $activeLogo->file_path) }}" alt="Logo" class="logo" style="max-width: 100px;">
                    @else
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" style="max-width: 100px;">
                    @endif
                </a>
                <button class="navbar-toggler" type="button" aria-label="Toggle navigation" id="offcanvasToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileOffcanvas" aria-labelledby="mobileOffcanvasLabel">
                  <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileOffcanvasLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.surveys.index') }}">Surveys</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
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
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                  </div>
                </div>
                <div class="collapse navbar-collapse d-none d-md-block" id="navbarSupportedContent">
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
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
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
        var offcanvasToggle = document.getElementById('offcanvasToggle');
        var offcanvasEl = document.getElementById('mobileOffcanvas');
        if (offcanvasToggle && offcanvasEl) {
          offcanvasToggle.addEventListener('click', function() {
            var bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
            bsOffcanvas.toggle();
          });
        }
      });
    </script>
    <style>
      @media (max-width: 767.98px) {
        .navbar-collapse.d-none.d-md-block { display: none !important; }
        .offcanvas { width: 25vw; min-width: 120px; max-width: 50vw; }
      }
      @media (min-width: 768px) {
        .offcanvas { display: none !important; }
        .navbar-collapse.d-none.d-md-block { display: flex !important; }
      }
    </style>
</body>
</html>
