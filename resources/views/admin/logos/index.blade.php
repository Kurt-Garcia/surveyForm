@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 mb-5 animate__animated animate__fadeIn">
                <div class="card-header bg-primary text-white py-4 d-flex justify-content-between align-items-center rounded-top-4 border-0">
                    <h3 class="mb-0 fw-bold"><i class="bi bi-image me-2"></i>Manage Logos</h3>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm" id="closeFormBtn">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success animate__animated animate__fadeInDown">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger animate__animated animate__fadeInDown">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('admin.logos.store') }}" method="POST" enctype="multipart/form-data" class="mb-5">
                        @csrf
                        <div class="mb-4">
                            <label for="logo" class="form-label fw-semibold">Upload Logo</label>
                            <input type="file" class="form-control form-control-lg @error('logo') is-invalid @enderror" id="logo" name="logo" required>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Logo Name <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" id="uploadLogoBtn" class="btn btn-gradient-blue btn-lg px-5 shadow-sm">Upload <i class="bi bi-upload ms-2"></i></button>
                    </form>
                    <h5 class="fw-bold mb-4" style="color: var(--text-color)">Available Logos</h5>
                    <div class="table-responsive animate__animated animate__fadeInUp">
                        <table class="table table-hover table-borderless align-middle rounded-3 overflow-hidden">
                            <thead class="bg-light-blue text-primary">
                                <tr>
                                    <th class="py-3">Preview</th>
                                    <th class="py-3">Name</th>
                                    <th class="py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logos as $logo)
                                    <tr class="align-middle">
                                        <td><img src="{{ asset('storage/' . $logo->file_path) }}" alt="Logo" class="rounded shadow-sm border border-2 border-light" style="max-width: 90px; max-height: 60px; background: #f8f9fa;"></td>
                                        <td class="fw-semibold">{{ $logo->name ?? '-' }}</td>
                                        <td>
                                            @if($logo->is_active)
                                                <span class="badge bg-gradient-green text-white px-3 py-2 fs-6">Active</span>
                                            @else
                                                <form action="{{ route('admin.logos.activate', $logo->id) }}" method="POST" style="display:inline-block;" class="activate-logo-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-gradient-blue me-2 activate-logo-btn">Activate</button>
                                                </form>
                                                <form action="{{ route('admin.logos.destroy', $logo->id) }}" method="POST" style="display:inline-block;" class="delete-logo-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-logo-btn">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert2 Configuration
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });

        // Close Form Button
        document.getElementById('closeFormBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "Any unsaved changes will be lost!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, close it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // Upload Logo Button
        const uploadBtn = document.getElementById('uploadLogoBtn');
        const logoForm = document.querySelector('form[action*="logos.store"]');
        if (uploadBtn && logoForm) {
            uploadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const fileInput = document.getElementById('logo');
                if (fileInput && fileInput.files.length > 0) {
                    swalWithBootstrapButtons.fire({
                        title: "Upload this logo?",
                        text: "The logo will be available for use in your surveys",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, upload it!",
                        cancelButtonText: "Cancel",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            logoForm.submit();
                        }
                    });
                } else {
                    // If no file selected, trigger the default HTML5 validation
                    logoForm.reportValidity();
                }
            });
        }

        // Activate Logo Buttons
        const activateBtns = document.querySelectorAll('.activate-logo-btn');
        activateBtns.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                swalWithBootstrapButtons.fire({
                    title: "Activate this logo?",
                    text: "This will set this logo as the active logo for all surveys",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, activate it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Delete Logo Buttons
        const deleteBtns = document.querySelectorAll('.delete-logo-btn');
        deleteBtns.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                
                swalWithBootstrapButtons.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this! This logo will be permanently deleted.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<style>
.bg-gradient-blue {
    background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%) !important;
    color: #fff !important;
}
.bg-gradient-green {
    background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%) !important;
    color: #fff !important;
}
.btn-gradient-blue {
    background: var(--primary-color) !important;
    color: #fff !important;
    border: none;
    transition: box-shadow 0.2s, transform 0.2s;
}
.btn-gradient-blue:hover, .btn-gradient-blue:focus {
    background: var(--secondary-color) !important;
    box-shadow: 0 4px 16px rgba(var(--secondary-color-rgb), 0.15);
    transform: translateY(-2px) scale(1.03);
}
.btn-outline-gradient-blue {
    border: 2px solid #2563eb;
    color: #2563eb;
    background: transparent;
    transition: background 0.2s, color 0.2s;
}
.btn-outline-gradient-blue:hover, .btn-outline-gradient-blue:focus {
    background: var(--secondary-color) !important;
    color: #fff !important;
}
.bg-light-blue {
    background: #e0e7ff !important;
}
.animate__animated {animation-duration: 0.7s;}
.form-control:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.25);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
</style>
@endsection