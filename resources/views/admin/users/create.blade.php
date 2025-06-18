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
        <div class="col-lg-3 col-xl-4">
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
                                        <div class="col-md-6 col-lg-12 col-xl-6">
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
                            <div class="col-md-7 col-lg-12 col-xl-7">
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
                            <div class="col-md-5 col-lg-12 col-xl-5">
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
                        <div class="row g-3 mb-4 password-fields">
                            <div class="col-md-6 col-lg-12 col-xl-6">
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
                            <div class="col-md-6 col-lg-12 col-xl-6">
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
        <div class="col-lg-9 col-xl-8">
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

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient text-white">
                <h5 class="modal-title fw-bold" id="userDetailsModalLabel">
                    <i class="bi bi-person-circle me-2"></i>User Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Fixed User Information Section -->
            <div class="modal-body-fixed p-4 border-bottom">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">
                                <i class="bi bi-person me-2 text-primary"></i>Full Name
                            </div>
                            <div class="info-value" id="modal-user-name">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">
                                <i class="bi bi-envelope me-2 text-warning"></i>Email Address
                            </div>
                            <div class="info-value" id="modal-user-email">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">
                                <i class="bi bi-telephone me-2 text-success"></i>Contact Number
                            </div>
                            <div class="info-value" id="modal-user-contact">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-label">
                                <i class="bi bi-calendar me-2 text-info"></i>Created Date
                            </div>
                            <div class="info-value" id="modal-user-created">-</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fixed Search Section -->
            <div class="modal-search-fixed p-3 border-bottom bg-light">
                <div id="modal-search-container">
                    <!-- DataTable search will be moved here -->
                </div>
            </div>

            <!-- Scrollable Table Section -->
            <div class="modal-body-scrollable">
                <div class="table-container-full-width">
                    <table id="modalUserDetailsTable" class="table table-hover modern-table mb-0">
                        <thead class="table-header-full-width">
                            <tr>
                                <th class="fw-semibold" style="width: 60px;">No.</th>
                                <th class="fw-semibold">SBU</th>
                                <th class="fw-semibold">SITE</th>
                            </tr>
                        </thead>
                        <tbody id="modal-sbu-sites-table" class="table-body-padded">
                            <!-- Dynamic content will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fixed Pagination Section -->
            <div class="modal-pagination-fixed p-3 border-top bg-light">
                <div id="modal-pagination-container" class="d-flex justify-content-between align-items-center">
                    <div id="modal-info-container">
                        <!-- DataTable info will be moved here -->
                    </div>
                    <div id="modal-paginate-container">
                        <!-- DataTable pagination will be moved here -->
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal Size Control */
    .modal-dialog {
        max-width: 90vw;
    }
    
    /* Ensure modal centering on all devices */
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100vh - 2rem);
    }
    
    /* Mobile modal centering fixes */
    @media (max-width: 768px) {
        .modal-dialog-centered {
            min-height: calc(100vh - 1rem);
        }
        
        .modal-dialog {
            margin: 0.5rem auto;
            max-width: calc(100vw - 1rem);
        }
    }
    
    .modal-content {
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    
    @media (min-width: 992px) {
        .modal-dialog {
            max-width: 800px;
        }
    }
    
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

    /* Remove Bootstrap validation icons */
    .form-control.is-valid,
    .form-control.is-invalid {
        background-image: none !important;
        padding-right: 12px !important;
    }

    /* Custom validation styles - only border colors */
    .form-control.is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.1rem rgba(40, 167, 69, 0.25) !important;
    }

    .form-control.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Special handling for password fields to account for toggle button */
    .password-input-group .form-control.is-valid,
    .password-input-group .form-control.is-invalid {
        padding-right: 50px !important;
        background-image: none !important;
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

    /* Modal Body Layout */
    .modal-body-fixed {
        flex-shrink: 0;
    }
    
    .modal-body-scrollable {
        flex: 1;
        overflow-y: auto;
        min-height: 200px;
        max-height: 300px; /* Reduced to account for fixed search and pagination */
    }
    
    /* Fixed Pagination Section Styling */
    .modal-pagination-fixed {
        flex-shrink: 0;
        background: #f8f9fa !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .modal-pagination-fixed .dataTables_info {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate {
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Enhanced table row hover effect */
    .table-hover-active {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.08) 0%, rgba(var(--secondary-color-rgb), 0.08) 100%) !important;
        transform: translateX(3px) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }

    .more-sites-badge {
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        margin-left: 2px !important;
    }

    .more-sites-badge:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 15px !important;
        overflow: hidden;
    }

    .modal-header.bg-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
        word-break: break-word;
    }

    .table-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .table-section h6 {
        margin-bottom: 1.5rem !important;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--primary-color);
        color: #2c3e50;
    }

    .modern-table {
        margin-bottom: 0 !important;
        margin-top: 0 !important; /* Remove top margin */
        margin-left: 0 !important; /* Remove left margin */
        margin-right: 0 !important; /* Remove right margin */
        background: white;
        border-radius: 0 !important;
        overflow: visible;
        box-shadow: none;
        border: none;
        width: 100% !important; /* Ensure full width */
        /* Ensure table supports sticky positioning */
        border-collapse: separate;
        border-spacing: 0;
        /* Prevent content from bleeding through sticky header */
        position: relative;
    }

    /* Ensure table body doesn't interfere with sticky header */
    .modern-table tbody {
        position: relative;
        z-index: 1;
        background: white;
    }

    /* Additional layer protection for table rows */
    .modern-table tbody tr {
        position: relative;
        background: white;
        z-index: 1;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        border: none !important;
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .modern-table tbody td {
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        border-bottom: 1px solid #f1f3f5 !important;
        vertical-align: middle !important;
        transition: all 0.3s ease;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.05) 0%, rgba(var(--secondary-color-rgb), 0.05) 100%) !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .sbu-cell {
        color: var(--primary-color) !important;
        font-weight: 700 !important;
        font-size: 1rem;
        position: relative;
    }

    .sites-cell {
        color: #495057;
        font-weight: 500;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .sites-cell .text-muted {
        font-style: italic;
        color: #6c757d !important;
    }

    /* Modal responsive adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
            max-height: calc(100vh - 1rem);
        }
        
        .modal-content {
            height: calc(100vh - 1rem);
            display: flex;
            flex-direction: column;
        }
        
        .modal-body-fixed {
            flex-shrink: 0;
            padding: 1rem !important;
        }
        
        .modal-body-scrollable {
            flex: 1;
            overflow-y: auto;
            max-height: none;
            padding: 0 !important; /* Remove padding to fix sticky header */
        }
        
        /* Add padding to table container instead */
        .modal-body-scrollable .table-container-full-width {
            padding: 0 1rem 1rem 1rem !important;
        }
        
        .modal-footer {
            flex-shrink: 0;
            padding: 0.75rem 1rem !important;
        }
        
        .modal-body-scrollable {
            max-height: 300px;
        }
        
        .info-card {
            padding: 0.75rem;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.75rem 0.5rem !important; /* Further reduce padding for mobile */
            font-size: 0.9rem;
        }
        
        .sbu-cell {
            font-size: 0.9rem;
        }
        
        .sites-cell {
            font-size: 0.85rem;
        }
        
        /* Modal DataTable responsive adjustments */
        .modal-body-scrollable .dataTables_wrapper .row {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        
        .modal-body-scrollable .dataTables_wrapper .row > div {
            width: 100% !important;
            justify-content: center !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: center !important;
            justify-content: center !important;
        }
        
        .modal-body_scrollable .dataTables_wrapper .row > div {
            display: flex !important;
            align-items: center !important;
            padding: 0 !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: right !important;
            justify-content: flex-end !important;
        }
        
        .modal-body-scrollable .dataTables_length {
            justify-content: flex-start !important;
        }
        
        .modal-body-scrollable .dataTables_filter label,
        .modal-body-scrollable .dataTables_length label {
            margin-bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

    /* Fixed Pagination Section Styling */
    .modal-pagination-fixed {
        flex-shrink: 0;
        background: #f8f9fa !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .modal-pagination-fixed .dataTables_info {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate {
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Tooltip styling for better visibility */
    .tooltip {
        font-size: 0.8rem !important;
    }

    .tooltip-inner {
        max-width: 300px !important;
        text-align: center !important;
        word-wrap: break-word !important;
        background: rgba(0,0,0,0.9) !important;
        border-radius: 6px !important;
        padding: 8px 12px !important;
    }

    /* Mobile responsiveness for site badges */
    @media (max-width: 768px) {
        .site-badge {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
            max-width: 120px !important;
        }
        
        .site-column {
            max-width: 180px !important;
            min-width: 120px !important;
        }
    }

    @media (max-width: 480px) {
        .site-badge {
            font-size: 0.65rem !important;
            padding: 3px 6px !important;
            max-width: 100px !important;
        }
        
        .site-column {
            max-width: 150px !important;
            min-width: 100px !important;
        }
    }

    /* Clickable row styling */
    .table-hover-active {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.08) 0%, rgba(var(--secondary-color-rgb), 0.08) 100%) !important;
        transform: translateX(3px) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }

    .more-sites-badge {
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        margin-left: 2px !important;
    }

    .more-sites-badge:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 15px !important;
        overflow: hidden;
    }

    .modal-header.bg-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
        word-break: break-word;
    }

    .table-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .table-section h6 {
        margin-bottom: 1.5rem !important;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--primary-color);
        color: #2c3e50;
    }

    .modern-table {
        margin-bottom: 0 !important;
        margin-top: 0 !important; /* Remove top margin */
        margin-left: 0 !important; /* Remove left margin */
        margin-right: 0 !important; /* Remove right margin */
        background: white;
        border-radius: 0 !important;
        overflow: visible;
        box-shadow: none;
        border: none;
        width: 100% !important; /* Ensure full width */
        /* Ensure table supports sticky positioning */
        border-collapse: separate;
        border-spacing: 0;
        /* Prevent content from bleeding through sticky header */
        position: relative;
    }

    /* Ensure table body doesn't interfere with sticky header */
    .modern-table tbody {
        position: relative;
        z-index: 1;
        background: white;
    }

    /* Additional layer protection for table rows */
    .modern-table tbody tr {
        position: relative;
        background: white;
        z-index: 1;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        border: none !important;
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .modern-table tbody td {
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        border-bottom: 1px solid #f1f3f5 !important;
        vertical-align: middle !important;
        transition: all 0.3s ease;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.05) 0%, rgba(var(--secondary-color-rgb), 0.05) 100%) !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .sbu-cell {
        color: var(--primary-color) !important;
        font-weight: 700 !important;
        font-size: 1rem;
        position: relative;
    }

    .sites-cell {
        color: #495057;
        font-weight: 500;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .sites-cell .text-muted {
        font-style: italic;
        color: #6c757d !important;
    }

    /* Modal responsive adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
            max-height: calc(100vh - 1rem);
        }
        
        .modal-content {
            height: calc(100vh - 1rem);
            display: flex;
            flex-direction: column;
        }
        
        .modal-body-fixed {
            flex-shrink: 0;
            padding: 1rem !important;
        }
        
        .modal-body-scrollable {
            flex: 1;
            overflow-y: auto;
            max-height: none;
            padding: 0 !important; /* Remove padding to fix sticky header */
        }
        
        /* Add padding to table container instead */
        .modal-body-scrollable .table-container-full-width {
            padding: 0 1rem 1rem 1rem !important;
        }
        
        .modal-footer {
            flex-shrink: 0;
            padding: 0.75rem 1rem !important;
        }
        
        .modal-body-scrollable {
            max-height: 300px;
        }
        
        .info-card {
            padding: 0.75rem;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.75rem 0.5rem !important; /* Further reduce padding for mobile */
            font-size: 0.9rem;
        }
        
        .sbu-cell {
            font-size: 0.9rem;
        }
        
        .sites-cell {
            font-size: 0.85rem;
        }
        
        /* Modal DataTable responsive adjustments */
        .modal-body-scrollable .dataTables_wrapper .row {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        
        .modal-body-scrollable .dataTables_wrapper .row > div {
            width: 100% !important;
            justify-content: center !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: center !important;
            justify-content: center !important;
        }
        
        .modal-body_scrollable .dataTables_wrapper .row > div {
            display: flex !important;
            align-items: center !important;
            padding: 0 !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: right !important;
            justify-content: flex-end !important;
        }
        
        .modal-body-scrollable .dataTables_length {
            justify-content: flex-start !important;
        }
        
        .modal-body-scrollable .dataTables_filter label,
        .modal-body-scrollable .dataTables_length label {
            margin-bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

    /* Fixed Pagination Section Styling */
    .modal-pagination-fixed {
        flex-shrink: 0;
        background: #f8f9fa !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .modal-pagination-fixed .dataTables_info {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate {
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Tooltip styling for better visibility */
    .tooltip {
        font-size: 0.8rem !important;
    }

    .tooltip-inner {
        max-width: 300px !important;
        text-align: center !important;
        word-wrap: break-word !important;
        background: rgba(0,0,0,0.9) !important;
        border-radius: 6px !important;
        padding: 8px 12px !important;
    }

    /* Mobile responsiveness for site badges */
    @media (max-width: 768px) {
        .site-badge {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
            max-width: 120px !important;
        }
        
        .site-column {
            max-width: 180px !important;
            min-width: 120px !important;
        }
    }

    @media (max-width: 480px) {
        .site-badge {
            font-size: 0.65rem !important;
            padding: 3px 6px !important;
            max-width: 100px !important;
        }
        
        .site-column {
            max-width: 150px !important;
            min-width: 100px !important;
        }
    }

    /* Clickable row styling */
    .table-hover-active {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.08) 0%, rgba(var(--secondary-color-rgb), 0.08) 100%) !important;
        transform: translateX(3px) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }

    .more-sites-badge {
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        margin-left: 2px !important;
    }

    .more-sites-badge:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 15px !important;
        overflow: hidden;
    }

    .modal-header.bg-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
        word-break: break-word;
    }

    .table-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .table-section h6 {
        margin-bottom: 1.5rem !important;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--primary-color);
        color: #2c3e50;
    }

    .modern-table {
        margin-bottom: 0 !important;
        margin-top: 0 !important; /* Remove top margin */
        margin-left: 0 !important; /* Remove left margin */
        margin-right: 0 !important; /* Remove right margin */
        background: white;
        border-radius: 0 !important;
        overflow: visible;
        box-shadow: none;
        border: none;
        width: 100% !important; /* Ensure full width */
        /* Ensure table supports sticky positioning */
        border-collapse: separate;
        border-spacing: 0;
        /* Prevent content from bleeding through sticky header */
        position: relative;
    }

    /* Ensure table body doesn't interfere with sticky header */
    .modern-table tbody {
        position: relative;
        z-index: 1;
        background: white;
    }

    /* Additional layer protection for table rows */
    .modern-table tbody tr {
        position: relative;
        background: white;
        z-index: 1;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        border: none !important;
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .modern-table tbody td {
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        border-bottom: 1px solid #f1f3f5 !important;
        vertical-align: middle !important;
        transition: all 0.3s ease;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.05) 0%, rgba(var(--secondary-color-rgb), 0.05) 100%) !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .sbu-cell {
        color: var(--primary-color) !important;
        font-weight: 700 !important;
        font-size: 1rem;
        position: relative;
    }

    .sites-cell {
        color: #495057;
        font-weight: 500;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .sites-cell .text-muted {
        font-style: italic;
        color: #6c757d !important;
    }

    /* Modal responsive adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
            max-height: calc(100vh - 1rem);
        }
        
        .modal-content {
            height: calc(100vh - 1rem);
            display: flex;
            flex-direction: column;
        }
        
        .modal-body-fixed {
            flex-shrink: 0;
            padding: 1rem !important;
        }
        
        .modal-body-scrollable {
            flex: 1;
            overflow-y: auto;
            max-height: none;
            padding: 0 !important; /* Remove padding to fix sticky header */
        }
        
        /* Add padding to table container instead */
        .modal-body-scrollable .table-container-full-width {
            padding: 0 1rem 1rem 1rem !important;
        }
        
        .modal-footer {
            flex-shrink: 0;
            padding: 0.75rem 1rem !important;
        }
        
        .modal-body-scrollable {
            max-height: 300px;
        }
        
        .info-card {
            padding: 0.75rem;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.75rem 0.5rem !important; /* Further reduce padding for mobile */
            font-size: 0.9rem;
        }
        
        .sbu-cell {
            font-size: 0.9rem;
        }
        
        .sites-cell {
            font-size: 0.85rem;
        }
        
        /* Modal DataTable responsive adjustments */
        .modal-body-scrollable .dataTables_wrapper .row {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        
        .modal-body-scrollable .dataTables_wrapper .row > div {
            width: 100% !important;
            justify-content: center !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: center !important;
            justify-content: center !important;
        }
        
        .modal-body_scrollable .dataTables_wrapper .row > div {
            display: flex !important;
            align-items: center !important;
            padding: 0 !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: right !important;
            justify-content: flex-end !important;
        }
        
        .modal-body-scrollable .dataTables_length {
            justify-content: flex-start !important;
        }
        
        .modal-body-scrollable .dataTables_filter label,
        .modal-body-scrollable .dataTables_length label {
            margin-bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

    /* Fixed Pagination Section Styling */
    .modal-pagination-fixed {
        flex-shrink: 0;
        background: #f8f9fa !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .modal-pagination-fixed .dataTables_info {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate {
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Tooltip styling for better visibility */
    .tooltip {
        font-size: 0.8rem !important;
    }

    .tooltip-inner {
        max-width: 300px !important;
        text-align: center !important;
        word-wrap: break-word !important;
        background: rgba(0,0,0,0.9) !important;
        border-radius: 6px !important;
        padding: 8px 12px !important;
    }

    /* Mobile responsiveness for site badges */
    @media (max-width: 768px) {
        .site-badge {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
            max-width: 120px !important;
        }
        
        .site-column {
            max-width: 180px !important;
            min-width: 120px !important;
        }
    }

    @media (max-width: 480px) {
        .site-badge {
            font-size: 0.65rem !important;
            padding: 3px 6px !important;
            max-width: 100px !important;
        }
        
        .site-column {
            max-width: 150px !important;
            min-width: 100px !important;
        }
    }

    /* Clickable row styling */
    .table-hover-active {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.08) 0%, rgba(var(--secondary-color-rgb), 0.08) 100%) !important;
        transform: translateX(3px) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
    }

    .more-sites-badge {
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        margin-left: 2px !important;
    }

    .more-sites-badge:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 15px !important;
        overflow: hidden;
    }

    .modal-header.bg-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
        word-break: break-word;
    }

    .table-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    .table-section h6 {
        margin-bottom: 1.5rem !important;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid var(--primary-color);
        color: #2c3e50;
    }

    .modern-table {
        margin-bottom: 0 !important;
        margin-top: 0 !important; /* Remove top margin */
        margin-left: 0 !important; /* Remove left margin */
        margin-right: 0 !important; /* Remove right margin */
        background: white;
        border-radius: 0 !important;
        overflow: visible;
        box-shadow: none;
        border: none;
        width: 100% !important; /* Ensure full width */
        /* Ensure table supports sticky positioning */
        border-collapse: separate;
        border-spacing: 0;
        /* Prevent content from bleeding through sticky header */
        position: relative;
    }

    /* Ensure table body doesn't interfere with sticky header */
    .modern-table tbody {
        position: relative;
        z-index: 1;
        background: white;
    }

    /* Additional layer protection for table rows */
    .modern-table tbody tr {
        position: relative;
        background: white;
        z-index: 1;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        border: none !important;
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }

    .modern-table tbody td {
        padding: 1rem 0.75rem !important; /* Reduce horizontal padding */
        border-bottom: 1px solid #f1f3f5 !important;
        vertical-align: middle !important;
        transition: all 0.3s ease;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.05) 0%, rgba(var(--secondary-color-rgb), 0.05) 100%) !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .sbu-cell {
        color: var(--primary-color) !important;
        font-weight: 700 !important;
        font-size: 1rem;
        position: relative;
    }

    .sites-cell {
        color: #495057;
        font-weight: 500;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .sites-cell .text-muted {
        font-style: italic;
        color: #6c757d !important;
    }

    /* Modal responsive adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
            max-height: calc(100vh - 1rem);
        }
        
        .modal-content {
            height: calc(100vh - 1rem);
            display: flex;
            flex-direction: column;
        }
        
        .modal-body-fixed {
            flex-shrink: 0;
            padding: 1rem !important;
        }
        
        .modal-body-scrollable {
            flex: 1;
            overflow-y: auto;
            max-height: none;
            padding: 0 !important; /* Remove padding to fix sticky header */
        }
        
        /* Add padding to table container instead */
        .modal-body-scrollable .table-container-full-width {
            padding: 0 1rem 1rem 1rem !important;
        }
        
        .modal-footer {
            flex-shrink: 0;
            padding: 0.75rem 1rem !important;
        }
        
        .modal-body-scrollable {
            max-height: 300px;
        }
        
        .info-card {
            padding: 0.75rem;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.75rem 0.5rem !important; /* Further reduce padding for mobile */
            font-size: 0.9rem;
        }
        
        .sbu-cell {
            font-size: 0.9rem;
        }
        
        .sites-cell {
            font-size: 0.85rem;
        }
        
        /* Modal DataTable responsive adjustments */
        .modal-body-scrollable .dataTables_wrapper .row {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        
        .modal-body-scrollable .dataTables_wrapper .row > div {
            width: 100% !important;
            justify-content: center !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: center !important;
            justify-content: center !important;
        }
        
        .modal-body_scrollable .dataTables_wrapper .row > div {
            display: flex !important;
            align-items: center !important;
            padding: 0 !important;
        }
        
        .modal-body-scrollable .dataTables_filter {
            text-align: right !important;
            justify-content: flex-end !important;
        }
        
        .modal-body-scrollable .dataTables_length {
            justify-content: flex-start !important;
        }
        
        .modal-body-scrollable .dataTables_filter label,
        .modal-body-scrollable .dataTables_length label {
            margin-bottom: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

    /* Fixed Pagination Section Styling */
    .modal-pagination-fixed {
        flex-shrink: 0;
        background: #f8f9fa !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .modal-pagination-fixed .dataTables_info {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate {
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Password Toggle Button Styling */
    .password-input-group {
        position: relative;
        display: inline-block;
        width: 100%;
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
        /* Ensure button stays aligned with input field only */
        margin-top: 0;
        margin-bottom: 0;
    }

    .password-toggle-btn:hover {
        color: var(--primary-color);
        background-color: rgba(var(--primary-color-rgb), 0.1);
    }

    .password-toggle-btn:focus {
        outline: none;
        color: var(--primary-color);
        background-color: rgba(var(--primary-color-rgb), 0.15);
    }

    .password-toggle-btn:active {
        transform: translateY(-50%) scale(0.95);
    }

    /* Adjust padding for password inputs to accommodate toggle button */
    .password-input-group .form-control {
        padding-right: 50px !important;
        /* Ensure input field has consistent height */
        box-sizing: border-box;
    }

    /* Ensure validation messages don't affect button positioning */
    .password-input-group + .text-danger,
    .password-input-group + .text-success,
    .password-input-group + .text-warning,
    .password-input-group + .invalid-feedback {
        margin-top: 0.25rem;
    }

    /* Fix for dynamically added validation messages */
    .password-input-group ~ .text-danger,
    .password-input-group ~ .text-success,
    .password-input-group ~ .text-warning {
        margin-top: 0.25rem;
        display: block;
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
    
    // Real-time email format validation as user types
    emailField.addEventListener('input', function() {
        const email = this.value.trim();
        
        // Remove any existing feedback
        const existingFeedback = document.getElementById('email-validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Clear validation classes
        this.classList.remove('is-invalid', 'is-valid');
        
        if (email.length > 0) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                // Invalid email format
                emailIsValid = false;
                const warningFeedback = document.createElement('div');
                warningFeedback.className = 'text-warning mt-1';
                warningFeedback.id = 'email-validation-feedback';
                warningFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle me-1"></i>Please enter a valid email format (e.g., user@example.com)</small>';
                this.parentNode.appendChild(warningFeedback);
                this.classList.add('is-invalid');
            } else {
                // Valid email format, but don't check availability yet
                emailIsValid = true;
                const infoFeedback = document.createElement('div');
                infoFeedback.className = 'text-info mt-1';
                infoFeedback.id = 'email-validation-feedback';
                infoFeedback.innerHTML = '<small><i class="bi bi-info-circle me-1"></i>Valid email format. Click outside to check availability.</small>';
                this.parentNode.appendChild(infoFeedback);
                this.classList.add('is-valid');
            }
        } else {
            emailIsValid = false;
        }
    });
    
    emailField.addEventListener('blur', function() {
        const email = emailField.value.trim();
        if (email) {
            // Remove any existing feedback
            const existingFeedback = document.getElementById('email-validation-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            // Clear validation classes
            emailField.classList.remove('is-invalid', 'is-valid');
            
            // First validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                // Invalid email format
                emailIsValid = false;
                const errorFeedback = document.createElement('div');
                errorFeedback.className = 'text-danger mt-1';
                errorFeedback.id = 'email-validation-feedback';
                errorFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Please enter a valid email address</small>';
                emailField.parentNode.appendChild(errorFeedback);
                emailField.classList.add('is-invalid');
                return;
            }
            
            // If email format is valid, check availability via AJAX
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
                        // Email is available and valid format
                        emailIsValid = true;
                        const successFeedback = document.createElement('div');
                        successFeedback.className = 'text-success mt-1';
                        successFeedback.id = 'email-validation-feedback';
                        successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Valid email address and available</small>';
                        emailField.parentNode.appendChild(successFeedback);
                        emailField.classList.add('is-valid');
                    }
                })
                .catch(error => {
                    console.error('Error checking email:', error);
                    // Show error message for network issues
                    emailIsValid = false;
                    const errorFeedback = document.createElement('div');
                    errorFeedback.className = 'text-warning mt-1';
                    errorFeedback.id = 'email-validation-feedback';
                    errorFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle me-1"></i>Unable to verify email availability. Please try again.</small>';
                    emailField.parentNode.appendChild(errorFeedback);
                    emailField.classList.add('is-invalid');
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
                // Insert after the password-input-group div instead of inside it
                passwordConfirmationField.parentNode.parentNode.insertBefore(successFeedback, passwordConfirmationField.parentNode.nextSibling);
                passwordConfirmationField.classList.remove('is-invalid');
                passwordConfirmationField.classList.add('is-valid');
            } else {
                // Passwords don't match
                passwordsMatch = false;
                const errorFeedback = document.createElement('div');
                errorFeedback.className = 'text-danger mt-1';
                errorFeedback.id = 'password-match-feedback';
                errorFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Passwords do not match</small>';
                // Insert after the password-input-group div instead of inside it
                passwordConfirmationField.parentNode.parentNode.insertBefore(errorFeedback, passwordConfirmationField.parentNode.nextSibling);
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
            // Insert after the password-input-group div instead of inside it
            passwordConfirmationField.parentNode.parentNode.insertBefore(warningFeedback, passwordConfirmationField.parentNode.nextSibling);
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

//Modal initialization for more than five sites
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
                render: function(data, type, row) {
                    if (!data || data === 'N/A') return '<span class="text-muted">No site assigned</span>';
                    
                    const siteCount = row.site_count || 0;
                    const sites = row.sites || [];
                    
                    if (siteCount <= 1) {
                        // Show all sites as badges
                        let badgesHtml = '';
                        sites.forEach(site => {
                            badgesHtml += `
                                <span class="badge bg-secondary site-badge me-1 mb-1" 
                                      style="font-size: 0.75rem; padding: 4px 8px;">
                                    ${site.name}
                                </span>
                            `;
                        });
                        return badgesHtml || '<span class="text-muted">No sites</span>';
                    } else {
                        // Show limited sites and make row clickable
                        let limitedBadges = '';
                        sites.slice(0, 1).forEach(site => {
                            limitedBadges += `
                                <span class="badge bg-secondary site-badge me-1 mb-1" 
                                      style="font-size: 0.75rem; padding: 4px 8px;">
                                    ${site.name}
                                </span>
                            `;
                        });
                        
                        return `
                            <div class="sites-display">
                                ${limitedBadges}
                                <span class="badge bg-primary more-sites-badge" 
                                      style="font-size: 0.75rem; padding: 4px 8px; cursor: pointer;"
                                      title="Click to view all ${siteCount} sites">
                                    +${siteCount - 1} more
                                </span>
                            </div>
                        `;
                    }
                },
                className: 'site-column'
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
            
            // Remove any existing event handlers to prevent duplicate events
            $('.table tbody tr').off('click mouseenter mouseleave');
            
            // Add click event handlers for all rows
            $('.table tbody tr').each(function() {
                const row = $(this);
                
                // Make the entire row clickable
                row.css('cursor', 'pointer');
                row.on('click', function(e) {
                    // Prevent default row click if clicking on other interactive elements
                    if ($(e.target).closest('a, button, .btn').length > 0) {
                        return;
                    }
                    
                    const rowData = $('#usersTable').DataTable().row(this).data();
                    showUserDetailsModal(rowData);
                });
                
                // Add hover effect
                row.on('mouseenter', function() {
                    $(this).addClass('table-hover-active');
                }).on('mouseleave', function() {
                    $(this).removeClass('table-hover-active');
                });
            });
            
            // Clean up any orphaned modal backdrops
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('overflow', '');
            $('body').css('padding-right', '');
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

// Global variable to store modal instance and DataTable instance
let userDetailsModalInstance = null;
let modalDataTable = null;

// Function to show user details modal
function showUserDetailsModal(userData) {
    // Clean up any existing modal instances and backdrops
    if (userDetailsModalInstance) {
        userDetailsModalInstance.dispose();
        userDetailsModalInstance = null;
    }
    
    // Destroy existing DataTable if it exists
    if (modalDataTable) {
        modalDataTable.destroy();
        modalDataTable = null;
    }
    
    // Remove any orphaned modal backdrops
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('overflow', '');
    $('body').css('padding-right', '');
    
    // Populate user information
    document.getElementById('modal-user-name').textContent = userData.name || '-';
    document.getElementById('modal-user-email').textContent = userData.email || '-';
    document.getElementById('modal-user-contact').textContent = userData.contact_number || '-';
    document.getElementById('modal-user-created').textContent = userData.created_at || '-';
    
    // Clear and populate SBU and Sites table
    const tableBody = document.getElementById('modal-sbu-sites-table');
    tableBody.innerHTML = '';
    
    const sites = userData.sites || [];
    
    if (sites.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    <i class="bi bi-exclamation-circle me-2"></i>No SBU or site access assigned
                </td>
            </tr>
        `;
    } else {
        // Function to convert SBU acronym to full name
        function getSbuFullName(sbuName) {
            const sbuMapping = {
                'FDC': 'Fast Distribution',
                'FUI': 'Fast Unimerchant'
            };
            return sbuMapping[sbuName] || sbuName || 'Unknown SBU';
        }
        
        // Create one row per site
        sites.forEach((site, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center fw-bold" style="color: var(--primary-color);">
                    ${index + 1}
                </td>
                <td class="fw-bold sbu-cell">
                    ${getSbuFullName(site.sbu_name)}
                </td>
                <td class="sites-cell">
                    ${site.name || 'Unknown Site'}
                </td>
            `;
            tableBody.appendChild(row);
        });
    }
    
    // Create new modal instance
    const modalElement = document.getElementById('userDetailsModal');
    userDetailsModalInstance = new bootstrap.Modal(modalElement, {
        backdrop: true,
        keyboard: true,
        focus: true
    });
    
    // Add event listeners for proper cleanup
    modalElement.addEventListener('hidden.bs.modal', function () {
        // Clean up when modal is hidden
        if (userDetailsModalInstance) {
            userDetailsModalInstance.dispose();
            userDetailsModalInstance = null;
        }
        
        // Destroy DataTable when modal is hidden
        if (modalDataTable) {
            modalDataTable.destroy();
            modalDataTable = null;
        }
        
        // Ensure body classes and styles are cleaned up
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
    }, { once: true }); // Use once: true to prevent multiple listeners
    
    // Show the modal first
    userDetailsModalInstance.show();
    
    // Initialize DataTable after modal is shown and content is populated
    modalElement.addEventListener('shown.bs.modal', function () {
        // Only initialize DataTable if there are sites to display
        if (sites.length > 0) {
            setTimeout(() => {
                modalDataTable = $('#modalUserDetailsTable').DataTable({
                    pageLength: 5,
                    lengthChange: false, // Remove the per page selector
                    responsive: true,
                    ordering: false, // Disable sorting for all columns
                    language: {
                        searchPlaceholder: "Search SBU or Sites...",
                        lengthMenu: "_MENU_ per page",
                        info: "Showing <span class='fw-semibold'>_START_</span> to <span class='fw-semibold'>_END_</span> of <span class='fw-semibold'>_TOTAL_</span> entries",
                        paginate: {
                            first: "<i class='bi bi-chevron-double-left'></i>",
                            last: "<i class='bi bi-chevron-double-right'></i>",
                            next: "<i class='bi bi-chevron-right'></i>",
                            previous: "<i class='bi bi-chevron-left'></i>"
                        },
                        emptyTable: "<div class='text-center py-3'><i class='bi bi-building text-muted fs-4 mb-2'></i><p class='text-muted mb-0'>No SBU or site access found</p></div>",
                        zeroRecords: "<div class='text-center py-3'><i class='bi bi-search text-muted fs-4 mb-2'></i><p class='text-muted mb-0'>No matching SBU or sites found</p></div>"
                    },
                    dom: 'rt<"d-none"<"info-holder"i><"paginate-holder"p>>',
                    initComplete: function() {
                        // Create custom search input in the fixed container
                        const searchContainer = $('#modal-search-container');
                        searchContainer.html(`
                            <div class="d-flex justify-content-end">
                                <div class="search-input-wrapper position-relative">
                                    <input type="text" id="modal-custom-search" class="form-control" 
                                           placeholder="Search SBU or Sites..." 
                                           style="border-radius: 25px; border: 2px solid #e9ecef; padding: 8px 20px 8px 55px; min-width: 250px;">
                                </div>
                            </div>
                        `);
                        
                        // Connect custom search to DataTable
                        $('#modal-custom-search').on('keyup search', function() {
                            modalDataTable.search(this.value).draw();
                        });
                        
                        // Style the custom search input focus
                        $('#modal-custom-search').on('focus', function() {
                            $(this).css({
                                'border-color': 'var(--primary-color)',
                                'box-shadow': '0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25)'
                            });
                        }).on('blur', function() {
                            $(this).css({
                                'border-color': '#e9ecef',
                                'box-shadow': 'none'
                            });
                        });
                        
                        // Responsive adjustments for modal search
                        function adjustModalSearch() {
                            const modalWidth = $('.modal-dialog').width();
                            const searchInput = $('#modal-custom-search');
                            
                            if (modalWidth < 576) {
                                searchInput.css({
                                    'min-width': '200px',
                                    'font-size': '0.85rem',
                                    'padding': '6px 15px 6px 45px'
                                });
                            } else if (modalWidth < 768) {
                                searchInput.css({
                                    'min-width': '220px',
                                    'font-size': '0.9rem',
                                    'padding': '7px 18px 7px 50px'
                                });
                            } else {
                                searchInput.css({
                                    'min-width': '250px',
                                    'font-size': '1rem',
                                    'padding': '8px 20px 8px 55px'
                                });
                            }
                        }
                        
                        // Call on init and window resize
                        adjustModalSearch();
                        $(window).on('resize', adjustModalSearch);
                        
                        // Move pagination controls to fixed containers
                        setTimeout(() => {
                            // Move info to fixed container
                            const infoElement = $('.info-holder .dataTables_info').detach();
                            $('#modal-info-container').append(infoElement);
                            
                            // Move pagination to fixed container
                            const paginateElement = $('.paginate-holder .dataTables_paginate').detach();
                            $('#modal-paginate-container').append(paginateElement);
                            
                            // Style the moved elements
                            $('#modal-info-container .dataTables_info').addClass('mb-0');
                            $('#modal-paginate-container .dataTables_paginate').addClass('mb-0');
                        }, 50);
                    },
                    drawCallback: function() {
                        // Add animation to rows
                        $('.modal-body-scrollable table tbody tr').each(function(index) {
                            $(this).css({
                                'animation-delay': (index * 0.05) + 's',
                                'animation': 'fadeInUp 0.4s ease forwards'
                            });
                        });
                        
                        // Ensure pagination controls stay in fixed containers after redraw
                        setTimeout(() => {
                            // Move info if it's not in the fixed container
                            if ($('.info-holder .dataTables_info').length > 0) {
                                const infoElement = $('.info-holder .dataTables_info').detach();
                                $('#modal-info-container').empty().append(infoElement);
                            }
                            
                            // Move pagination if it's not in the fixed container
                            if ($('.paginate-holder .dataTables_paginate').length > 0) {
                                const paginateElement = $('.paginate-holder .dataTables_paginate').detach();
                                $('#modal-paginate-container').empty().append(paginateElement);
                            }
                            
                            // Style the elements
                            $('#modal-info-container .dataTables_info').addClass('mb-0');
                            $('#modal-paginate-container .dataTables_paginate').addClass('mb-0');
                        }, 10);
                    }
                });
            }, 100); // Small delay to ensure modal is fully rendered
        }
    }, { once: true });
}

function confirmSubmit(event) {
    event.preventDefault();
    
    // Helper function to get user-friendly field labels
    function getFieldLabel(fieldName) {
        const labels = {
            'name': 'Name',
            'email': 'Email',
            'contact_number': 'Contact Number',
            'password': 'Password',
            'password_confirmation': 'Confirm Password'
        };
        return labels[fieldName] || fieldName;
    }
    
    // Clear any existing validation messages first
    document.querySelectorAll('.validation-error-message').forEach(msg => msg.remove());
    document.querySelectorAll('.form-control, .form-select').forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    // Check for required field validation
    const requiredFields = ['name', 'email', 'contact_number', 'password', 'password_confirmation'];
    let hasEmptyFields = false;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && !field.value.trim()) {
            hasEmptyFields = true;
            
            // Add invalid styling
            field.classList.add('is-invalid');
            
            // Create and add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-1 validation-error-message';
            errorDiv.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${getFieldLabel(fieldName)} is required</small>`;
            field.parentNode.appendChild(errorDiv);
        }
    });
    
    // Check SBU selection
    const selectedSbus = document.querySelectorAll('input[name="sbu_ids[]"]:checked');
    const sbuContainer = document.querySelector('.sbu-selection-container');
    if (selectedSbus.length === 0) {
        hasEmptyFields = true;
        sbuContainer.style.border = '2px solid #dc3545';
        sbuContainer.style.borderRadius = '8px';
        sbuContainer.style.padding = '10px';
        
        // Add error message for SBU selection
        const existingError = sbuContainer.querySelector('.validation-error-message');
        if (!existingError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-2 validation-error-message';
            errorDiv.innerHTML = '<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Please select at least one Strategic Business Unit</small>';
            sbuContainer.appendChild(errorDiv);
        }
    } else {
        sbuContainer.style.border = '';
        sbuContainer.style.borderRadius = '';
        sbuContainer.style.padding = '';
    }
    
    // Check site selection
    const siteSelect = document.getElementById('site_ids');
    const selectedSites = siteSelect.selectedOptions;
    const sitesContainer = document.querySelector('.sites-selection-container');
    if (selectedSites.length === 0) {
        hasEmptyFields = true;
        sitesContainer.style.border = '2px solid #dc3545';
        sitesContainer.style.borderRadius = '8px';
        sitesContainer.style.padding = '10px';
        
        // Add error message for site selection
        const existingError = sitesContainer.querySelector('.validation-error-message');
        if (!existingError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-2 validation-error-message';
            errorDiv.innerHTML = '<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Please select at least one site location</small>';
            sitesContainer.appendChild(errorDiv);
        }
    } else {
        sitesContainer.style.border = '';
        sitesContainer.style.borderRadius = '';
        sitesContainer.style.padding = '';
    }
    
    // If there are empty required fields, show error and return
    if (hasEmptyFields) {
        swalWithBootstrapButtons.fire({
            title: "Required Fields Missing",
            text: "Please fill in all required fields and make your selections before submitting.",
            icon: "error",
            confirmButtonText: "OK"
        });
        return false;
    }
    
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
        border: 2px solid #e9ecef;
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
        
        .sbu-selection-container .row.g-3 {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }
        
        .sbu-card {
            min-height: 90px;
            max-height: 110px;
        }
        
        .sbu-card-content {
            padding: 0.75rem;
        }
        
        .sbu-name {
            font-size: 0.9rem;
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
        
        .sbu-name {
            font-size: 1rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
    }

    /* Medium tablets responsive (769px to 820px) - iPad standard */
    @media (min-width: 769px) and (max-width: 820px) {
        .col-lg-3.col-xl-4 {
            flex: 0 0 45%;
            max-width: 45%;
        }
        
        .col-lg-9.col-xl-8 {
            flex: 0 0 55%;
            max-width: 55%;
        }
        
        .card-body {
            padding: 1.75rem !important;
        }
        
        /* Stack email and contact fields vertically on iPad */
        .col-md-7.col-lg-12.col-xl-7,
        .col-md-5.col-lg-12.col-xl-5 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
        
        /* Stack password fields vertically on iPad */
        .row.g-3.mb-4 .col-md-6.col-lg-12.col-xl-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
        
        /* SBU cards single column on standard iPad */
        .sbu-selection-container .col-md-6.col-lg-12.col-xl-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .sbu-card {
            min-height: 90px;
            max-height: 110px;
        }
        
        .sbu-card-content {
            padding: 0.75rem;
        }
        
        .sbu-name {
            font-size: 0.9rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
    }

    /* iPad Pro responsive (821px to 1024px) */
    @media (min-width: 821px) and (max-width: 1024px) {
        /* Adjust main layout for better iPad Pro spacing */
        .col-lg-3.col-xl-4 {
            flex: 0 0 42%;
            max-width: 42%;
        }
        
        .col-lg-9.col-xl-8 {
            flex: 0 0 58%;
            max-width: 58%;
        }
        
        /* Optimize form layout for iPad Pro */
        .card-body {
            padding: 2rem !important;
        }
        
        /* SBU Cards responsive adjustments */
        .sbu-selection-container {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .sbu-selection-container .row.g-3 {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }
        
        .sbu-card {
            min-height: 100px;
            max-height: 120px;
        }
        
        .sbu-card-content {
            padding: 0.875rem;
        }
        
        .sbu-name {
            font-size: 0.95rem;
            margin-bottom: 0.375rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
        
        /* Sites selection optimization */
        .sites-selection-container {
            padding: 1.25rem;
        }
        
        .sites-selection-container .sites-header-container {
            flex-direction: column;
            gap: 0.875rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            flex-direction: row;
            gap: 0.75rem;
            width: 100%;
            justify-content: flex-start;
        }
        
        .sites-selection-container .selection-controls .btn {
            flex: 0 0 auto;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            min-width: 120px;
            margin-bottom: 0;
        }
        
        .sites-selection-container .text-muted {
            width: 100%;
            flex: none;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        /* Form fields optimization */
        .form-control, .form-select {
            padding: 10px 14px !important;
            font-size: 0.9rem;
        }
        
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem !important;
        }
        
        /* Contact information row */
        .row.g-3.mb-4 .col-md-7.col-lg-12.col-xl-7,
        .row.g-3.mb-4 .col-md-5.col-lg-12.col-xl-5 {
            padding-right: 0.75rem;
            padding-left: 0.75rem;
        }
        
        /* Password fields */
        .row.g-3.mb-4 .col-md-6.col-lg-12.col-xl-6 {
            padding-right: 0.75rem;
            padding-left: 0.75rem;
        }
        
        /* Submit button */
        .btn-lg {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }
        
        /* Select2 adjustments for iPad Pro */
        .select2-container--default .select2-selection--multiple {
            min-height: 100px;
            max-height: 140px;
            padding: 0.5rem;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            padding: 4px 8px;
            margin: 2px;
            font-size: 0.85rem;
            border-radius: 6px;
        }
    }

    /* Large iPad Pro responsive (1025px to 1200px) */
    @media (min-width: 1025px) and (max-width: 1200px) {
        /* Fine-tune layout for large iPad Pro */
        .container-fluid {
            padding-left: 2rem;
            padding-right: 2rem;
        }
        
        .card-body {
            padding: 2.5rem !important;
        }
        
        /* Optimize SBU cards for large screen */
        .sbu-selection-container .row.g-3 {
            --bs-gutter-x: 1.25rem;
            --bs-gutter-y: 1.25rem;
        }
        
        .sbu-card {
            min-height: 110px;
        }
        
        /* Form optimization */
        .form-control, .form-select {
            padding: 12px 16px !important;
            font-size: 0.95rem;
        }
        
        .sites-selection-container .selection-controls .btn {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            min-width: 130px;
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
    
    /* Fixed Pagination Section Styling */
    .modal-pagination-fixed {
        flex-shrink: 0;
        background: #f8f9fa !important;
        border-top: 2px solid #e9ecef !important;
    }
    
    .modal-pagination-fixed .dataTables_info {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate {
        margin: 0;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button {
        padding: 6px 12px;
        margin: 0 2px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.current {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
    
    .modal-pagination-fixed .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Password Toggle Button Styling */
    .password-input-group {
        position: relative;
        display: inline-block;
        width: 100%;
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
        /* Ensure button stays aligned with input field only */
        margin-top: 0;
        margin-bottom: 0;
    }

    .password-toggle-btn:hover {
        color: var(--primary-color);
        background-color: rgba(var(--primary-color-rgb), 0.1);
    }

    .password-toggle-btn:focus {
        outline: none;
        color: var(--primary-color);
        background-color: rgba(var(--primary-color-rgb), 0.15);
    }

    .password-toggle-btn:active {
        transform: translateY(-50%) scale(0.95);
    }

    /* Adjust padding for password inputs to accommodate toggle button */
    .password-input-group .form-control {
        padding-right: 50px !important;
        /* Ensure input field has consistent height */
        box-sizing: border-box;
    }

    /* Ensure validation messages don't affect button positioning */
    .password-input-group + .text-danger,
    .password-input-group + .text-success,
    .password-input-group + .text-warning,
    .password-input-group + .invalid-feedback {
        margin-top: 0.25rem;
    }

    /* Fix for dynamically added validation messages */
    .password-input-group ~ .text-danger,
    .password-input-group ~ .text-success,
    .password-input-group ~ .text-warning {
        margin-top: 0.25rem;
        display: block;
    }
`;

document.head.appendChild(style);
</script>
@endsection