@extends('layouts.app')

@section('content')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">{{ __('Add New Admin') }}</span>
                    <a href="javascript:void(0)" id="closeFormBtn" class="btn btn-outline-light btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back To Home">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.admins.store') }}" id="adminForm">
                        @csrf

                        <div class="row mb-3">
                            <label for="sbu" class="col-md-4 col-form-label text-md-end">{{ __('SBU') }}</label>
                            <div class="col-md-6">
                                <select id="sbu" class="form-select @error('sbu') is-invalid @enderror" name="sbu" required>
                                    <option value="" selected disabled>Select SBU</option>
                                    <option value="FDC" {{ old('sbu') == 'FDC' ? 'selected' : '' }}>FDC</option>
                                    <option value="FUI" {{ old('sbu') == 'FUI' ? 'selected' : '' }}>FUI</option>
                                </select>
                                @error('sbu')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="site" class="col-md-4 col-form-label text-md-end">{{ __('Site') }}</label>
                            <div class="col-md-6">
                                <select id="site" class="form-select @error('site') is-invalid @enderror" name="site" required>
                                    <option value="" selected disabled>Select SBU first</option>
                                </select>
                                @error('site')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-end">{{ __('Contact Number') }}</label>
                            <div class="col-md-6">
                                <input id="contact_number" type="tel" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number') }}" required autocomplete="tel">
                                @error('contact_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" id="submitBtn" class="btn btn-primary">
                                    {{ __('Add Admin') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Theme-specific overrides for this page */
    .card-header {
        background-color: var(--primary-color) !important;
        color: #ffffff !important;
        border-bottom: none;
    }
    
    .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--primary-color), 0.25);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    label {
        font-weight: 500;
        color: var(--text-color);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SBU and Site dropdown relationship
        const sbuSelect = document.getElementById('sbu');
        const siteSelect = document.getElementById('site');
        
        // Define sites for each SBU
        const sites = {
            'FDC': [
                // Camanava Region
                'FDC Bignay - main',
                'FDC Punturin',
                // Bohol Region
                'FDC Tagbilaran - main',
                'FDC Ubay',
                // Leyte Region
                'FDC Tacloban - main',
                'FDC Ormoc',
                'FDC Sogod',
                // Samar Region
                'FDC Calbayog - main',
                'FDC Bogongan',
                'FDC Catarman',
                // Panay Region
                'FDC Roxas - main',
                'FDC Kalibo',
                // Mindanao Region
                'FDC Gensan - main',
                'FDC Koronadal',
                'FDC CDO - main',
                'FDC Valencia',
                'FDC Iligan',
                'FDC RX/RO',
                'FDC Cebu - main',
                'FDC Davao'
            ],
            'FUI': [
                'NAI Cebu - main',
                'NAI Bohol',
                'NAI Iloilo - main',
                'NAI Roxas',
                'NAI Bacolod - main',
                'NAI Dumaguete',
                'NAI Leyte - main',
                'NAI Samar',
                'NAI Borongan',
                'MNC Cebu - main',
                'MNC Bohol',
                'MNC Ozamiz - main',
                'MNC Dipolog',
                'Shell Cebu - main',
                'Shell Bohol',
                'Shell Leyte - main',
                'Shell Samar',
                'Shell Negros - main',
                'Shell Panay'
            ]
        };
        
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
            defaultOption.textContent = selectedSBU ? `Select ${selectedSBU} Site` : 'Select SBU first';
            siteSelect.appendChild(defaultOption);
            
            // If an SBU is selected, populate with corresponding sites
            if (selectedSBU && sites[selectedSBU]) {
                sites[selectedSBU].forEach(site => {
                    const option = document.createElement('option');
                    option.value = site;
                    option.textContent = site;
                    // Check if this option should be selected (for form validation redisplay)
                    if (site === '{{ old("site") }}') {
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

        // Handle form submission with confirmation
        document.getElementById('submitBtn').addEventListener('click', function() {
            // Check if name is valid before proceeding
            if (!nameIsValid) {
                swalWithBootstrapButtons.fire({
                    title: "Name Already Exists",
                    text: "Please use a different name.",
                    icon: "error"
                });
                return;
            }
            
            // Check if email is valid before proceeding
            if (!emailIsValid) {
                swalWithBootstrapButtons.fire({
                    title: "Email Already Exists",
                    text: "Please use a different email address.",
                    icon: "error"
                });
                return;
            }
            
            swalWithBootstrapButtons.fire({
                title: "Confirm Admin Creation",
                text: "Are you sure you want to add this admin?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, add admin!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    document.getElementById('adminForm').submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Admin creation was cancelled",
                        icon: "error"
                    });
                }
            });
        });

        // Handle close button with confirmation
        document.getElementById('closeFormBtn').addEventListener('click', function() {
            swalWithBootstrapButtons.fire({
                title: "Discard Changes?",
                text: "Any unsaved changes will be lost!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, leave page!",
                cancelButtonText: "No, stay here!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to dashboard
                    window.location.href = "{{ route('admin.dashboard') }}";
                }
            });
        });
    });
</script>
@endsection