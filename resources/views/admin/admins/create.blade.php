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
        // SweetAlert2 configuration
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });

        // Handle form submission with confirmation
        document.getElementById('submitBtn').addEventListener('click', function() {
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