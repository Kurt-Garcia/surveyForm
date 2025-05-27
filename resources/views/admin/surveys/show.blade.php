@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-start">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <!-- Header Section -->
                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2 class="mb-0 h3 h1-md fw-bold text-primary">{{ strtoupper($survey->title) }}</h2>
                            <p class="text-muted mb-0 d-flex align-items-center flex-wrap gap-3">
                                <span><i class="bi bi-calendar me-2"></i>Created {{ $survey->created_at->format('M d, Y') }}</span>
                                <span class="badge {{ $survey->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                                    <i class="bi bi-circle-fill me-1 small"></i>
                                    {{ $survey->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <form action="{{ route('admin.surveys.toggle-status', $survey) }}" method="POST" class="d-inline" id="toggleSurveyForm">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn {{ $survey->is_active ? 'btn-soft-danger' : 'btn-soft-success' }} btn-sm btn-md" id="toggleSurveyBtn">
                                    <i class="bi {{ $survey->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                    <span class="d-none d-md-inline">{{ $survey->is_active ? 'Pause' : 'Activate' }} Survey</span>
                                    <span class="d-md-none">{{ $survey->is_active ? 'Pause' : 'Activate' }}</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" class="d-inline" id="deleteSurveyForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm btn-md" id="deleteSurveyBtn">
                                    <i class="bi bi-trash me-2"></i>
                                    <span class="d-none d-md-inline">Delete Survey</span>
                                    <span class="d-md-none">Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Logo Upload Section -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h4 class="fw-bold mb-3">Survey Logo</h4>
                        <form action="{{ route('admin.surveys.update-logo', $survey) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-start gap-4">
                            @csrf
                            @method('PATCH')
                            
                            <div class="logo-preview-container bg-light rounded p-3" style="width: 150px; height: 150px;">
                                @if($survey->logo)
                                    <img id="logoPreview" src="{{ asset('storage/' . $survey->logo) }}" alt="Survey Logo" class="img-fluid" style="width: 100%; height: 100%; object-fit: contain;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <i class="bi bi-image display-4"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="mb-3">
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                                    <small class="text-muted d-block mt-1">Recommended size: 200x200px. Max file size: 2MB</small>
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex flex-column flex-sm-row gap-2 w-100 logo-btn-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cloud-upload me-2"></i>Update Logo
                                    </button>
                                    @if($survey->logo)
                                        <button type="submit" name="remove_logo" value="1" class="btn btn-outline-danger ms-0 ms-sm-2 mt-2 mt-sm-0">
                                            <i class="bi bi-trash me-2"></i>Remove Logo
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- SBU and Site Edit Section -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h4 class="fw-bold mb-3">Deployment Settings</h4>
                        <form action="{{ route('admin.surveys.update-deployment', $survey) }}" method="POST" class="d-flex flex-column gap-3">
                            @csrf
                            @method('PATCH')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sbu_id" class="form-label fw-bold">SBU</label>
                                    <select id="sbu_id" class="form-select @error('sbu_id') is-invalid @enderror" name="sbu_id" required>
                                        <option value="" disabled>Select SBU</option>
                                        @foreach(\App\Models\Sbu::all() as $sbu)
                                            <option value="{{ $sbu->id }}" {{ $survey->sbu_id == $sbu->id ? 'selected' : '' }}>{{ $sbu->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sbu_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="site_ids" class="form-label fw-bold">Deployment Sites</label>
                                    <select id="site_ids" class="form-select select2 @error('site_ids') is-invalid @enderror" name="site_ids[]" multiple required>
                                        <!-- Sites will be populated via JavaScript -->
                                    </select>
                                    <small class="text-muted d-block mt-1">You can select multiple deployment sites</small>
                                    @error('site_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Update Deployment Settings
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="fw-bold mb-0">Questions</h3>
                            <div class="badge bg-primary-subtle text-primary rounded-pill">
                                {{ $survey->questions->count() }}
                            </div>
                        </div>
                        <a href="{{ route('admin.surveys.questions.create', $survey) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-2"></i>Add Question
                        </a>
                    </div>

                    @if($survey->questions->count() > 0)
                        <div class="question-list">
                            @foreach($survey->questions as $index => $question)
                                <div class="card border-0 shadow-sm hover-shadow transition mb-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div class="question-content flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="question-number fw-bold text-primary">Q{{ $index + 1 }}</span>
                                                    <span class="badge {{ $question->type === 'star' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info' }} rounded-pill">
                                                        <i class="bi {{ $question->type === 'star' ? 'bi-star-fill' : 'bi bi-circle-fill' }} me-1"></i>{{ ucfirst($question->type) }}
                                                    </span>
                                                    @if($question->required)
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            <i class="bi bi-asterisk me-1"></i>Required
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">
                                                            <i class="bi bi-circle me-1"></i>Optional
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mb-2">{{ $question->text }}</p>
                                                @if($question->description)
                                                    <p class="text-muted mb-0 small">{{ $question->description }}</p>
                                                @endif
                                                @if($question->type === 'multiple_choice' && $question->options)
                                                    <div class="mt-3">
                                                        <div class="row g-2">
                                                            @foreach(json_decode($question->options) as $option)
                                                                <div class="col-md-6">
                                                                    <div class="p-2 rounded bg-light">
                                                                        <i class="bi bi-circle me-2 text-primary"></i>{{ $option }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="question-actions d-flex gap-2">
                                                <a href="{{ route('admin.surveys.questions.edit', [$survey, $question]) }}" 
                                                   class="btn btn-light btn-sm" title="Edit Question">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.surveys.questions.destroy', [$survey, $question]) }}" 
                                      method="POST" class="d-inline delete-question-form" id="deleteQuestionForm{{ $question->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger delete-question-btn" 
                                            title="Delete Question" id="deleteQuestionBtn{{ $question->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-clipboard-plus display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">No questions yet</h5>
                                <p class="text-muted mb-3">Start building your survey by adding some questions</p>
                                <a href="{{ route('admin.surveys.questions.create', $survey) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-2"></i>Add Your First Question
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-left me-2"></i>Back to Surveys
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.2s ease-in-out;
}

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.btn-soft-primary {
    color: #4e73df;
    background-color: rgba(78, 115, 223, 0.1);
    border-color: transparent;
}

.btn-soft-primary:hover {
    color: #fff;
    background-color: #4e73df;
}

.btn-soft-danger {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
    border-color: transparent;
}

.btn-soft-danger:hover {
    color: #fff;
    background-color: #dc3545;
}

.btn-soft-success {
    color: #198754;
    background-color: rgba(25, 135, 84, 0.1);
    border-color: transparent;
}

.btn-soft-success:hover {
    color: #fff;
    background-color: #198754;
}

.bg-primary-subtle {
    background-color: rgba(78, 115, 223, 0.1)!important;
}

.text-primary {
    color: var(--text-color)!important;
}

.question-number {
    display: inline-block;
    min-width: 32px;
    font-size: 1.3rem;
}

.question-content p {
    font-size: 1.1rem;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .h3 {
        font-size: 1.5rem;
    }
}

@media (min-width: 769px) {
    .btn-md {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
    }
    .h1-md {
        font-size: 2.5rem;
    }
}
.logo-btn-group > .btn {
    width: 100%;
}
@media (min-width: 576px) {
    .logo-btn-group {
        flex-direction: row !important;
    }
    .logo-btn-group > .btn {
        width: auto;
    }
}

.form-control:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.25);
}
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// SweetAlert2 configuration
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-success me-3",
        cancelButton: "btn btn-outline-danger",
        actions: 'gap-2 justify-content-center'
    },
    buttonsStyling: false
});

// Delete Survey confirmation
document.addEventListener('DOMContentLoaded', function() {
    // Handle Toggle Survey Status button
    const toggleSurveyForm = document.getElementById('toggleSurveyForm');
    if (toggleSurveyForm) {
        toggleSurveyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const isCurrentlyActive = toggleSurveyForm.querySelector('.btn-soft-danger') !== null;
            const actionText = isCurrentlyActive ? 'pause' : 'activate';
            
            swalWithBootstrapButtons.fire({
                title: `Are you sure?`,
                text: `Do you want to ${actionText} this survey?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: `Yes, ${actionText} it!`,
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)}d!`,
                        text: `Survey has been ${actionText}d successfully.`,
                        icon: "success"
                    });
                    toggleSurveyForm.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "No changes were made to the survey status.",
                        icon: "error"
                    });
                }
            });
        });
    }

    // Handle Delete Survey button
    const deleteSurveyForm = document.getElementById('deleteSurveyForm');
    if (deleteSurveyForm) {
        deleteSurveyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this! This will permanently delete the survey and all associated data.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    });
                    deleteSurveyForm.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your survey is safe :)",
                        icon: "error"
                    });
                }
            });
        });
    }
    
    // Handle Delete Question buttons
    const deleteQuestionForms = document.querySelectorAll('.delete-question-form');
    deleteQuestionForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this! This will permanently delete this question.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    });
                    form.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your question is safe :)",
                        icon: "error"
                    });
                }
            });
        });
    });
    
    // Handle Remove Logo button
    const removeLogoButton = document.querySelector('button[name="remove_logo"]');
    if (removeLogoButton) {
        removeLogoButton.addEventListener('click', function(e) {
            e.preventDefault();
            const form = e.target.closest('form');
            
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this! This will remove the survey logo.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a hidden input for remove_logo
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'remove_logo';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    // Clear the file input
                    document.getElementById('logo').value = '';
                    
                    // Submit the form
                    form.submit();
                    
                    // Show success message
                    swalWithBootstrapButtons.fire({
                        title: "Removed!",
                        text: "Your logo has been removed.",
                        icon: "success"
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your logo is safe :)",
                        icon: "error"
                    });
                }
            });
        });
    }
});

// Logo preview functionality
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            Swal.fire({
                title: "File too large",
                text: "File size must be less than 2MB",
                icon: "error"
            });
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            const container = document.querySelector('.logo-preview-container');
            
            // Remove placeholder if exists
            const placeholder = container.querySelector('.d-flex');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Create or update preview image
            if (!preview) {
                const img = document.createElement('img');
                img.id = 'logoPreview';
                img.className = 'img-fluid';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';
                container.appendChild(img);
            }
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// SBU and Site dropdown relationship
document.addEventListener('DOMContentLoaded', function() {
    const sbuSelect = document.getElementById('sbu_id');
    const sitesSelect = document.getElementById('site_ids');
    
    // Get current survey sites
    const currentSites = @json($survey->sites->pluck('id'));
    
    // Fetch all SBUs and their sites
    fetch('/admin/api/sbus-with-sites')
        .then(response => response.json())
        .then(data => {
            // Transform the data into a structure organized by SBU ID
            const sbuSites = data.reduce((acc, sbu) => {
                acc[sbu.id] = sbu.sites;
                return acc;
            }, {});
            
            // Function to update site options based on selected SBU
            function updateSiteOptions() {
                const selectedSBU = sbuSelect.value;
                
                // Clear current options
                sitesSelect.innerHTML = '';
                
                // If an SBU is selected, populate with corresponding sites
                if (selectedSBU && sbuSites[selectedSBU]) {
                    // Sort sites with main sites first, then alphabetically
                    const sites = [...sbuSites[selectedSBU]].sort((a, b) => {
                        if (a.is_main !== b.is_main) {
                            return a.is_main ? -1 : 1;
                        }
                        return a.name.localeCompare(b.name);
                    });
                    
                    sites.forEach(site => {
                        const option = document.createElement('option');
                        option.value = site.id;
                        option.textContent = site.name + (site.is_main ? ' (Main)' : '');
                        // Check if this site is currently selected
                        if (currentSites.includes(parseInt(site.id))) {
                            option.selected = true;
                        }
                        sitesSelect.appendChild(option);
                    });
                }
                
                // Initialize Select2 if it's available
                if (typeof $.fn.select2 !== 'undefined') {
                    $(sitesSelect).select2({
                        placeholder: 'Select deployment sites',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }
            
            // Update site options when SBU selection changes
            sbuSelect.addEventListener('change', updateSiteOptions);
            
            // Initialize site options based on initial SBU value
            updateSiteOptions();
        })
        .catch(error => {
            console.error('Error fetching SBUs and sites:', error);
        });
});
</script>
@endsection