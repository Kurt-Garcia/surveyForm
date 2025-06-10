@extends('layouts.app')

@section('content')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container-fluid py-4 px-4" style="background: var(--background-color); min-height: 100vh;">
    <!-- Hero Section with Gradient Background -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center mb-4" style="color: var(--text-color);">
                <h1 class="display-4 fw-bold mb-3" style="color: var(--text-color);">
                    <i class="bi bi-person-plus-fill me-3"></i>User Management
                </h1>
                <p class="lead" style="color: var(--text-color); opacity: 0.8;">Create new surveyors and manage existing users with modern tools</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Create New User Form - Left Side -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-header bg-gradient text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add New Surveyor
                            </h4>
                            <p class="mb-0 opacity-90 small mt-1">Create a new surveyor account</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm" onsubmit="return confirmSubmit(event)">
                        @csrf
                        
                        <!-- SBU Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="bi bi-building me-1" style="color: var(--primary-color);"></i>Strategic Business Units
                            </label>
                            <div class="sbu-selection-container">
                                <p class="text-muted mb-3 fs-6">Select one or more SBUs where this user will have access:</p>
                                <div class="row g-3">
                                    @foreach($sbus as $sbu)
                                        <div class="col-md-6">
                                            <div class="sbu-card" data-sbu-id="{{ $sbu->id }}">
                                                <input class="sbu-checkbox d-none" type="checkbox" 
                                                       id="sbu_{{ $sbu->id }}" 
                                                       name="sbu_ids[]" 
                                                       value="{{ $sbu->id }}"
                                                       {{ in_array($sbu->id, old('sbu_ids', [])) ? 'checked' : '' }}>
                                                
                                                <div class="sbu-card-content">
                                                    <div class="sbu-card-header">
                                                        <div class="sbu-check-indicator">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                    </div>
                                                    <div class="sbu-card-body">
                                                        <h5 class="sbu-name">{{ $sbu->name }}</h5>
                                                        <p class="sbu-sites-count">{{ $sbu->sites->count() }} sites available</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('sbu_ids')
                                    <div class="text-danger mt-3" role="alert">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Site Selection -->
                        <div class="mb-4">
                            <label for="site_ids" class="form-label fw-semibold text-dark">
                                <i class="bi bi-geo-alt me-1" style="color: var(--primary-color);"></i>Site Locations
                            </label>
                            <div class="sites-selection-container">
                                <div class="d-flex justify-content-between align-items-center mb-3 sites-header-container">
                                    <p class="text-muted mb-0 fs-6 flex-grow-1 me-3">Select sites where this user will have access:</p>
                                    <div class="selection-controls flex-shrink-0">
                                        <button type="button" id="selectAllSites" class="btn btn-sm me-2" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; border: none;" disabled>
                                            <i class="fas fa-check-double me-1"></i>Select All
                                        </button>
                                        <button type="button" id="deselectAllSites" class="btn btn-outline-secondary btn-sm" disabled>
                                            <i class="fas fa-times me-1"></i>Deselect All
                                        </button>
                                    </div>
                                </div>
                                <select id="site_ids" class="form-select select2 form-select-lg @error('site_ids') is-invalid @enderror" name="site_ids[]" multiple required>
                                    <option disabled>Please select SBUs first...</option>
                                </select>
                                @error('site_ids')
                                    <div class="text-danger mt-3" role="alert">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
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
                                       id="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" 
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
                                       id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="09123456789" 
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
                                <div class="password-input-group position-relative">
                                    <input type="password" class="form-control form-control-lg border-0 shadow-sm @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Enter password..." 
                                           autocomplete="new-password" required>
                                    <button type="button" class="password-toggle-btn" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-shield-check me-1 text-secondary"></i>Confirm Password
                                </label>
                                <div class="password-input-group position-relative">
                                    <input type="password" class="form-control form-control-lg border-0 shadow-sm" 
                                           id="password_confirmation" name="password_confirmation" placeholder="Confirm password..." 
                                           autocomplete="new-password" required>
                                    <button type="button" class="password-toggle-btn" data-target="password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border: none;">
                                <i class="bi bi-person-plus-fill me-2"></i>Create Surveyor Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Users Table - Right Side -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                <div class="card-header bg-gradient text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-people-fill me-2"></i>Survey Users
                            </h4>
                            <p class="mb-0 opacity-90 small mt-1">All surveyors in the system</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-database-fill me-1"></i><span id="totalUsers">Loading...</span> Total
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 fw-semibold text-dark border-0">Name</th>
                                    <th class="fw-semibold text-dark border-0">Email</th>
                                    <th class="fw-semibold text-dark border-0">Contact</th>
                                    <th class="fw-semibold text-dark border-0">SBU</th>
                                    <th class="fw-semibold text-dark border-0">Site</th>
                                    <th class="pe-4 fw-semibold text-dark border-0">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
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

    /* DataTable Modern Styling */
    .dataTables_wrapper {
        padding: 1.5rem;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_length {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dataTables_wrapper .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0 !important;
        font-weight: 500;
        color: var(--text-color);
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 25px !important;
        border: 2px solid #e9ecef !important;
        padding: 8px 20px !important;
        width: 350px !important;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25) !important;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px !important;
        border: 2px solid #e9ecef !important;
        padding: 6px 12px !important;
        width: auto !important;
        min-width: 80px !important;
        max-width: 120px !important;
    }

    /* Table Styling */
    .table th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border: none !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem !important;
    }

    .table td {
        border: none !important;
        padding: 1rem !important;
        vertical-align: middle !important;
    }

    .table tbody tr {
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.05) 0%, rgba(var(--secondary-color-rgb), 0.05) 100%) !important;
        transform: translateX(5px);
    }

    /* User Type Badges */
    .user-type-badge {
        border-radius: 20px !important;
        padding: 6px 16px !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-admin {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        color: white !important;
    }

    .badge-surveyor {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        color: white !important;
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

    /* Validation Styling */
    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    /* Export Buttons */
    .dt-buttons {
        margin-bottom: 1rem !important;
    }

    .dt-button {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 8px 16px !important;
        margin-right: 8px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease;
    }

    .dt-button:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 5px 15px rgba(var(--primary-color-rgb), 0.3) !important;
    }

    /* Pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        text-align: center !important;
        padding: 8px !important;
        margin: 0 2px !important;
        border: 2px solid var(--primary-color) !important;
        color: var(--primary-color) !important;
        background: white !important;
        transition: all 0.3s ease;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-color) !important;
        color: white !important;
        transform: scale(1.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .dataTables_wrapper {
            padding: 1rem !important;
        }
        
        /* DataTables Pagination - Mobile Fix */
        .dataTables_wrapper .dataTables_info {
            margin-bottom: 1rem !important;
            text-align: center !important;
            font-size: 0.8rem !important;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            margin-top: 1rem !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            width: 35px !important;
            height: 35px !important;
            padding: 6px !important;
            font-size: 0.8rem !important;
            margin: 0 1px !important;
        }
    }
    
    /* Very small mobile devices */
    @media (max-width: 480px) {
        .dataTables_wrapper .dataTables_info {
            font-size: 0.7rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            width: 30px !important;
            height: 30px !important;
            padding: 4px !important;
            font-size: 0.7rem !important;
            margin: 0 !important;
        }
    }
</style>

<script>
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-success me-3",
        cancelButton: "btn btn-outline-danger",
        actions: 'gap-2 justify-content-center'
    },
    buttonsStyling: false
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables for existing users
    initializeUsersTable();
    
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
            
            // Check if email exists via AJAX
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

    // Contact number field validation
    const contactNumberField = document.getElementById('contact_number');
    let contactNumberIsValid = true;
    
    // Debug: Check if contact number field exists
    if (!contactNumberField) {
        console.error('Contact number field not found!');
        return;
    }
    
    // Add real-time input validation for contact number
    contactNumberField.addEventListener('input', function() {
        let value = this.value;
        
        // Remove any existing feedback
        const existingFeedback = document.getElementById('contact-number-validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Clear validation classes
        this.classList.remove('is-invalid', 'is-valid');
        
        // Check if value starts with valid prefixes
        if (value.length > 0) {
            if (value.startsWith('+639')) {
                // For +639 format: allow only digits after +639 and limit to 13 total characters
                const afterPrefix = value.substring(4);
                const filteredAfterPrefix = afterPrefix.replace(/[^0-9]/g, '');
                
                // Reconstruct the value
                value = '+639' + filteredAfterPrefix;
                
                // Limit to 13 characters total (+639 + 9 digits)
                if (value.length > 13) {
                    value = value.substring(0, 13);
                }
                
                this.value = value;
                
                // Validate length
                if (value.length < 13) {
                    contactNumberIsValid = false;
                    const warningFeedback = document.createElement('div');
                    warningFeedback.className = 'text-warning mt-1';
                    warningFeedback.id = 'contact-number-validation-feedback';
                    warningFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle me-1"></i>Contact number should be 13 characters for +639 format (${value.length}/13)</small>`;
                    this.parentNode.appendChild(warningFeedback);
                    this.classList.add('is-invalid');
                } else {
                    contactNumberIsValid = true;
                    const successFeedback = document.createElement('div');
                    successFeedback.className = 'text-success mt-1';
                    successFeedback.id = 'contact-number-validation-feedback';
                    successFeedback.innerHTML = `<small><i class="bi bi-check-circle-fill me-1"></i>Valid contact number (${value.length}/13)</small>`;
                    this.parentNode.appendChild(successFeedback);
                    this.classList.add('is-valid');
                }
                
            } else if (value.startsWith('09')) {
                // For 09 format: allow only digits and limit to 11 total characters
                value = value.replace(/[^0-9]/g, '');
                
                // Ensure it still starts with 09 after filtering
                if (!value.startsWith('09')) {
                    value = '09';
                }
                
                // Limit to 11 characters total
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                
                this.value = value;
                
                // Validate length
                if (value.length < 11) {
                    contactNumberIsValid = false;
                    const warningFeedback = document.createElement('div');
                    warningFeedback.className = 'text-warning mt-1';
                    warningFeedback.id = 'contact-number-validation-feedback';
                    warningFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle me-1"></i>Contact number should be 11 characters for 09 format (${value.length}/11)</small>`;
                    this.parentNode.appendChild(warningFeedback);
                    this.classList.add('is-invalid');
                } else {
                    contactNumberIsValid = true;
                    const successFeedback = document.createElement('div');
                    successFeedback.className = 'text-success mt-1';
                    successFeedback.id = 'contact-number-validation-feedback';
                    successFeedback.innerHTML = `<small><i class="bi bi-check-circle-fill me-1"></i>Valid contact number (${value.length}/11)</small>`;
                    this.parentNode.appendChild(successFeedback);
                    this.classList.add('is-valid');
                }
                
            } else {
                // Value doesn't start with valid prefix
                // Check if user is typing +639
                if ('+639'.startsWith(value)) {
                    // User is still typing +639, allow it
                    this.value = value;
                    contactNumberIsValid = false;
                    const infoFeedback = document.createElement('div');
                    infoFeedback.className = 'text-info mt-1';
                    infoFeedback.id = 'contact-number-validation-feedback';
                    infoFeedback.innerHTML = `<small><i class="bi bi-info-circle me-1"></i>Continue typing +639...</small>`;
                    this.parentNode.appendChild(infoFeedback);
                } else if ('09'.startsWith(value)) {
                    // User is still typing 09, allow it
                    this.value = value;
                    contactNumberIsValid = false;
                    const infoFeedback = document.createElement('div');
                    infoFeedback.className = 'text-info mt-1';
                    infoFeedback.id = 'contact-number-validation-feedback';
                    infoFeedback.innerHTML = `<small><i class="bi bi-info-circle me-1"></i>Continue typing 09...</small>`;
                    this.parentNode.appendChild(infoFeedback);
                } else {
                    // Invalid start, clear the field or reset to valid start
                    contactNumberIsValid = false;
                    this.value = '';
                    const errorFeedback = document.createElement('div');
                    errorFeedback.className = 'text-danger mt-1';
                    errorFeedback.id = 'contact-number-validation-feedback';
                    errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Contact number must start with 09 or +639</small>`;
                    this.parentNode.appendChild(errorFeedback);
                    this.classList.add('is-invalid');
                }
            }
        }
    });
    
    // Handle paste events to ensure pasted content follows the same rules
    contactNumberField.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        
        // Clean and validate pasted text
        let cleanedText = pastedText.trim();
        
        if (cleanedText.startsWith('+639')) {
            // Keep +639 and digits only
            cleanedText = '+639' + cleanedText.substring(4).replace(/[^0-9]/g, '');
            if (cleanedText.length > 13) {
                cleanedText = cleanedText.substring(0, 13);
            }
        } else if (cleanedText.startsWith('09')) {
            // Keep digits only
            cleanedText = cleanedText.replace(/[^0-9]/g, '');
            if (!cleanedText.startsWith('09')) {
                cleanedText = '';
            }
            if (cleanedText.length > 11) {
                cleanedText = cleanedText.substring(0, 11);
            }
        } else {
            // Invalid format
            cleanedText = '';
        }
        
        this.value = cleanedText;
        
        // Trigger input event to run validation
        this.dispatchEvent(new Event('input'));
    });
    
    contactNumberField.addEventListener('blur', function() {
        console.log('Contact number blur event triggered');
        const contactNumber = contactNumberField.value.trim();
        console.log('Contact number value:', contactNumber);
        
        if (contactNumber && ((contactNumber.startsWith('09') && contactNumber.length === 11) || 
                              (contactNumber.startsWith('+639') && contactNumber.length === 13))) {
            // Remove any existing feedback
            const existingFeedback = document.getElementById('contact-number-validation-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            console.log('Making AJAX call for contact number validation...');
            
            // Check if contact number exists via AJAX
            fetch(`/admin/check-contact-number-availability?contact_number=${encodeURIComponent(contactNumber)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.exists) {
                        // Contact number already exists
                        contactNumberIsValid = false;
                        const errorFeedback = document.createElement('div');
                        errorFeedback.className = 'text-danger mt-1';
                        errorFeedback.id = 'contact-number-validation-feedback';
                        errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                        contactNumberField.parentNode.appendChild(errorFeedback);
                        contactNumberField.classList.add('is-invalid');
                        console.log('Error feedback added');
                    } else {
                        // Contact number is available
                        contactNumberIsValid = true;
                        const successFeedback = document.createElement('div');
                        successFeedback.className = 'text-success mt-1';
                        successFeedback.id = 'contact-number-validation-feedback';
                        successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Contact number is available</small>';
                        contactNumberField.parentNode.appendChild(successFeedback);
                        contactNumberField.classList.remove('is-invalid');
                        contactNumberField.classList.add('is-valid');
                        console.log('Success feedback added');
                    }
                })
                .catch(error => {
                    console.error('Error checking contact number:', error);
                });
        }
    });
    
    // Password field validation and real-time matching
    const passwordField = document.getElementById('password');
    const passwordConfirmationField = document.getElementById('password_confirmation');
    let passwordsMatch = false;
    
    // Function to check password match in real-time
    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmation = passwordConfirmationField.value;
        
        // Remove existing match feedback
        const existingMatchFeedback = document.getElementById('password-match-feedback');
        if (existingMatchFeedback) {
            existingMatchFeedback.remove();
        }
        
        // Only show feedback if both fields have values
        if (password && confirmation) {
            if (password === confirmation) {
                // Passwords match
                passwordsMatch = true;
                const successFeedback = document.createElement('div');
                successFeedback.className = 'text-success mt-1';
                successFeedback.id = 'password-match-feedback';
                successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Passwords match</small>';
                passwordConfirmationField.parentNode.appendChild(successFeedback);
                passwordConfirmationField.classList.remove('is-invalid');
                passwordConfirmationField.classList.add('is-valid');
            } else {
                // Passwords don't match
                passwordsMatch = false;
                const errorFeedback = document.createElement('div');
                errorFeedback.className = 'text-danger mt-1';
                errorFeedback.id = 'password-match-feedback';
                errorFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Passwords do not match</small>';
                passwordConfirmationField.parentNode.appendChild(errorFeedback);
                passwordConfirmationField.classList.remove('is-valid');
                passwordConfirmationField.classList.add('is-invalid');
            }
        } else if (confirmation && !password) {
            // Confirmation field has value but password field is empty
            passwordsMatch = false;
            const warningFeedback = document.createElement('div');
            warningFeedback.className = 'text-warning mt-1';
            warningFeedback.id = 'password-match-feedback';
            warningFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle me-1"></i>Please enter password first</small>';
            passwordConfirmationField.parentNode.appendChild(warningFeedback);
            passwordConfirmationField.classList.remove('is-valid');
            passwordConfirmationField.classList.add('is-invalid');
        } else {
            // Reset validation classes if fields are empty
            passwordConfirmationField.classList.remove('is-valid', 'is-invalid');
            passwordsMatch = false;
        }
    }
    
    // Add real-time password matching validation
    passwordField.addEventListener('input', checkPasswordMatch);
    passwordConfirmationField.addEventListener('input', checkPasswordMatch);
});

function initializeUsersTable() {
    $('#usersTable').DataTable({
        ajax: {
            url: '{{ route("admin.users.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { 
                data: 'name',
                render: function(data, type, row) {
                    return `<h6 class="mb-0 fw-semibold">${data}</h6>`;
                }
            },
            { 
                data: 'email',
                render: function(data) {
                    return `<span class="text-muted"><i class="bi bi-envelope me-1"></i>${data}</span>`;
                }
            },
            { 
                data: 'contact_number',
                render: function(data) {
                    return `<span class="text-muted"><i class="bi bi-telephone me-1"></i>${data}</span>`;
                }
            },
            { 
                data: 'sbu_name',
                render: function(data) {
                    return `<span class="badge bg-info rounded-pill">${data}</span>`;
                }
            },
            { 
                data: 'site_name',
                render: function(data) {
                    return `<span class="badge bg-secondary rounded-pill">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return `<small class="text-muted"><i class="bi bi-calendar me-1"></i>${data}</small>`;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        responsive: true,
        order: [[5, 'desc']], // Sort by created date
        language: {
            search: "<i class='bi bi-search'></i>",
            searchPlaceholder: "Search surveyors...",
            lengthMenu: "_MENU_ per page",
            info: "Showing <span class='fw-semibold'>_START_</span> to <span class='fw-semibold'>_END_</span> of <span class='fw-semibold'>_TOTAL_</span> surveyors",
            paginate: {
                first: "<i class='bi bi-chevron-double-left'></i>",
                last: "<i class='bi bi-chevron-double-right'></i>",
                next: "<i class='bi bi-chevron-right'></i>",
                previous: "<i class='bi bi-chevron-left'></i>"
            },
            emptyTable: "<div class='text-center py-5'><i class='bi bi-people-fill text-muted fs-1 mb-3'></i><p class='text-muted'>No surveyors found</p></div>"
        },
        dom: '<"row mb-4"<"col-md-6 d-flex gap-2"Bl><"col-md-6"f>>rt<"row align-items-center py-3"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'collection',
                text: '<i class="bi bi-download me-1"></i> Export',
                className: 'btn btn-primary btn-sm rounded-pill px-3',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard me-1"></i> Copy',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-filetype-csv me-1"></i> CSV',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                        title: 'Survey Users List'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                        title: 'Survey Users List'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                        title: 'Survey Users List'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                        title: 'Survey Users List'
                    }
                ]
            }
        ],
        initComplete: function() {
            // Update total users count
            const info = this.api().page.info();
            document.getElementById('totalUsers').textContent = info.recordsTotal;
            
            // Style the search input
            $('.dataTables_filter input').addClass('form-control');
            $('.dataTables_filter label').addClass('position-relative');
            
            // Style the length select
            $('.dataTables_length select').addClass('form-select');
        },
        drawCallback: function() {
            // Add animation to rows
            $('.table tbody tr').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.05) + 's',
                    'animation': 'fadeInUp 0.6s ease forwards'
                });
            });
        }
    });
}

function confirmClose() {
    swalWithBootstrapButtons.fire({
        title: "Are you sure?",
        text: "Any unsaved changes will be lost!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, leave page!",
        cancelButtonText: "No, stay here!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('admin.dashboard') }}";
        }
    });
}

function confirmSubmit(event) {
    event.preventDefault();
    
    // Check if name and email are valid before submitting
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const contactNumberField = document.getElementById('contact_number');
    
    // Only check for invalid class if the validation has been performed (feedback exists)
    const nameValidationFeedback = document.getElementById('name-validation-feedback');
    const emailValidationFeedback = document.getElementById('email-validation-feedback');
    const contactNumberValidationFeedback = document.getElementById('contact-number-validation-feedback');
    
    // Also check the validation flags
    if ((nameValidationFeedback && nameField.classList.contains('is-invalid')) ||
        (emailValidationFeedback && emailField.classList.contains('is-invalid')) ||
        (contactNumberValidationFeedback && contactNumberField.classList.contains('is-invalid')) ||
        (typeof nameIsValid !== 'undefined' && !nameIsValid) ||
        (typeof emailIsValid !== 'undefined' && !emailIsValid) ||
        (typeof contactNumberIsValid !== 'undefined' && !contactNumberIsValid)) {
        swalWithBootstrapButtons.fire({
            title: "Invalid Input",
            text: "Please fix the validation errors before submitting.",
            icon: "error",
            confirmButtonText: "OK"
        });
        return false;
    }
    
    // Check password confirmation match using the real-time validation flag
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    if (password && passwordConfirmation) {
        if (typeof passwordsMatch !== 'undefined' && !passwordsMatch) {
            swalWithBootstrapButtons.fire({
                title: "Password Mismatch",
                text: "Password and confirmation password do not match.",
                icon: "error",
                confirmButtonText: "OK"
            });
            return false;
        }
        
        if (password !== passwordConfirmation) {
            swalWithBootstrapButtons.fire({
                title: "Password Mismatch",
                text: "Password and confirmation password do not match.",
                icon: "error",
                confirmButtonText: "OK"
            });
            return false;
        }
    }
    
    swalWithBootstrapButtons.fire({
        title: "Create New Surveyor?",
        text: "Please confirm to create a new surveyor account!",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, create it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            const submitBtn = document.querySelector('#userForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Creating...';
            submitBtn.disabled = true;
            
            document.getElementById('userForm').submit();
        }
    });
    
    return false;
}

// Function to handle close button confirmation (global scope)
function confirmClose() {
    swalWithBootstrapButtons.fire({
        title: "Discard Changes?",
        text: "Any unsaved changes will be lost!",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "Stay here",
        confirmButtonText: "Yes, leave page!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to dashboard
            window.location.href = "{{ route('admin.dashboard') }}";
        }
    });
}

// SBU Card functionality
document.querySelectorAll('.sbu-card').forEach(card => {
    card.addEventListener('click', function() {
        const checkbox = this.querySelector('.sbu-checkbox');
        checkbox.checked = !checkbox.checked;
        
        if (checkbox.checked) {
            this.classList.add('selected');
        } else {
            this.classList.remove('selected');
        }
        
        // Update sites after SBU selection changes
        updateSiteOptions();
    });
});

// Function to update site options based on selected SBUs
function updateSiteOptions() {
    const selectedSbuIds = [];
    document.querySelectorAll('.sbu-checkbox:checked').forEach(checkbox => {
        selectedSbuIds.push(checkbox.value);
    });
    
    const siteSelect = document.getElementById('site_ids');
    const selectAllBtn = document.getElementById('selectAllSites');
    const deselectAllBtn = document.getElementById('deselectAllSites');
    
    // Clear existing options
    siteSelect.innerHTML = '';
    
    if (selectedSbuIds.length === 0) {
        siteSelect.innerHTML = '<option disabled>Please select SBUs first...</option>';
        selectAllBtn.disabled = true;
        deselectAllBtn.disabled = true;
        return;
    }
    
    // Enable control buttons
    selectAllBtn.disabled = false;
    deselectAllBtn.disabled = false;
    
    // Get sites for selected SBUs
    fetch(`{{ route('admin.sites.by-sbus') }}?sbu_ids=${selectedSbuIds.join(',')}`)
        .then(response => response.json())
        .then(sites => {
            sites.forEach(site => {
                const option = document.createElement('option');
                option.value = site.id;
                option.textContent = `${site.name} (${site.sbu.name})`;
                siteSelect.appendChild(option);
            });
            
            // Refresh Select2 if initialized
            const $siteSelect = jQuery(siteSelect);
            if ($siteSelect.hasClass('select2-hidden-accessible')) {
                $siteSelect.trigger('change');
            } else {
                // Initialize Select2 if not already done
                $siteSelect.select2({
                    placeholder: 'Select sites...',
                    allowClear: true,
                    width: '100%'
                });
            }
        })
        .catch(error => {
            console.error('Error fetching sites:', error);
            siteSelect.innerHTML = '<option disabled>Error loading sites</option>';
        });
}

// Site selection control buttons
document.getElementById('selectAllSites').addEventListener('click', function() {
    const siteSelect = document.getElementById('site_ids');
    Array.from(siteSelect.options).forEach(option => {
        option.selected = true;
    });
    
    const $siteSelect = jQuery(siteSelect);
    if ($siteSelect.hasClass('select2-hidden-accessible')) {
        $siteSelect.trigger('change');
    }
});

document.getElementById('deselectAllSites').addEventListener('click', function() {
    const siteSelect = document.getElementById('site_ids');
    Array.from(siteSelect.options).forEach(option => {
        option.selected = false;
    });
    
    const $siteSelect = jQuery(siteSelect);
    if ($siteSelect.hasClass('select2-hidden-accessible')) {
        $siteSelect.trigger('change');
    }
});

// Select2 will be initialized by the global layout script

// Restore selected SBUs from old input (if validation fails)
document.addEventListener('DOMContentLoaded', function() {
    const oldSbuIds = @json(old('sbu_ids', []));
    if (oldSbuIds && oldSbuIds.length > 0) {
        oldSbuIds.forEach(sbuId => {
            const card = document.querySelector(`[data-sbu-id="${sbuId}"]`);
            if (card) {
                const checkbox = card.querySelector('.sbu-checkbox');
                checkbox.checked = true;
                card.classList.add('selected');
            }
        });
        updateSiteOptions();
    }
});

// Add CSS animations and styles
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* SBU Card Styles */
    .sbu-selection-container {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f8 100%);
        border-radius: 12px;
        border: 2px solid #e9ecf3;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    .sbu-selection-container:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(var(--primary-color-rgb), 0.1);
    }
    
    .sbu-card {
        cursor: pointer;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #ffffff;
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 120px;
    }
    
    .sbu-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(var(--primary-color-rgb), 0.15);
        transform: translateY(-2px);
    }
    
    .sbu-card.selected {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        box-shadow: 0 6px 20px rgba(var(--primary-color-rgb), 0.3);
    }
    
    .sbu-card-content {
        padding: 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .sbu-card-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 0.5rem;
    }
    
    .sbu-check-indicator {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        background: white;
        opacity: 0;
        transform: scale(0);
    }
    
    .sbu-card.selected .sbu-check-indicator {
        background: #ffffff;
        border-color: #ffffff;
        color: var(--primary-color);
        opacity: 1;
        transform: scale(1);
    }
    
    .sbu-check-indicator i {
        font-size: 12px;
        opacity: 1;
        transition: opacity 0.3s ease;
    }
    
    .sbu-card.selected .sbu-check-indicator i {
        opacity: 1;
    }
    
    .sbu-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .sbu-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: inherit;
    }
    
    .sbu-sites-count {
        font-size: 0.875rem;
        margin-bottom: 0;
        opacity: 0.8;
        color: inherit;
    }
    
    .sbu-card:not(.selected) .sbu-sites-count {
        color: #6c757d;
    }
    
    /* Sites selection styles */
    .sites-selection-container {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f8 100%);
        border-radius: 12px;
        border: 2px solid #e9ecf3;
        transition: all 0.3s ease;
    }
    
    .sites-selection-container:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(var(--primary-color-rgb), 0.1);
    }
    
    .sites-selection-container .selection-controls {
        flex-shrink: 0;
    }
    
    .sites-selection-container .form-select {
        min-height: 120px;
    }
    
    /* Sites header container responsive handling */
    .sites-header-container {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .sites-header-container .text-muted {
        min-width: 0;
        flex: 1 1 auto;
    }
    
    .sites-header-container .selection-controls {
        flex: 0 0 auto;
        min-width: 200px;
    }

    /* Enhanced Select2 styling for sites */
    .sites-selection-container .select2-container--default .select2-selection--multiple {
        border: 2px solid #e9ecf3;
        border-radius: 8px;
        min-height: 120px;
        background: white;
        transition: all 0.3s ease;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
        word-wrap: break-word !important;
    }

    .sites-selection-container .select2-container--default .select2-selection--multiple:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.15);
    }

    .sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border: none;
        color: white;
        border-radius: 6px;
        padding: 6px 35px 6px 25px;
        margin: 6px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
        max-width: calc(100% - 20px) !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
    }

    .sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255,255,255,0.8);
        margin-right: 8px;
        border: none;
        font-size: 1.1rem;
        transition: color 0.2s ease;
    }

    .sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: white;
        background: rgba(255,255,255,0.2);
        border-radius: 3px;
    }

    .sites-selection-container .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }

    .sites-selection-container .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 8px;
        font-size: 0.95rem;
    }

    .sites-selection-container .select2-container--default .select2-search--inline .select2-search__field::placeholder {
        color: #6c757d;
        font-style: italic;
    }

    /* Ensure Select2 dropdown doesn't overflow on smaller screens */
    .select2-dropdown {
        z-index: 9999 !important;
    }
    
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        width: 100% !important;
        overflow-x: hidden !important;
        word-wrap: break-word !important;
        box-sizing: border-box !important;
    }
    
    /* Select2 custom styles for site selection */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        min-height: 120px;
        padding: 8px;
        transition: all 0.3s ease;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border: 1px solid var(--primary-color);
        border-radius: 6px;
        color: white;
        padding: 4px 8px;
        margin: 2px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 8px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcccc;
    }
    
    /* Select2 dropdown styling */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
    }
    
    /* Site selection buttons styling */
    .sites-selection-container .selection-controls .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(var(--primary-color-rgb), 0.2);
    }
    
    .sites-selection-container .selection-controls .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .sites-selection-container .selection-controls .btn:disabled:hover {
        transform: none;
        box-shadow: none;
    }
    
    /* iPad Pro responsive fixes - prevent overflow */
    @media screen and (min-width: 1024px) and (max-width: 1366px) {
        .sites-selection-container {
            padding: 1.25rem;
        }
        
        .sites-selection-container .sites-header-container {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
            width: 100%;
            justify-content: flex-start;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            min-width: auto;
            flex: 0 1 auto;
            white-space: nowrap;
        }
        
        .sites-selection-container .text-muted {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            flex: none;
            width: 100%;
        }
        
        .select2-container--default .select2-selection--multiple {
            min-height: 100px;
            max-height: 150px;
            overflow-y: auto;
            width: 100% !important;
            max-width: 100%;
            box-sizing: border-box;
        }
        
        .select2-container {
            width: 100% !important;
            max-width: 100%;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            padding: 3px 6px;
            margin: 2px;
            font-size: 0.85rem;
            max-width: calc(100% - 10px);
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* SBU cards responsive adjustments for iPad Pro */
        .sbu-selection-container {
            padding: 1.25rem;
        }
        
        .sbu-card {
            min-height: 110px;
        }
        
        .sbu-name {
            font-size: 1rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
    }
    
    /* iPad Mini responsive (768px to 820px) */
    @media (min-width: 768px) and (max-width: 820px) {
        .sites-selection-container {
            padding: 1rem;
        }
        
        .sites-selection-container .sites-header-container {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .sites-selection-container .text-muted {
            width: 100%;
            flex: none;
            margin-bottom: 0.5rem;
        }
    }
    
    /* Standard tablet responsive (821px to 1023px) */
    @media (min-width: 821px) and (max-width: 1023px) {
        .sites-selection-container {
            padding: 1.25rem;
        }
        
        .sites-selection-container .sites-header-container {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            width: 100%;
            gap: 0.75rem;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            flex: 1;
            font-size: 0.85rem;
            padding: 0.65rem 0.85rem;
        }
        
        .sites-selection-container .text-muted {
            width: 100%;
            flex: none;
        }
    }
    
    /* Mobile responsive improvements (below 768px) */
    @media (max-width: 767px) {
        .sbu-selection-container,
        .sites-selection-container {
            padding: 1rem;
        }
        
        .sites-selection-container .sites-header-container {
            flex-direction: column;
            gap: 1.25rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .sites-selection-container .text-muted {
            width: 100%;
            flex: none;
            margin-bottom: 0.75rem;
        }
        
        .sbu-card {
            min-height: 100px;
        }
        
        .sbu-name {
            font-size: 1rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
    }
    
    /* Very small mobile devices (below 480px) */
    @media (max-width: 479px) {
        .sites-selection-container .selection-controls {
            gap: 1.25rem;
        }
        
        .sites-selection-container .selection-controls .btn {
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }
        
        .sites-selection-container .sites-header-container {
            gap: 1.5rem;
        }
        
        .sites-selection-container .text-muted {
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection