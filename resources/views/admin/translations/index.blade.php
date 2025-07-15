<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Translation Management - Developer Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

.developer-dashboard {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 100vh;
    color: white;
    position: relative;
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
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 0.5; }
}

.dev-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
}

.dev-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    color: white;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.form-select option {
    background: #1a1a2e;
    color: white;
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

.btn-outline-secondary {
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    background: transparent;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
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
    color: white;
}

.table-light {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.table-hover tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
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
    background: rgba(108, 117, 125, 0.8) !important;
    color: white;
}

.header-section {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.header-section h1 {
    background: linear-gradient(135deg, #00d4ff, #007bff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.header-section p {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0;
}

.text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
}

.text-primary {
    color: #007bff !important;
}

.card-header {
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}

.form-label {
    color: white;
    font-weight: 500;
}

.pagination .page-link {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
}

.pagination .page-link:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.pagination .page-item.active .page-link {
    background: #007bff;
    border-color: #007bff;
}
</style>
</head>
<body>

<div class="bg-particles" id="particles"></div>

<div class="developer-dashboard">
    <div class="container-fluid px-4 py-5" style="position: relative; z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Header Section -->
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-6 fw-bold mb-2">Translation Management</h1>
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
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); color: white;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                <h5 class="modal-title" id="addLanguageModalLabel">
                    <i class="bi bi-globe me-2"></i>Add New Language
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('developer.translations.addLanguage') }}" id="addLanguageForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="language_name" class="form-label">Language Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="language_name" class="form-control" 
                               placeholder="e.g., English, Spanish, French" required
                               style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: white;">
                        <div class="form-text" style="color: rgba(255, 255, 255, 0.7);">The display name for this language</div>
                    </div>
                    <div class="mb-3">
                        <label for="language_locale" class="form-label">Language Code <span class="text-danger">*</span></label>
                        <input type="text" name="locale" id="language_locale" class="form-control" 
                               placeholder="e.g., en, es, fr, de" required maxlength="5"
                               style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: white;">
                        <div class="form-text" style="color: rgba(255, 255, 255, 0.7);">ISO language code (2-5 characters)</div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
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
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
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
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; background: rgba(40, 167, 69, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(40, 167, 69, 0.3); color: white;';
        alert.innerHTML = `
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 5000);
    });
@endif
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
