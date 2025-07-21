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
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS loaded synchronously for critical styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.0/css/dataTables.dataTables.min.css">
    
    
    <!-- Theme Fonts -->
    @if(isset($activeTheme) && $activeTheme)
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $activeTheme->heading_font) }}:wght@400;500;600;700&family={{ str_replace(' ', '+', $activeTheme->body_font) }}:wght@400;500;600&display=swap" rel="stylesheet">
    @endif

    <!-- Critical CSS to prevent navbar dropdown glitch with loading overlay -->
    <style>
        /* Loading overlay to hide content until fully styled */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease-out;
        }
        
        #loading-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Hide navbar initially */
        .navbar {
            visibility: hidden;
            opacity: 0;
        }
        
        /* Show navbar only when ready */
        .navbar.ready {
            visibility: visible;
            opacity: 1;
            transition: opacity 0.2s ease-in;
        }
        
        /* Ensure proper navbar structure when shown */
        .navbar.ready {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        /* Critical dropdown positioning */
        .navbar.ready .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            left: auto;
            z-index: 1000;
        }
        
        .navbar.ready .dropdown-menu-end {
            right: 0;
            left: auto;
        }
        
        /* Ensure Bootstrap classes work correctly when ready */
        .navbar.ready .ms-auto {
            margin-left: auto !important;
        }
        
        .navbar.ready .me-auto {
            margin-right: auto !important;
        }
        
        /* Hide main content until ready */
        main {
            visibility: hidden;
            opacity: 0;
        }
        
        main.ready {
            visibility: visible;
            opacity: 1;
            transition: opacity 0.2s ease-in 0.1s;
        }
        
        /* Final safety: lock dropdown positioning once ready */
        .navbar.ready .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            transform: none !important;
            will-change: auto !important;
        }
        
        .navbar.ready .dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }
    </style>
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body style="background-color: var(--background-color)">
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
    @endif
    
    <!-- Loading overlay -->
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="{{ route('index') }}">
                    @php
                        $activeLogo = \App\Models\Logo::where('is_active', true)->first();
                    @endphp
                    @if($activeLogo)
                        <img src="{{ asset('storage/' . $activeLogo->file_path) }}" alt="Logo" class="logo" style="min-width: 50px; max-width: 150px; min-height: 30px; max-height: 35px; object-fit: contain;">
                    @else
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" style="min-width: 50px; max-width: 150px; min-height: 30px; max-height: 35px; object-fit: contain;">
                    @endif
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
                  <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileNavLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-body">
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdownMobile" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMobile">
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ session('is_admin') ? route('admin.logout') : route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form-mobile" action="{{ session('is_admin') ? route('admin.logout') : route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                  </div>
                </div>
                
                <div class="collapse navbar-collapse d-none d-md-block" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
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
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ session('is_admin') ? route('admin.logout') : route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form-user-desktop').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form-user-desktop" action="{{ session('is_admin') ? route('admin.logout') : route('logout') }}" method="POST" class="d-none">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Comprehensive loading solution to prevent navbar glitch -->
    <script>
        (function() {
            let bootstrapReady = false;
            let contentReady = false;
            
            function hideLoadingOverlay() {
                if (bootstrapReady && contentReady) {
                    const overlay = document.getElementById('loading-overlay');
                    const navbar = document.querySelector('.navbar');
                    const main = document.querySelector('main');
                    
                    if (overlay) {
                        overlay.classList.add('hidden');
                        setTimeout(() => {
                            overlay.style.display = 'none';
                        }, 300);
                    }
                    
                    if (navbar) {
                        navbar.classList.add('ready');
                    }
                    
                    if (main) {
                        main.classList.add('ready');
                    }
                }
            }
            
            function checkBootstrapReady() {
                try {
                    // Test if Bootstrap CSS is properly loaded
                    const testElement = document.createElement('div');
                    testElement.className = 'dropdown-menu position-absolute';
                    testElement.style.visibility = 'hidden';
                    testElement.style.position = 'absolute';
                    document.body.appendChild(testElement);
                    
                    const computedStyle = window.getComputedStyle(testElement);
                    const hasBootstrapStyles = computedStyle.position === 'absolute' && 
                                             parseFloat(computedStyle.zIndex) >= 1000;
                    
                    document.body.removeChild(testElement);
                    
                    if (hasBootstrapStyles && typeof bootstrap !== 'undefined') {
                        bootstrapReady = true;
                        hideLoadingOverlay();
                    } else {
                        setTimeout(checkBootstrapReady, 10);
                    }
                } catch (e) {
                    // Fallback if test fails
                    setTimeout(() => {
                        bootstrapReady = true;
                        hideLoadingOverlay();
                    }, 100);
                }
            }
            
            function initializeContent() {
                contentReady = true;
                hideLoadingOverlay();
            }
            
            // Start checking when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    checkBootstrapReady();
                    initializeContent();
                });
            } else {
                checkBootstrapReady();
                initializeContent();
            }
            
            // Safety fallback: show content after 1 second even if checks fail
            setTimeout(() => {
                bootstrapReady = true;
                contentReady = true;
                hideLoadingOverlay();
            }, 1000);
        })();
    </script>
    <style>
      /* Additional navbar styles after Bootstrap loads */
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
      
      /* Ensure proper responsive behavior */
      @media (max-width: 767.98px) {
        .navbar-collapse.d-none.d-md-block { 
          display: none !important; 
        }
        .offcanvas { 
          width: 280px; 
          max-width: 80vw; 
        }
      }
      
      @media (min-width: 768px) {
        .offcanvas { 
          display: none !important; 
        }
        .navbar-collapse.d-none.d-md-block { 
          display: flex !important; 
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
