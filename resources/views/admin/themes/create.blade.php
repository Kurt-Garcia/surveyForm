@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Header -->
            <div class="theme-header mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-5 fw-bold mb-0">Create New Theme</h2>
                        <p class="text-muted lead">Define colors, fonts, and styles for your survey</p>
                    </div>
                    <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary rounded-pill" id="backButton">
                        <i class="bi bi-arrow-left me-2"></i>Back to Themes
                    </a>
                </div>
                <div class="header-decoration mt-3"></div>
            </div>

            <!-- Theme Form -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Form Section -->
                        <div class="col-12 p-5">
                            <form action="{{ route('admin.themes.store') }}" method="POST" id="themeForm">
                                @csrf
                                
                                <div class="row mb-5">
                                    <!-- Theme Name -->
                                    <div class="col-md-8 mb-3">
                                        <label for="name" class="form-label fw-bold fs-5">Theme Name</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-transparent border-end-0">
                                                <i class="bi bi-palette2"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-lg border-start-0 @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name') }}" required 
                                                placeholder="Enter a unique theme name" autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3 d-flex align-items-center">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="activate" name="activate" value="1" style="width: 3em; height: 1.5em;">
                                            <label class="form-check-label ms-2 fs-5" for="activate">
                                                Activate when created
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Color Settings -->
                                <div class="color-settings-container p-4 rounded-4 mb-5">
                                    <h4 class="color-settings-title mb-4"><i class="bi bi-palette-fill me-2"></i>Color Settings</h4>
                                    <div class="row g-4">
                                        <!-- Primary Color -->
                                        <div class="col-md-4">
                                            <div class="color-picker-container">
                                                <label for="primary_color" class="form-label">Primary Color</label>
                                                <input type="color" class="form-control form-control-color w-100 @error('primary_color') is-invalid @enderror"
                                                    id="primary_color" name="primary_color" value="{{ old('primary_color', '#4e73df') }}">
                                                @error('primary_color')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Secondary Color -->
                                        <div class="col-md-4">
                                            <div class="color-picker-container">
                                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                                <input type="color" class="form-control form-control-color w-100 @error('secondary_color') is-invalid @enderror"
                                                    id="secondary_color" name="secondary_color" value="{{ old('secondary_color', '#858796') }}">
                                                @error('secondary_color')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Accent Color -->
                                        <div class="col-md-4">
                                            <div class="color-picker-container">
                                                <label for="accent_color" class="form-label">Accent Color</label>
                                                <input type="color" class="form-control form-control-color w-100 @error('accent_color') is-invalid @enderror"
                                                    id="accent_color" name="accent_color" value="{{ old('accent_color', '#36b9cc') }}">
                                                @error('accent_color')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Background Color -->
                                        <div class="col-md-6">
                                            <div class="color-picker-container">
                                                <label for="background_color" class="form-label">Background Color</label>
                                                <input type="color" class="form-control form-control-color w-100 @error('background_color') is-invalid @enderror"
                                                    id="background_color" name="background_color" value="{{ old('background_color', '#f8f9fc') }}">
                                                @error('background_color')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Text Color -->
                                        <div class="col-md-6">
                                            <div class="color-picker-container">
                                                <label for="text_color" class="form-label">Text Color</label>
                                                <input type="color" class="form-control form-control-color w-100 @error('text_color') is-invalid @enderror"
                                                    id="text_color" name="text_color" value="{{ old('text_color', '#333333') }}">
                                                @error('text_color')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Font Settings -->
                                <div class="font-settings-container p-4 rounded-4 mb-5">
                                    <h4 class="font-settings-title mb-4"><i class="bi bi-type me-2"></i>Font Settings</h4>
                                    <div class="row g-4">
                                        <!-- Heading Font -->
                                        <div class="col-md-6">
                                            <label for="heading_font" class="form-label">Heading Font</label>
                                            <select class="form-select select2 form-select-lg @error('heading_font') is-invalid @enderror" id="heading_font" name="heading_font">
                                                @foreach($fonts as $key => $font)
                                                    <option value="{{ $key }}" {{ old('heading_font') == $key ? 'selected' : '' }} 
                                                        style="font-family: '{{ $key }}', sans-serif;">
                                                        {{ $font }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('heading_font')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Body Font -->
                                        <div class="col-md-6">
                                            <label for="body_font" class="form-label">Body Font</label>
                                            <select class="form-select select2 form-select-lg @error('body_font') is-invalid @enderror" id="body_font" name="body_font">
                                                @foreach($fonts as $key => $font)
                                                    <option value="{{ $key }}" {{ old('body_font') == $key ? 'selected' : '' }}
                                                        style="font-family: '{{ $key }}', sans-serif;">
                                                        {{ $font }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('body_font')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-5">
                                    <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary btn-lg me-2 px-4" id="cancelButton">Cancel</a>
                                    <button type="button" class="btn btn-primary btn-lg px-5" id="createThemeButton"><i class="bi bi-check-circle me-2"></i>Create Theme</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Header Decoration */
    .header-decoration {
        height: 5px;
        background: linear-gradient(to right, var(--bs-primary), var(--bs-info));
        border-radius: 5px;
        width: 100px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 0.7; }
        50% { opacity: 1; }
        100% { opacity: 0.7; }
    }
    
    /* Color Settings Container */
    .color-settings-container {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        border-left: 4px solid var(--bs-primary);
        transition: all 0.3s ease;
    }
    
    .color-settings-container:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    /* Font Settings Container */
    .font-settings-container {
        background-color: rgba(var(--bs-info-rgb), 0.05);
        border-left: 4px solid var(--bs-info);
        transition: all 0.3s ease;
    }
    
    .font-settings-container:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    /* Color Picker Styling */
    .color-picker-container {
        position: relative;
        border-radius: 10px;
        padding: 15px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .color-picker-container:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .form-control-color {
        height: 50px;
        padding: 5px;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s ease;
        opacity: 0.9;
    }
    
    .form-control-color:hover {
        transform: scale(1.02);
        opacity: 1;
    }
    

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        height: 48px;
        padding: 10px 15px;
        border-radius: 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/webfontloader@1.6.28/webfontloader.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Load Google Fonts
    WebFont.load({
        google: {
            families: [
                @foreach($fonts as $key => $font)
                    '{{ $key }}',
                @endforeach
            ]
        }
    });

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        
        // Set up event listeners for fonts
        // (Font preview handled by Select2)
        
        // Initialize Select2
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                templateResult: formatFontOption,
                templateSelection: formatFontOption
            });
        }
    });
    
    // Format font options to show actual font
    function formatFontOption(option) {
        if (!option.id) return option.text;
        return $('<span>').text(option.text).css('font-family', `'${option.id}', sans-serif`);
    }

    // SweetAlert2 Configuration
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success me-3",
            cancelButton: "btn btn-outline-danger",
            actions: 'gap-2 justify-content-center'
        },
        buttonsStyling: false
    });

    // Handle Create Theme button
    document.addEventListener('DOMContentLoaded', function() {
        // Create Theme Button
        const createThemeButton = document.getElementById('createThemeButton');
        if (createThemeButton) {
            createThemeButton.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('themeForm');
                
                swalWithBootstrapButtons.fire({
                    title: "Create New Theme?",
                    text: "This will create a new theme with your selected settings",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Yes, create it!",
                    cancelButtonText: "Cancel",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }

        // Cancel Button and Back Button (they perform the same action)
        const navigationButtons = document.querySelectorAll('#cancelButton, #backButton');
        navigationButtons.forEach(button => {
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    
                    swalWithBootstrapButtons.fire({
                        title: "Discard changes?",
                        text: "Any unsaved changes will be lost",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, discard changes",
                        cancelButtonText: "No, continue editing",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = href;
                        }
                    });
                });
            }
        });
    });
</script>
@endsection
@endsection