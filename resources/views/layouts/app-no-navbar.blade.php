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
        @php
            $systemFonts = ['Arial', 'Helvetica', 'Times New Roman', 'Times', 'Courier New', 'Courier', 'Verdana', 'Georgia', 'Palatino', 'Garamond', 'Bookman', 'Comic Sans MS', 'Trebuchet MS', 'Impact'];
            $headingFont = $activeTheme->heading_font;
            $bodyFont = $activeTheme->body_font;
            $isHeadingSystem = in_array($headingFont, $systemFonts);
            $isBodySystem = in_array($bodyFont, $systemFonts);
            
            $fontFamilies = [];
            if (!$isHeadingSystem) {
                $fontFamilies[] = 'family=' . str_replace(' ', '+', $headingFont) . ':wght@400;500;600;700';
            }
            if (!$isBodySystem) {
                $fontFamilies[] = 'family=' . str_replace(' ', '+', $bodyFont) . ':wght@400;500;600';
            }
            $googleFontsUrl = !empty($fontFamilies) ? 'https://fonts.googleapis.com/css2?' . implode('&', $fontFamilies) . '&display=swap' : '';
        @endphp
        @if($googleFontsUrl)
            <link href="{{ $googleFontsUrl }}" rel="stylesheet">
        @endif
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
            color: var(--text-color);
            background-color: var(--background-color);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
            color: var(--text-color);
        }
        
        .btn-primary {
            background-color: var(--btn-primary-bg);
            border-color: var(--btn-primary-bg);
            color: var(--btn-primary-color);
        }
        
        .btn-primary:hover, 
        .btn-primary:focus, 
        .btn-primary:active {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        /* Add any custom CSS from the theme */
        {{ isset($activeTheme) && $activeTheme ? ($activeTheme->custom_css ?? '') : '' }}
    </style>
    @endif
    
    <div id="app">
        <!-- No navbar for developer view -->
        
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Initialize DataTables for tables with the myDataTable ID
          if (document.getElementById('myDataTable')) {
            new DataTable('#myDataTable');
          }
        });

        $(document).ready(function() {
          $('.select2').select2();
        });
    </script>
    
    <!-- Password toggle functionality -->
    <style>
        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
        
        .password-toggle-btn:hover,
        .password-toggle-btn:focus {
            color: #495057;
            outline: none;
        }
        
        .password-field-container {
            position: relative;
        }
        
        .password-field-container .form-control {
            padding-right: 40px;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordFields = document.querySelectorAll('.password-toggle');
            
            passwordFields.forEach(field => {
                // Create container
                const container = document.createElement('div');
                container.className = 'password-field-container';
                field.parentNode.insertBefore(container, field);
                container.appendChild(field);
                
                // Create toggle button
                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'password-toggle-btn';
                toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
                toggleBtn.setAttribute('aria-label', 'Toggle password visibility');
                container.appendChild(toggleBtn);
                
                // Add event listener
                toggleBtn.addEventListener('click', function() {
                    if (field.type === 'password') {
                        field.type = 'text';
                        toggleBtn.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                        field.type = 'password';
                        toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
                    }
                });
            });
        });
    </script>
</body>
</html>