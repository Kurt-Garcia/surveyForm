<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ isset($survey) ? $survey->title : 'Survey' }}</title>
    
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        .survey-wrapper {
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .survey-container {
        max-width: 1200px;
        margin: 0 auto;
        background: var(--card-background);
        border-radius: var(--border-radius);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 2rem;
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
          color: #007bff;
          background-color: rgba(0, 123, 255, 0.1);
        }

        .password-toggle-btn:focus {
          outline: none;
          color: #007bff;
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
</head>
<body>
    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    
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