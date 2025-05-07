@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Create New Theme</h2>
                    <p class="text-muted">Define colors, fonts, and styles for your theme</p>
                </div>
                <a href="{{ route('admin.themes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Themes
                </a>
            </div>

            <!-- Theme Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.themes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <!-- Theme Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Theme Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="activate" name="activate" value="1">
                                    <label class="form-check-label" for="activate">
                                        Activate this theme when created
                                    </label>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Color Settings</h5>
                        <div class="row mb-4">
                            <!-- Primary Color -->
                            <div class="col-md-4 mb-3">
                                <label for="primary_color" class="form-label">Primary Color</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control form-control-color me-2 @error('primary_color') is-invalid @enderror"
                                        id="primary_color" name="primary_color" value="{{ old('primary_color', '#4e73df') }}">
                                    <input type="text" class="form-control @error('primary_color') is-invalid @enderror" 
                                        id="primary_color_text" value="{{ old('primary_color', '#4e73df') }}">
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
                                        id="secondary_color" name="secondary_color" value="{{ old('secondary_color', '#858796') }}">
                                    <input type="text" class="form-control @error('secondary_color') is-invalid @enderror" 
                                        id="secondary_color_text" value="{{ old('secondary_color', '#858796') }}">
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
                                        id="accent_color" name="accent_color" value="{{ old('accent_color', '#36b9cc') }}">
                                    <input type="text" class="form-control @error('accent_color') is-invalid @enderror" 
                                        id="accent_color_text" value="{{ old('accent_color', '#36b9cc') }}">
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
                                        id="background_color" name="background_color" value="{{ old('background_color', '#f8f9fc') }}">
                                    <input type="text" class="form-control @error('background_color') is-invalid @enderror" 
                                        id="background_color_text" value="{{ old('background_color', '#f8f9fc') }}">
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
                                        id="text_color" name="text_color" value="{{ old('text_color', '#333333') }}">
                                    <input type="text" class="form-control @error('text_color') is-invalid @enderror" 
                                        id="text_color_text" value="{{ old('text_color', '#333333') }}">
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
                                        <option value="{{ $key }}" {{ old('heading_font') == $key ? 'selected' : '' }} 
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
                                        <option value="{{ $key }}" {{ old('body_font') == $key ? 'selected' : '' }}
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
                            <button type="submit" class="btn btn-primary">Create Theme</button>
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
            });

            // Sync text input to color picker
            textInput.addEventListener('input', () => {
                colorPicker.value = textInput.value;
            });
        });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize font previews
        updateFontPreviews();

        // Set up event listeners
        document.getElementById('heading_font').addEventListener('change', updateFontPreviews);
        document.getElementById('body_font').addEventListener('change', updateFontPreviews);

        // Initialize color sync
        syncColorInputs();
    });
</script>
@endsection
@endsection