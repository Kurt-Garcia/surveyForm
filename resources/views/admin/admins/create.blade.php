@extends('layouts.app')

@section('content')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4 px-4" style="background: var(--background-color); min-height: 100vh;">
    <!-- Hero Section with Gradient Background -->

    <div class="row justify-content-center">
        <!-- Create New Admin Form - Centered -->
        <div class="col-lg-6 col-xl-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add New Administrator
                            </h4>
                            <p class="mb-0 opacity-90 small mt-1">Create a new admin account with full privileges</p>
                        </div>
                        <a href="javascript:void(0)" onclick="confirmClose()" class="btn btn-light btn-sm rounded-pill" data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <ul class="mb-0 ms-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.admins.store') }}" method="POST" id="adminForm" onsubmit="return confirmSubmit(event)">
                        @csrf
                        
                        <!-- SBU Selection -->
                        <div class="mb-4">
                            <label for="sbu_id" class="form-label fw-semibold text-dark">
                                <i class="bi bi-building me-1 text-primary"></i>Strategic Business Unit
                            </label>
                            <select id="sbu_id" class="form-select form-select-lg border-0 shadow-sm @error('sbu_id') is-invalid @enderror" name="sbu_id" required>
                                <option value="" selected disabled>Choose SBU...</option>
                                @foreach($sbus as $sbu)
                                    <option value="{{ $sbu->id }}" {{ old('sbu_id') == $sbu->id ? 'selected' : '' }}>{{ $sbu->name }}</option>
                                @endforeach
                            </select>
                            @error('sbu_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Site Selection -->
                        <div class="mb-4">
                            <label for="site_id" class="form-label fw-semibold text-dark">
                                <i class="bi bi-geo-alt me-1 text-success"></i>Site Location
                            </label>
                            <select id="site_id" class="form-select form-select-lg border-0 shadow-sm @error('site_id') is-invalid @enderror" name="site_id" required>
                                <option value="" selected disabled>Select SBU first...</option>
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Personal Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-person me-1 text-info"></i>Full Name
                                </label>
                                <input type="text" class="form-control form-control-lg border-0 shadow-sm @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name..." 
                                       autocomplete="name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <label for="email" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-envelope me-1 text-warning"></i>Email Address
                                </label>
                                <input type="email" class="form-control form-control-lg border-0 shadow-sm @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" 
                                       autocomplete="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="contact_number" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-telephone me-1 text-danger"></i>Contact Number
                                </label>
                                <input type="tel" class="form-control form-control-lg border-0 shadow-sm @error('contact_number') is-invalid @enderror" 
                                       id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="+63912345678" 
                                       autocomplete="tel" required>
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Password Fields -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-shield-lock me-1 text-secondary"></i>Password
                                </label>
                                <input type="password" class="form-control form-control-lg border-0 shadow-sm @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Enter password..." 
                                       autocomplete="new-password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-shield-check me-1 text-secondary"></i>Confirm Password
                                </label>
                                <input type="password" class="form-control form-control-lg border-0 shadow-sm" 
                                       id="password_confirmation" name="password_confirmation" placeholder="Confirm password..." 
                                       autocomplete="new-password" required>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3">
                                <i class="bi bi-person-gear me-2"></i>Create Administrator Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern Card Styling with Gradient */
    .bg-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    }
    
    .card {
        border: none !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }

    /* Form Styling */
    .form-control, .form-select {
        border-radius: 12px !important;
        padding: 12px 16px !important;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--accent-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.15) !important;
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600 !important;
        color: var(--text-color) !important;
        margin-bottom: 8px !important;
    }

    /* Button Styling */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        border: none !important;
        font-weight: 600 !important;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(var(--primary-color-rgb), 0.3) !important;
    }

    /* Alert Styling */
    .alert {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }

    /* Validation Styling */
    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .is-valid {
        border-color: #28a745 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.98-.98-.03-.03L6.6 2.38A.5.5 0 0 1 7.07 3l-3.67 3.68-.03.03-.98.98a.5.5 0 0 1-.71 0l-1.48-1.48a.5.5 0 0 1 .71-.71l1.12 1.12Z'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4M8.2 4.6l-2.4 2.4'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(var(--primary-color-rgb), 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2.5rem !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .form-control-lg, .form-select-lg {
            padding: 10px 14px !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SBU and Site dropdown relationship
        const sbuSelect = document.getElementById('sbu_id');
        const siteSelect = document.getElementById('site_id');
        
        // Store all sites data from PHP
        const allSites = @json($sbus->pluck('sites', 'id'));
        
        // Function to update site options based on selected SBU
        function updateSiteOptions() {
            const selectedSBU = sbuSelect.value;
            
            // Clear current options
            siteSelect.innerHTML = '';
            
            // Add default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.textContent = selectedSBU ? 'Select Site' : 'Select SBU first';
            siteSelect.appendChild(defaultOption);
            
            // If an SBU is selected, populate with corresponding sites
            if (selectedSBU && allSites[selectedSBU]) {
                allSites[selectedSBU].forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = site.name;
                    // Check if this option should be selected (for form validation redisplay)
                    if (site.id == '{{ old("site_id") }}') {
                        option.selected = true;
                    }
                    siteSelect.appendChild(option);
                });
            }
        }
        
        // Initialize site options based on initial SBU value
        updateSiteOptions();
        
        // Update site options when SBU selection changes
        sbuSelect.addEventListener('change', updateSiteOptions);
        
        // SweetAlert2 configuration
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        // Name field validation
        const nameField = document.getElementById('name');
        let nameIsValid = true;
        
        nameField.addEventListener('blur', function() {
            const name = nameField.value.trim();
            if (name) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('name-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if name exists in admin_users or users table via AJAX
                fetch(`/admin/check-name-availability?name=${encodeURIComponent(name)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Name already exists
                            nameIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'name-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            nameField.parentNode.appendChild(errorFeedback);
                            nameField.classList.add('is-invalid');
                        } else {
                            // Name is available
                            nameIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'name-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Name is available</small>';
                            nameField.parentNode.appendChild(successFeedback);
                            nameField.classList.remove('is-invalid');
                            nameField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking name:', error);
                    });
            }
        });
        
        // Email field validation
        const emailField = document.getElementById('email');
        let emailIsValid = true;
        
        emailField.addEventListener('blur', function() {
            const email = emailField.value.trim();
            if (email) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('email-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if email exists in admin_users or users table via AJAX
                fetch(`/admin/check-email-availability?email=${encodeURIComponent(email)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Email already exists
                            emailIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'email-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            emailField.parentNode.appendChild(errorFeedback);
                            emailField.classList.add('is-invalid');
                        } else {
                            // Email is available
                            emailIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'email-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Email is available</small>';
                            emailField.parentNode.appendChild(successFeedback);
                            emailField.classList.remove('is-invalid');
                            emailField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking email:', error);
                    });
            }
        });
        
        // Password field validation
        const passwordField = document.getElementById('password');
        let passwordIsValid = true;
        
        passwordField.addEventListener('blur', function() {
            const password = passwordField.value.trim();
            if (password) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('password-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if password exists in admin_users or users table via AJAX
                fetch(`/admin/check-password-availability?password=${encodeURIComponent(password)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Password already exists
                            passwordIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'password-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            passwordField.parentNode.appendChild(errorFeedback);
                            passwordField.classList.add('is-invalid');
                        } else {
                            // Password is available
                            passwordIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'password-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Password is available</small>';
                            passwordField.parentNode.appendChild(successFeedback);
                            passwordField.classList.remove('is-invalid');
                            passwordField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking password:', error);
                    });
            }
        });
    });

    // Function to handle form submission confirmation
    function confirmSubmit(event) {
        event.preventDefault();
        
        // Get name, email, and password validation states
        const nameField = document.getElementById('name');
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        
        // Check if fields have validation feedback
        const nameValidationFeedback = document.getElementById('name-validation-feedback');
        const emailValidationFeedback = document.getElementById('email-validation-feedback');
        const passwordValidationFeedback = document.getElementById('password-validation-feedback');
        
        const nameIsValid = !nameField.classList.contains('is-invalid');
        const emailIsValid = !emailField.classList.contains('is-invalid');
        const passwordIsValid = !passwordField.classList.contains('is-invalid');
        
        if (!nameIsValid) {
            swalWithBootstrapButtons.fire({
                title: "Name Already Exists",
                text: "Please use a different name.",
                icon: "error"
            });
            return false;
        }
        
        if (!emailIsValid) {
            swalWithBootstrapButtons.fire({
                title: "Email Already Exists", 
                text: "Please use a different email address.",
                icon: "error"
            });
            return false;
        }
        
        if (!passwordIsValid) {
            swalWithBootstrapButtons.fire({
                title: "Password Already in Use",
                text: "Please choose a different password.",
                icon: "error"
            });
            return false;
        }
        
        swalWithBootstrapButtons.fire({
            title: "Confirm Admin Creation",
            text: "Are you sure you want to add this administrator?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, create admin!",
            cancelButtonText: "Cancel",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                document.getElementById('adminForm').submit();
            }
        });
        
        return false;
    }

    // Function to handle close button confirmation
    function confirmClose() {
        Swal.fire({
            title: "Discard Changes?",
            text: "Any unsaved changes will be lost!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, leave page!",
            cancelButtonText: "Stay here",
            customClass: {
                confirmButton: "btn btn-danger me-3",
                cancelButton: "btn btn-outline-secondary"
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to dashboard
                window.location.href = "{{ route('admin.dashboard') }}";
            }
        });
    }
</script>
@endsection