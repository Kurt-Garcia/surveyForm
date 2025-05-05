@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-0">Theme Settings</h2>
                    <p class="text-muted">Customize the appearance of your survey application</p>
                </div>
                <a href="{{ route('admin.themes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Theme
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Theme Cards -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
                @foreach($themes as $theme)
                <div class="col">
                    <div class="card theme-card h-100 {{ $theme->is_active ? 'border-primary active' : '' }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">{{ $theme->name }}</h5>
                            @if($theme->is_active)
                                <span class="badge bg-primary">Active</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <!-- Color Preview -->
                            <div class="theme-preview mb-3">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="color-swatch" style="background-color: {{ $theme->primary_color }};"></div>
                                        <div class="color-label">Primary</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="color-swatch" style="background-color: {{ $theme->secondary_color }};"></div>
                                        <div class="color-label">Secondary</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="color-swatch" style="background-color: {{ $theme->accent_color }};"></div>
                                        <div class="color-label">Accent</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Font Info -->
                            <div class="theme-fonts mb-3">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1 small text-muted">Heading Font</p>
                                        <p class="mb-0" style="font-family: '{{ $theme->heading_font }}', sans-serif;">{{ $theme->heading_font }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 small text-muted">Body Font</p>
                                        <p class="mb-0" style="font-family: '{{ $theme->body_font }}', sans-serif;">{{ $theme->body_font }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Theme Actions -->
                            <div class="d-flex flex-column gap-2 mt-3">
                                @if(!$theme->is_active)
                                <form action="{{ route('admin.themes.activate', $theme->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-check-circle me-2"></i>Activate Theme
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.themes.edit', $theme->id) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Theme
                                </a>
                                @if(!$theme->is_active)
                                <form action="{{ route('admin.themes.destroy', $theme->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this theme?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-2"></i>Delete Theme
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Theme Documentation -->
            <div class="card bg-light">
                <div class="card-header">
                    <h4 class="mb-0">How Themes Work</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Color Settings</h5>
                            <ul class="list-unstyled">
                                <li><strong>Primary Color:</strong> Main branding color used for headers and buttons</li>
                                <li><strong>Secondary Color:</strong> Used for secondary elements and hover states</li>
                                <li><strong>Accent Color:</strong> Used for highlights and call-to-action elements</li>
                                <li><strong>Background Color:</strong> The main page background color</li>
                                <li><strong>Text Color:</strong> The color used for body text</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Font Settings</h5>
                            <ul class="list-unstyled">
                                <li><strong>Heading Font:</strong> Used for headings, titles, and important text</li>
                                <li><strong>Body Font:</strong> Used for general content and paragraph text</li>
                            </ul>
                            <h5>Custom CSS</h5>
                            <p>Use custom CSS for advanced styling needs beyond the basic theme settings.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.theme-card {
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
}

.theme-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.theme-card.active {
    box-shadow: 0 0 0 2px var(--primary-color, #4e73df);
}

.color-swatch {
    height: 50px;
    border-radius: 8px;
    margin-bottom: 5px;
}

.color-label {
    font-size: 0.8rem;
    text-align: center;
}

@media (max-width: 768px) {
    .color-swatch {
        height: 40px;
    }
}
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/webfontloader@1.6.28/webfontloader.min.js"></script>
<script>
    // Load all the Google Fonts used in themes
    document.addEventListener('DOMContentLoaded', function() {
        const fontFamilies = [
            @foreach($themes as $theme)
                '{{ $theme->heading_font }}',
                '{{ $theme->body_font }}',
            @endforeach
        ];
        
        // Remove duplicates
        const uniqueFonts = [...new Set(fontFamilies)];
        
        WebFont.load({
            google: {
                families: uniqueFonts
            }
        });
    });
</script>
@endsection
@endsection