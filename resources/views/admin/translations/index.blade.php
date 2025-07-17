@php
    $isDeveloper = request()->route()->getName() === 'developer.translations.index';
@endphp

@extends($isDeveloper ? 'layouts.app-no-navbar' : 'layouts.app')

@push('head')
<style>
/* Base styles for both admin and developer */
.dashboard-container {
    min-height: 100vh;
    position: relative;
}

/* Admin-specific styles (light mode) */
.admin-dashboard {
    background: #ffffff;
    color: #333333;
}

/* Developer-specific styles (dark mode) */
.developer-dashboard {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: #ffffff;
}

.developer-dashboard .card-header {
    background: rgba(0, 0, 0, 0.2);
    color: #ffffff;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.developer-dashboard .card-body {
    background: rgba(0, 0, 0, 0.1);
}

.developer-dashboard .form-control,
.developer-dashboard .form-select {
    background-color: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
}

.developer-dashboard .form-control:focus,
.developer-dashboard .form-select:focus {
    background-color: rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
}

.bg-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
    pointer-events: none;
}

.particle {
    position: absolute;
    width: 2px;
    height: 2px;
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.admin-dashboard .particle {
    background: rgba(0, 123, 255, 0.1);
}

.developer-dashboard .particle {
    background: rgba(255, 255, 255, 0.05);
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    50% { transform: translateY(-20px) rotate(10deg); opacity: 0.5; }
}

.developer-dashboard .particle {
    animation: float-dev 8s ease-in-out infinite;
}

@keyframes float-dev {
    0% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
    25% { transform: translateY(-15px) rotate(5deg); opacity: 1; }
    50% { transform: translateY(-30px) rotate(15deg); opacity: 0.5; }
    75% { transform: translateY(-15px) rotate(25deg); opacity: 0.8; }
    100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
}

.dev-card {
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
}

.admin-dashboard .dev-card {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.developer-dashboard .dev-card {
    background: rgba(26, 32, 44, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.developer-dashboard .card {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4), 0 0 15px rgba(0, 123, 255, 0.1);
    transition: all 0.3s ease;
}

.developer-dashboard .card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 123, 255, 0.2);
    transform: translateY(-5px);
}

.dev-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.form-control, .form-select {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.2);
    color: #333333;
}

.form-control:focus, .form-select:focus {
    background: #ffffff;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    color: #333333;
}

.form-control::placeholder {
    color: rgba(0, 0, 0, 0.4);
}

.form-select option {
    background: #ffffff;
    color: #333333;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
}

/* Admin button styles */
.admin-dashboard .btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

/* Developer button styles */
.developer-dashboard .btn-primary {
    background: linear-gradient(135deg, #4a5568, #2d3748);
    color: #ffffff;
    transition: all 0.3s ease;
}

.btn-outline-secondary {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
}

.admin-dashboard .btn-outline-secondary {
    border: 2px solid rgba(0, 0, 0, 0.2);
    color: #333;
    background: transparent;
}

.developer-dashboard .btn-outline-secondary {
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: #e2e8f0;
    background: transparent;
}

.developer-dashboard .btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

.btn-outline-secondary:hover {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.3);
    color: #5a6268;
    transform: translateY(-2px);
}

.btn-outline-primary {
    border: 2px solid #007bff;
    color: #007bff;
    background: transparent;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
}

.btn-outline-danger {
    border: 2px solid #dc3545;
    color: #dc3545;
    background: transparent;
    transition: all 0.3s ease;
}

.btn-outline-danger:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
}

.table {
    color: #333333;
}

.admin-dashboard .table {
    color: #333;
}

.developer-dashboard .table {
    color: #e2e8f0;
}

.developer-dashboard .table thead th {
    border-bottom-color: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

.developer-dashboard .table td {
    border-top-color: rgba(255, 255, 255, 0.1);
}

.developer-dashboard .pagination .page-link {
    background-color: rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
}

.developer-dashboard .pagination .page-item.active .page-link {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

.developer-dashboard .pagination .page-item.disabled .page-link {
    background-color: rgba(0, 0, 0, 0.1);
    border-color: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.5);
.admin-dashboard .table-light {
    background: #f8f9fa;
    color: #333333;
}

.developer-dashboard .table-light {
    background: rgba(0, 0, 0, 0.15);
    color: #e2e8f0;
}

.table-hover tbody tr:hover {
    background: rgba(0, 0, 0, 0.02);
}

.badge {
    padding: 0.5em 0.8em;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 6px;
}

.badge.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.badge.bg-secondary {
    background: #6c757d !important;
    color: white;
}

.header-section {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.header-section h1 {
    color: #007bff;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.header-section p {
    color: rgba(0, 0, 0, 0.6);
    margin-bottom: 0;
}

.text-muted {
    color: rgba(0, 0, 0, 0.6) !important;
}

.text-primary {
    color: #007bff !important;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    color: #333333;
}

.form-label {
    color: #333333;
    font-weight: 500;
}

.pagination .page-link {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    color: #333333;
}

.pagination .page-link:hover {
    background: #f8f9fa;
    border-color: rgba(0, 0, 0, 0.2);
    color: #333333;
}

.pagination .page-item.active .page-link {
    background: #007bff;
    border-color: #007bff;
    color: #ffffff;
}
</style>
@endpush

@section('content')
<div class="bg-particles" id="particles"></div>

@php
    $dashboardClass = $isDeveloper ? 'developer-dashboard' : 'admin-dashboard';
@endphp
<div class="dashboard-container {{ $dashboardClass }}">
    <div class="container-fluid px-4 py-5" style="position: relative; z-index: 10;">
        @if($isDeveloper)
        <div class="mb-4">
            <a href="{{ route('developer.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Developer Dashboard
            </a>
        </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Header Section -->
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-6 fw-bold mb-2">Language Management</h1>
                            <p class="text-muted">Manage your application translations dynamically</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('developer.translations.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Translation
                            </a>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                                <i class="bi bi-globe"></i> Add Language
                            </button>
                            <form method="POST" action="{{ route('developer.translations.clearCache') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Clear Cache
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="dev-card mb-4">
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('developer.translations.index') }}">
                            <div class="row g-4 align-items-end">
                                <div class="col-md-4">
                                    <label for="locale" class="form-label mb-2">Language</label>
                                    <select name="locale" id="locale" class="form-select" onchange="this.form.submit()">
                                        <option value="">All Languages</option>
                                        @foreach($locales as $localeCode => $localeName)
                                            <option value="{{ $localeCode }}" {{ $locale == $localeCode ? 'selected' : '' }}>
                                                {{ $localeName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="search" class="form-label mb-2">Search</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           value="{{ $search }}" placeholder="Search key or value..." 
                                           oninput="debounceSearch(this.value)">
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('developer.translations.index') }}" 
                                           class="btn btn-outline-secondary px-4">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Translations Table -->
                <div class="dev-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Translations 
                            <span class="badge bg-secondary">{{ $translations->total() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($translations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Key</th>
                                            <th>Locale</th>
                                            <th>Value</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($translations as $translation)
                                            <tr>
                                                <td>
                                                    <code class="text-primary">{{ $translation->key }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $translation->translationHeader->locale }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 400px;" title="{{ $translation->value }}">
                                                        {{ $translation->value }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('developer.translations.edit', $translation) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('developer.translations.destroy', $translation) }}" 
                                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this translation?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                @php
                                    $currentFilters = [
                                        'locale' => request('locale', ''),
                                        'search' => request('search', '')
                                    ];
                                @endphp
                                {{ $translations->appends($currentFilters)->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-translate text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">No translations found</h5>
                                <p class="text-muted">Try adjusting your filters or add a new translation.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1" aria-labelledby="addLanguageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal content styling will be handled by dashboard class -->
            <div class="modal-header {{ $isDeveloper ? 'border-dark' : '' }}" style="{{ $isDeveloper ? 'border-bottom: 1px solid rgba(255, 255, 255, 0.1);' : 'border-bottom: 1px solid rgba(0, 0, 0, 0.1);' }}">
                <h5 class="modal-title" id="addLanguageModalLabel">
                    <i class="bi bi-globe me-2"></i>Add New Language
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('developer.translations.addLanguage') }}" id="addLanguageForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="language_name" class="form-label">Language Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="language_name" class="form-control" 
                               placeholder="e.g., English, Spanish, French" required
                               style="background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.2); color: #333333;">
                        <div class="form-text" style="color: rgba(0, 0, 0, 0.6);">The display name for this language</div>
                    </div>
                    <div class="mb-3">
                        <label for="language_locale" class="form-label">Language Code <span class="text-danger">*</span></label>
                        <input type="text" name="locale" id="language_locale" class="form-control" 
                               placeholder="e.g., en, es, fr, de" required maxlength="5"
                               style="background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.2); color: #333333;">
                        <div class="form-text" style="color: rgba(0, 0, 0, 0.6);">ISO language code (2-5 characters)</div>
                    </div>
                </div>
                <div class="modal-footer {{ $isDeveloper ? 'border-dark' : '' }}" style="{{ $isDeveloper ? 'border-top: 1px solid rgba(255, 255, 255, 0.1);' : 'border-top: 1px solid rgba(0, 0, 0, 0.1);' }}">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Add Language
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Create floating particles
function createParticles() {
    const particles = document.getElementById('particles');
    const isDeveloper = document.querySelector('.developer-dashboard') !== null;
    const particleCount = isDeveloper ? 100 : 50; // More particles for developer mode
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        // Random size - larger for developer mode
        const size = isDeveloper ? (Math.random() * 6 + 3) : (Math.random() * 5 + 2);
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.animationDelay = Math.random() * 6 + 's';
        particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
        particles.appendChild(particle);
    }
}

// Debounced search function
let searchTimeout;
function debounceSearch(value) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const form = document.querySelector('form[method="GET"]');
        form.submit();
    }, 500); // 500ms delay
}

// Ensure pagination links maintain filter state
document.addEventListener('DOMContentLoaded', function() {
    createParticles();
    
    // Add current filter parameters to all pagination links
    const paginationLinks = document.querySelectorAll('.pagination a');
    const currentLocale = '{{ request("locale", "") }}';
    const currentSearch = '{{ request("search", "") }}';
    
    paginationLinks.forEach(link => {
        const url = new URL(link.href);
        if (currentLocale !== null) url.searchParams.set('locale', currentLocale);
        if (currentSearch !== null) url.searchParams.set('search', currentSearch);
        link.href = url.toString();
    });
    
    // Handle add language form
    const addLanguageForm = document.getElementById('addLanguageForm');
    const languageLocaleInput = document.getElementById('language_locale');
    
    // Convert locale to lowercase on input
    languageLocaleInput.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/[^a-z]/g, '');
    });
    
    // Reset form when modal is hidden
    const addLanguageModal = document.getElementById('addLanguageModal');
    addLanguageModal.addEventListener('hidden.bs.modal', function() {
        addLanguageForm.reset();
        // Remove any validation classes
        addLanguageForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        addLanguageForm.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    });
});

// Show success/error messages
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; background: rgba(40, 167, 69, 0.9); border: 1px solid rgba(40, 167, 69, 0.3); color: white; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);';
        alert.innerHTML = `
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    });
@endif
</script>
@endsection
