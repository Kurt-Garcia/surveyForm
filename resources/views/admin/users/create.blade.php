@extends('layouts.app')

@section('content')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

                    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm" onsubmit="return confirmSubmit(event)">
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
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name..." required>
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
                                       id="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="contact_number" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-telephone me-1 text-danger"></i>Contact Number
                                </label>
                                <input type="tel" class="form-control form-control-lg border-0 shadow-sm @error('contact_number') is-invalid @enderror" 
                                       id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="+63912345678" required>
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
                                       id="password" name="password" placeholder="Enter password..." required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-shield-check me-1 text-secondary"></i>Confirm Password
                                </label>
                                <input type="password" class="form-control form-control-lg border-0 shadow-sm" 
                                       id="password_confirmation" name="password_confirmation" placeholder="Confirm password..." required>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3">
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
        defaultOption.textContent = selectedSBU ? 'Select Site...' : 'Select SBU first...';
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
                        const errorFeedback = document.createElement('div');
                        errorFeedback.className = 'text-danger mt-1';
                        errorFeedback.id = 'email-validation-feedback';
                        errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                        emailField.parentNode.appendChild(errorFeedback);
                        emailField.classList.add('is-invalid');
                    } else {
                        // Email is available
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
    
    // Check if name is valid before submitting
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    
    // Only check for invalid class if the validation has been performed (feedback exists)
    const nameValidationFeedback = document.getElementById('name-validation-feedback');
    const emailValidationFeedback = document.getElementById('email-validation-feedback');
    
    if ((nameValidationFeedback && nameField.classList.contains('is-invalid')) ||
        (emailValidationFeedback && emailField.classList.contains('is-invalid'))) {
        swalWithBootstrapButtons.fire({
            title: "Invalid Input",
            text: "Please fix the validation errors before submitting.",
            icon: "error",
            confirmButtonText: "OK"
        });
        return false;
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

// Add CSS animations
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
`;
document.head.appendChild(style);
</script>
@endsection