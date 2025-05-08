@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Header -->
            <div class="theme-header mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-5 fw-bold mb-0">Edit Theme: {{ $theme->name }}</h2>
                        <p class="text-muted lead">Modify colors, fonts, and styles for this theme</p>
                    </div>
                    <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i>Back to Themes
                    </a>
                </div>
                <div class="header-decoration mt-3"></div>
            </div>

            <!-- Theme Form -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <form action="{{ route('admin.themes.update', $theme->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-5">
                            <!-- Theme Name -->
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label fw-bold fs-5">Theme Name</label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $theme->name) }}" required placeholder="Enter a unique theme name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if(!$theme->is_active)
                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="activate" name="activate" value="1" style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2 fs-5" for="activate">
                                        Activate when updated
                                    </label>
                                </div>
                            </div>
                            @endif
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
                                            id="primary_color" name="primary_color" value="{{ old('primary_color', $theme->primary_color) }}">
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
                                            id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $theme->secondary_color) }}">
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
                                            id="accent_color" name="accent_color" value="{{ old('accent_color', $theme->accent_color) }}">
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
                                            id="background_color" name="background_color" value="{{ old('background_color', $theme->background_color) }}">
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
                                            id="text_color" name="text_color" value="{{ old('text_color', $theme->text_color) }}">
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
                                    <select class="form-select form-select-lg @error('heading_font') is-invalid @enderror" id="heading_font" name="heading_font">
                                        @foreach($fonts as $key => $font)
                                            <option value="{{ $key }}" {{ old('heading_font', $theme->heading_font) == $key ? 'selected' : '' }} 
                                                style="font-family: '{{ $key }}', sans-serif;">
                                                {{ $font }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('heading_font')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-3 p-3 rounded heading-preview-container">
                                        <p class="heading-font-preview mb-0">Heading Font Preview</p>
                                    </div>
                                </div>
                                
                                <!-- Body Font -->
                                <div class="col-md-6">
                                    <label for="body_font" class="form-label">Body Font</label>
                                    <select class="form-select form-select-lg @error('body_font') is-invalid @enderror" id="body_font" name="body_font">
                                        @foreach($fonts as $key => $font)
                                            <option value="{{ $key }}" {{ old('body_font', $theme->body_font) == $key ? 'selected' : '' }}
                                                style="font-family: '{{ $key }}', sans-serif;">
                                                {{ $font }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('body_font')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-3 p-3 rounded body-preview-container">
                                        <p class="body-font-preview mb-0">Body Font Preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-5">
                            <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary btn-lg me-2 px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-check-circle me-2"></i>Update Theme</button>
                        </div>
                    </form>
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
    }
    
    /* Color Settings Container */
    .color-settings-container {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        border-left: 4px solid var(--bs-primary);
    }
    
    /* Font Settings Container */
    .font-settings-container {
        background-color: rgba(var(--bs-info-rgb), 0.05);
        border-left: 4px solid var(--bs-info);
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
    }
    
    .form-control-color {
        height: 60px;
        padding: 5px;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .form-control-color:hover {
        transform: scale(1.02);
    }
    
    /* Font Preview Containers */
    .heading-preview-container, .body-preview-container {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .heading-preview-container:hover, .body-preview-container:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .heading-font-preview {
        font-size: 1.8rem;
        line-height: 1.2;
        font-weight: 600;
    }
    
    .body-font-preview {
        font-size: 1rem;
        line-height: 1.6;
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/webfontloader@1.6.28/webfontloader.min.js"></script>
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

    // Function to update the preview fonts
    function updateFontPreviews() {
        const headingFont = document.getElementById('heading_font').value;
        const bodyFont = document.getElementById('body_font').value;

        document.querySelector('.heading-font-preview').style.fontFamily = `'${headingFont}', sans-serif`;
        document.querySelector('.body-font-preview').style.fontFamily = `'${bodyFont}', sans-serif`;
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize font previews
        updateFontPreviews();

        // Set up event listeners
        document.getElementById('heading_font').addEventListener('change', updateFontPreviews);
        document.getElementById('body_font').addEventListener('change', updateFontPreviews);
    });
</script>
@endsection
@endsection