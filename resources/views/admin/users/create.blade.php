@extends('layouts.app')

@section('content')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold">{{ __('Add New User') }}</span>
                    <a href="javascript:void(0)" onclick="confirmClose()" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back To Home">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm" onsubmit="return confirmSubmit(event)">
                        @csrf
                        <div class="mb-3">
                            <label for="sbu" class="form-label">SBU</label>
                            <select id="sbu" class="form-select @error('sbu') is-invalid @enderror" name="sbu" required>
                                <option value="" selected disabled>Select SBU</option>
                                <option value="FDC" {{ old('sbu') == 'FDC' ? 'selected' : '' }}>FDC</option>
                                <option value="FUI" {{ old('sbu') == 'FUI' ? 'selected' : '' }}>FUI</option>
                            </select>
                            @error('sbu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="site" class="form-label">Site</label>
                            <select id="site" class="form-select @error('site') is-invalid @enderror" name="site" required>
                                <option value="" selected disabled>Select SBU first</option>
                            </select>
                            @error('site')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create User</button>
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
    
    .card .card-header span {
        color: #ffffff;
    }
    
    .card .card-header .btn-outline-secondary {
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
    }
    
    .card .card-header .btn-outline-secondary:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .form-control:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.25);
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
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-success me-3",
        cancelButton: "btn btn-outline-danger",
        actions: 'gap-2 justify-content-center'
    },
    buttonsStyling: false
});

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
});

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
    
    // Only check for invalid class if the validation has been performed (feedback exists)
    const nameValidationFeedback = document.getElementById('name-validation-feedback');
    if (nameValidationFeedback && nameField.classList.contains('is-invalid')) {
        swalWithBootstrapButtons.fire({
            title: "Invalid Name",
            text: "Please choose a different name that is not already in use.",
            icon: "error",
            confirmButtonText: "OK"
        });
        return false;
    }
    
    swalWithBootstrapButtons.fire({
        title: "Create New User?",
        text: "Please confirm to create a new user!",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, create it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('userForm').submit();
        }
    });
    
    return false;
}
</script>
@endsection