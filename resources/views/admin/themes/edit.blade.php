@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Edit Theme: {{ $theme->name }}</h2>
                    <p class="text-muted">Modify colors, fonts, and styles for this theme</p>
                </div>
                <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Themes
                </a>
            </div>

            <!-- Theme Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.themes.update', $theme->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <!-- Theme Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Theme Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $theme->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if(!$theme->is_active)
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="activate" name="activate" value="1">
                                    <label class="form-check-label" for="activate">
                                        Activate this theme when updated
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>

                        <h5 class="mb-3">Color Settings</h5>
                        <div class="row mb-4">
                            <!-- Primary Color -->
                            <div class="col-md-4 mb-3">
                                <label for="primary_color" class="form-label">Primary Color</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control form-control-color me-2 @error('primary_color') is-invalid @enderror"
                                        id="primary_color" name="primary_color" value="{{ old('primary_color', $theme->primary_color) }}">
                                    <input type="text" class="form-control @error('primary_color') is-invalid @enderror" 
                                        id="primary_color_text" value="{{ old('primary_color', $theme->primary_color) }}">
                                </div>
                                @error('primary_color')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Secondary Color -->
                            <div class="col-md-4 mb-3">
                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control form-control-color me-2 @error('secondary_color') is-invalid @enderror"
                                        id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $theme->secondary_color) }}">
                                    <input type="text" class="form-control @error('secondary_color') is-invalid @enderror" 
                                        id="secondary_color_text" value="{{ old('secondary_color', $theme->secondary_color) }}">
                                </div>
                                @error('secondary_color')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Accent Color -->
                            <div class="col-md-4 mb-3">
                                <label for="accent_color" class="form-label">Accent Color</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control form-control-color me-2 @error('accent_color') is-invalid @enderror"
                                        id="accent_color" name="accent_color" value="{{ old('accent_color', $theme->accent_color) }}">
                                    <input type="text" class="form-control @error('accent_color') is-invalid @enderror" 
                                        id="accent_color_text" value="{{ old('accent_color', $theme->accent_color) }}">
                                </div>
                                @error('accent_color')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Background Color -->
                            <div class="col-md-6 mb-3">
                                <label for="background_color" class="form-label">Background Color</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control form-control-color me-2 @error('background_color') is-invalid @enderror"
                                        id="background_color" name="background_color" value="{{ old('background_color', $theme->background_color) }}">
                                    <input type="text" class="form-control @error('background_color') is-invalid @enderror" 
                                        id="background_color_text" value="{{ old('background_color', $theme->background_color) }}">
                                </div>
                                @error('background_color')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Text Color -->
                            <div class="col-md-6 mb-3">
                                <label for="text_color" class="form-label">Text Color</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control form-control-color me-2 @error('text_color') is-invalid @enderror"
                                        id="text_color" name="text_color" value="{{ old('text_color', $theme->text_color) }}">
                                    <input type="text" class="form-control @error('text_color') is-invalid @enderror" 
                                        id="text_color_text" value="{{ old('text_color', $theme->text_color) }}">
                                </div>
                                @error('text_color')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="mb-3">Font Settings</h5>
                        <div class="row mb-4">
                            <!-- Heading Font -->
                            <div class="col-md-6 mb-3">
                                <label for="heading_font" class="form-label">Heading Font</label>
                                <select class="form-select @error('heading_font') is-invalid @enderror" id="heading_font" name="heading_font">
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
                                <div class="mt-2">
                                    <p class="heading-font-preview mb-0">Heading Font Preview</p>
                                </div>
                            </div>
                            
                            <!-- Body Font -->
                            <div class="col-md-6 mb-3">
                                <label for="body_font" class="form-label">Body Font</label>
                                <select class="form-select @error('body_font') is-invalid @enderror" id="body_font" name="body_font">
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
                                <div class="mt-2">
                                    <p class="body-font-preview mb-0">Body Font Preview</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Theme</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .heading-font-preview {
        font-size: 1.8rem;
        line-height: 1.2;
        font-weight: 600;
    }
    
    .body-font-preview {
        font-size: 1rem;
        line-height: 1.6;
    }
    
    /* Make color inputs more visible */
    .form-control-color {
        width: 3rem;
        height: 38px;
    }

    /* Preview Styles */
    .preview-container {
        transition: all 0.3s ease;
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
        
        // Update the theme preview
        updateThemePreview();
    }

    // Function to sync color inputs with text inputs
    function syncColorInputs() {
        const colorInputs = [
            'primary_color',
            'secondary_color',
            'accent_color',
            'background_color',
            'text_color',
        ];

        colorInputs.forEach(color => {
            // Get the elements
            const colorPicker = document.getElementById(color);
            const textInput = document.getElementById(`${color}_text`);

            // Sync color picker to text input
            colorPicker.addEventListener('input', () => {
                textInput.value = colorPicker.value;
                updateThemePreview();
            });

            // Sync text input to color picker
            textInput.addEventListener('input', () => {
                colorPicker.value = textInput.value;
                updateThemePreview();
            });
        });
    }

    // Function to update theme preview
    function updateThemePreview() {
        const primaryColor = document.getElementById('primary_color').value;
        const secondaryColor = document.getElementById('secondary_color').value;
        const accentColor = document.getElementById('accent_color').value;
        const backgroundColor = document.getElementById('background_color').value;
        const textColor = document.getElementById('text_color').value;
        const headingFont = document.getElementById('heading_font').value;
        const bodyFont = document.getElementById('body_font').value;
        
        const preview = document.getElementById('theme-preview');
        
        // Update preview styles
        preview.querySelector('.preview-container').style.backgroundColor = backgroundColor;
        preview.querySelector('.preview-container').style.color = textColor;
        
        // Update headings
        const headings = preview.querySelectorAll('.preview-heading, .preview-subheading, .preview-card-title, .preview-label');
        headings.forEach(heading => {
            heading.style.fontFamily = `'${headingFont}', sans-serif`;
            heading.style.color = textColor;
        });
        
        // Update body text
        const bodyTexts = preview.querySelectorAll('.preview-text, .preview-card-text');
        bodyTexts.forEach(text => {
            text.style.fontFamily = `'${bodyFont}', sans-serif`;
            text.style.color = textColor;
        });
        
        // Update buttons
        preview.querySelector('.preview-primary-btn').style.backgroundColor = primaryColor;
        preview.querySelector('.preview-primary-btn').style.borderColor = primaryColor;
        
        preview.querySelector('.preview-outline-btn').style.color = primaryColor;
        preview.querySelector('.preview-outline-btn').style.borderColor = primaryColor;
        
        // Update card
        preview.querySelector('.preview-card-header').style.backgroundColor = primaryColor + '15'; // Light version
        preview.querySelector('.preview-card-header').style.color = primaryColor;
        preview.querySelector('.preview-card').style.borderColor = primaryColor + '30';
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize font previews
        updateFontPreviews();

        // Set up event listeners for font changes
        document.getElementById('heading_font').addEventListener('change', updateFontPreviews);
        document.getElementById('body_font').addEventListener('change', updateFontPreviews);

        // Initialize color sync
        syncColorInputs();
        
        // Initial preview update
        updateThemePreview();
    });
</script>
@endsection
@endsection