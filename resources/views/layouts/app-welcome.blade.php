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

    <!-- Theme Fonts -->
    @if(isset($activeTheme) && $activeTheme)
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $activeTheme->heading_font) }}:wght@400;500;600;700&family={{ str_replace(' ', '+', $activeTheme->body_font) }}:wght@400;500;600&display=swap" rel="stylesheet">
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
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
        
        /* Base styles */
        body {
            font-family: var(--body-font) !important;
            background-color: var(--background-color) !important;
            color: var(--text-color) !important;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font) !important;
            color: var(--text-color) !important;
        }
        
        /* Button styles */
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .btn-primary:hover, 
        .btn-primary:focus, 
        .btn-primary:active {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }
        
        /* Do not modify navbar colors - keep it white instead of theme colors */
        
        /* Add any custom CSS from the theme */
        {{ isset($activeTheme) && $activeTheme ? ($activeTheme->custom_css ?? '') : '' }}
    </style>
    @endif
    
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="/">
                    @php
                        $activeLogo = \App\Models\Logo::where('is_active', true)->first();
                    @endphp
                    @if($activeLogo)
                        <img src="{{ asset('storage/' . $activeLogo->file_path) }}" alt="Logo" class="logo" style="min-width: 50px; max-width: 150px; min-height: 30px; max-height: 35px; object-fit: contain;">
                    @else
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" style="min-width: 50px; max-width: 150px; min-height: 30px; max-height: 35px; object-fit: contain;">
                    @endif
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form-welcome').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form-welcome" action="{{ route('logout') }}" method="POST" class="d-none">
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
    
    <style>
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
</body>
</html>
