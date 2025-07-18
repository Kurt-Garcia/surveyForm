@extends('layouts.app')

@push('head')
<style>
/* Admin dashboard styles */
.dashboard-container {
    min-height: 100vh;
    position: relative;
    background: #ffffff;
    color: #333333;
}



.card {
    border-radius: 16px;
    transition: all 0.3s ease;
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.card:hover {
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

.btn-outline-secondary {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    border: 2px solid rgba(0, 0, 0, 0.2);
    color: #333;
    background: transparent;
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

.table-light {
    background: #f8f9fa;
    color: #333333;
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
<div class="dashboard-container">
    <div class="container-fluid px-4 py-5">
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
                            <a href="{{ route('admin.translations.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Translation
                            </a>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                                <i class="bi bi-globe"></i> Add Language
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#deployLanguageModal">
                                <i class="bi bi-globe2"></i> Deploy Language
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('admin.translations.index') }}">
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
                                        <a href="{{ route('admin.translations.index') }}" 
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
                <div class="card">
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
                                                        <a href="{{ route('admin.translations.edit', $translation) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.translations.destroy', $translation) }}" 
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
            <div class="modal-header">
                <h5 class="modal-title" id="addLanguageModalLabel">
                    <i class="bi bi-globe me-2"></i>Add New Language
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.translations.addLanguage') }}" id="addLanguageForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="language_name" class="form-label">Language Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="language_name" class="form-control" 
                               placeholder="e.g., English, Spanish, French" required>
                        <div class="form-text">The display name for this language</div>
                    </div>
                    <div class="mb-3">
                        <label for="language_locale" class="form-label">Language Code <span class="text-danger">*</span></label>
                        <input type="text" name="locale" id="language_locale" class="form-control" 
                               placeholder="e.g., en, es, fr, de" required maxlength="5">
                        <div class="form-text">ISO language code (2-5 characters)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Add Language
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deploy Language Modal -->
<div class="modal fade" id="deployLanguageModal" tabindex="-1" aria-labelledby="deployLanguageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deployLanguageModalLabel">
                    <i class="bi bi-globe2 me-2"></i>Deploy Languages
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.translations.deployLanguages') }}" id="deployLanguageForm">
                @csrf
                @php
                    $allLanguages = \App\Models\TranslationHeader::all();
                    $activeLanguages = \App\Models\TranslationHeader::active()->pluck('id')->toArray();
                    $englishLanguage = $allLanguages->where('locale', 'en')->first();
                @endphp
                @if($englishLanguage)
                    <input type="hidden" name="languages[]" value="{{ $englishLanguage->id }}">
                @endif
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Select exactly 2 additional languages to activate.</strong> English is always active as the default language. Only the selected languages will be available for use in the application.
                    </div>
                    
                    <div class="row">
                        
                        @foreach($allLanguages as $language)
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input language-checkbox" 
                                           type="checkbox" 
                                           name="languages[]" 
                                           value="{{ $language->id }}" 
                                           id="language_{{ $language->id }}"
                                           {{ in_array($language->id, $activeLanguages) ? 'checked' : '' }}
                                           {{ $language->locale === 'en' ? 'disabled checked' : '' }}>
                                    <label class="form-check-label d-flex justify-content-between align-items-center" for="language_{{ $language->id }}">
                                        <span>
                                            <strong>{{ $language->name }}</strong>
                                            <small class="text-muted">({{ $language->locale }})</small>
                                            @if($language->locale === 'en')
                                                <small class="text-info">(Default)</small>
                                            @endif
                                        </span>
                                        @if(in_array($language->id, $activeLanguages))
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div id="selectionError" class="alert alert-danger d-none">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Please select exactly 2 additional languages (English is always included).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="deployBtn">
                        <i class="bi bi-check-circle"></i> Deploy Languages
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

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
    
    // Deploy Language Modal functionality
    const deployLanguageForm = document.getElementById('deployLanguageForm');
    const languageCheckboxes = document.querySelectorAll('.language-checkbox');
    const selectionError = document.getElementById('selectionError');
    const deployBtn = document.getElementById('deployBtn');
    
    function validateLanguageSelection() {
        const checkedBoxes = document.querySelectorAll('.language-checkbox:checked:not(:disabled)');
        const englishCheckbox = document.querySelector('.language-checkbox[id*="language_"]:disabled');
        const totalSelected = checkedBoxes.length + (englishCheckbox ? 1 : 0);
        const isValid = totalSelected === 3;
        
        if (isValid) {
            selectionError.classList.add('d-none');
            deployBtn.disabled = false;
        } else {
            selectionError.classList.remove('d-none');
            deployBtn.disabled = true;
        }
        
        return isValid;
    }
    
    // Add event listeners to checkboxes
    languageCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateLanguageSelection);
    });
    
    // Validate on form submission
    deployLanguageForm.addEventListener('submit', function(e) {
        if (!validateLanguageSelection()) {
            e.preventDefault();
        }
    });
    
    // Initial validation
    validateLanguageSelection();
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
