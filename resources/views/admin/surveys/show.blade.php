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

                    <!-- Department Logo Section -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h4 class="fw-bold mb-3">Department Logo</h4>
                        <form action="{{ route('admin.surveys.update-department-logo', $survey) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-start gap-4">
                            @csrf
                            @method('PATCH')
                            
                            <div class="logo-preview-container bg-light rounded p-3" style="width: 150px; height: 150px;">
                                @if($survey->department_logo)
                                    <img id="departmentLogoPreview" src="{{ asset('storage/' . $survey->department_logo) }}" alt="Department Logo" class="img-fluid" style="width: 100%; height: 100%; object-fit: contain;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <i class="bi bi-building display-4"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="mb-3">
                                    <input type="file" class="form-control @error('department_logo') is-invalid @enderror" id="department_logo" name="department_logo" accept="image/*">
                                    <small class="text-muted d-block mt-1">Recommended size: 200x200px. Max file size: 2MB</small>
                                    @error('department_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex flex-column flex-sm-row gap-2 w-100 logo-btn-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cloud-upload me-2"></i>Update Department Logo
                                    </button>
                                    @if($survey->department_logo)
                                        <button type="submit" name="remove_department_logo" value="1" class="btn btn-outline-danger ms-0 ms-sm-2 mt-2 mt-sm-0">
                                            <i class="bi bi-trash me-2"></i>Remove Department Logo
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
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">SBU</label>
                                <div class="sbu-selection-container">
                                    @if($sbus->count() > 0)
                                        <p class="text-muted mb-4 fs-6">
                                            @if($sbus->count() == 1)
                                                Select your SBU to deploy this survey:
                                            @else
                                                Select one or more SBUs where you want to deploy this survey:
                                            @endif
                                        </p>
                                        <div class="row g-3 {{ $sbus->count() == 1 ? 'justify-content-center' : '' }}">
                                            @foreach($sbus as $sbu)
                                                <div class="{{ $sbus->count() == 1 ? 'col-md-6 col-lg-4' : 'col-md-6' }}">
                                                    <div class="sbu-card" data-sbu-id="{{ $sbu->id }}">
                                                        <input class="sbu-checkbox d-none" type="checkbox" 
                                                               id="sbu_{{ $sbu->id }}" 
                                                               name="sbu_ids[]" 
                                                               value="{{ $sbu->id }}"
                                                               {{ $survey->sbus->contains($sbu->id) ? 'checked' : '' }}>
                                                        
                                                        <div class="sbu-card-content">
                                                            <div class="sbu-card-header">
                                                                <div class="sbu-check-indicator">
                                                                    <i class="fas fa-check"></i>
                                                                </div>
                                                            </div>
                                                            <div class="sbu-card-body">
                                                                <h5 class="sbu-name">{{ $sbu->name }}</h5>
                                                                <p class="sbu-sites-count">{{ $sbu->sites->count() }} sites available</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning text-center" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>No SBUs Available</strong><br>
                                            <small>You don't have access to any SBU. Please contact your administrator to get access permissions.</small>
                                        </div>
                                    @endif
                                    @error('sbu_ids')
                                        <div class="text-danger mt-3" role="alert">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_ids" class="form-label fw-bold">Deployment Sites</label>
                                <div class="sites-selection-container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="text-muted mb-0 fs-6">Select deployment sites for your survey:</p>
                                        <div class="selection-controls">
                                            <button type="button" id="selectAllSites" class="btn btn-outline-primary btn-sm me-2" disabled>
                                                <i class="fas fa-check-double me-1"></i>Select All
                                            </button>
                                            <button type="button" id="deselectAllSites" class="btn btn-outline-secondary btn-sm" disabled>
                                                <i class="fas fa-times me-1"></i>Deselect All
                                            </button>
                                        </div>
                                    </div>
                                    <select id="site_ids" class="form-select select2 @error('site_ids') is-invalid @enderror" name="site_ids[]" multiple required>
                                        <!-- Sites will be populated via JavaScript -->
                                    </select>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle me-1"></i>You can search and select multiple sites. Use the buttons above for quick selection.
                                    </small>
                                </div>
                                @error('site_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                @if($sbus->count() > 0)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Update Deployment Settings
                                    </button>
                                @else
                                    <div class="alert alert-info text-center w-100" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Cannot update deployment settings without SBU access. Please contact your administrator.
                                    </div>
                                @endif
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

/* Modern SBU Card Styling */
.sbu-selection-container {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f8 100%);
    border-radius: 12px;
    border: 2px solid #e9ecf3;
    transition: all 0.3s ease;
}

.sbu-card {
    background: white;
    border: 2px solid #e9ecf3;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sbu-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.sbu-card:hover::before {
    left: 100%;
}

.sbu-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.sbu-card.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(var(--primary-color-rgb), 0.4);
}

.sbu-card.selected:hover {
    transform: translateY(-3px) scale(1.01);
    box-shadow: 0 10px 30px rgba(var(--primary-color-rgb), 0.5);
}

.sbu-card-content {
    text-align: center;
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 0.75rem;
}

.sbu-card-header {
    position: relative;
    margin-bottom: 0.5rem;
}

.sbu-check-indicator {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    background: var(--secondary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(var(--secondary-color-rgb), 0.3);
}

.sbu-card.selected .sbu-check-indicator {
    opacity: 1;
    transform: scale(1);
}

.sbu-card-body {
    text-align: center;
}

.sbu-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.125rem;
    transition: all 0.3s ease;
}

.sbu-card.selected .sbu-name {
    color: white;
}

.sbu-sites-count {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sbu-card.selected .sbu-sites-count {
    color: rgba(255,255,255,0.9);
}

/* Hover effects for unselected cards */
.sbu-card:not(.selected):hover .sbu-name {
    color: var(--primary-color);
}

.sbu-card:not(.selected):hover .sbu-sites-count {
    color: #4b5563;
}

/* Animation for selection */
@keyframes pulse-select {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.sbu-card.selecting {
    animation: pulse-select 0.3s ease-in-out;
}

/* Single SBU Card Styling - Enhanced centering and size */
.row.justify-content-center .sbu-card {
    max-width: 300px;
    height: 120px;
    margin: 0 auto;
}

.row.justify-content-center .sbu-card-content {
    padding: 1rem;
}

.row.justify-content-center .sbu-name {
    font-size: 1.1rem;
    font-weight: 700;
}

.row.justify-content-center .sbu-sites-count {
    font-size: 0.9rem;
}

/* Sites Selection Container Styling */
.sites-selection-container {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f8 100%);
    border-radius: 12px;
    border: 2px solid #e9ecf3;
    transition: all 0.3s ease;
}

.sites-selection-container:hover {
    border-color: #d1d9e6;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.selection-controls {
    display: flex;
    gap: 0.5rem;
}

.selection-controls .btn {
    font-size: 0.85rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
    min-width: 110px;
}

.selection-controls .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.selection-controls .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.selection-controls .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-color: #28a745;
    color: white;
}

.selection-controls .btn-outline-warning {
    border-color: #ffc107;
    color: #856404;
}

.selection-controls .btn-outline-warning:hover:not(:disabled) {
    background-color: #ffc107;
    color: #212529;
}

/* Enhanced Select2 styling for sites */
.sites-selection-container .select2-container--default .select2-selection--multiple {
    border: 2px solid #e9ecf3;
    border-radius: 8px;
    min-height: 120px;
    background: white;
    transition: all 0.3s ease;
}

.sites-selection-container .select2-container--default .select2-selection--multiple:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.15);
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border: none;
    color: white;
    border-radius: 6px;
    padding: 6px 28px 6px 25px;
    margin: 6px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.8);
    margin-right: 8px;
    border: none;
    font-size: 1.1rem;
    transition: color 0.2s ease;
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: white;
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

.sites-selection-container .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.sites-selection-container .select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 8px;
    font-size: 0.95rem;
}

.sites-selection-container .select2-container--default .select2-search--inline .select2-search__field::placeholder {
    color: #6c757d;
    font-style: italic;
}

/* Responsive design */
@media (max-width: 768px) {
    .sbu-selection-container {
        padding: 1rem;
    }
    
    .sites-selection-container {
        padding: 1rem;
    }
    
    .selection-controls {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .selection-controls .btn {
        width: 100%;
        min-width: auto;
    }
    
    .sbu-card {
        height: 90px;
    }
    
    .sbu-name {
        font-size: 0.95rem;
    }
    
    .sbu-sites-count {
        font-size: 0.75rem;
    }
    
    .sites-selection-container .select2-container--default .select2-selection--multiple {
        min-height: 100px;
    }
    
    /* Single SBU responsive styling */
    .row.justify-content-center .sbu-card {
        max-width: 100%;
        height: 100px;
    }
    
    .row.justify-content-center .sbu-card-content {
        padding: 0.75rem;
    }
    
    .row.justify-content-center .sbu-name {
        font-size: 1rem;
    }
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
    
    // Handle Remove Department Logo button
    const removeDepartmentLogoButton = document.querySelector('button[name="remove_department_logo"]');
    if (removeDepartmentLogoButton) {
        removeDepartmentLogoButton.addEventListener('click', function(e) {
            e.preventDefault();
            const form = e.target.closest('form');
            
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this! This will remove the department logo.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a hidden input for remove_department_logo
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'remove_department_logo';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    
                    // Clear the file input
                    document.getElementById('department_logo').value = '';
                    
                    // Submit the form
                    form.submit();
                    
                    // Show success message
                    swalWithBootstrapButtons.fire({
                        title: "Removed!",
                        text: "Your department logo has been removed.",
                        icon: "success"
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your department logo is safe :)",
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

// Department Logo preview functionality
document.getElementById('department_logo').addEventListener('change', function(e) {
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
            const preview = document.getElementById('departmentLogoPreview');
            const containers = document.querySelectorAll('.logo-preview-container');
            const container = containers[1]; // Second container is for department logo
            
            // Remove placeholder if exists
            const placeholder = container.querySelector('.d-flex');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Create or update preview image
            if (!preview) {
                const img = document.createElement('img');
                img.id = 'departmentLogoPreview';
                img.className = 'img-fluid';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'contain';
                img.alt = 'Department Logo';
                container.appendChild(img);
            } else {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
});

// Department Logo preview functionality
document.getElementById('department_logo').addEventListener('change', function(e) {
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
            const preview = document.getElementById('departmentLogoPreview');
            const container = document.querySelector('.logo-preview-container');
            
            // Remove placeholder if exists
            const placeholder = container.querySelector('.d-flex');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Create or update preview image
            if (!preview) {
                const img = document.createElement('img');
                img.id = 'departmentLogoPreview';
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

// SBU Card Click Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sbuCards = document.querySelectorAll('.sbu-card');
    
    // Initialize card states based on checked checkboxes
    sbuCards.forEach(card => {
        const checkbox = card.querySelector('.sbu-checkbox');
        if (checkbox && checkbox.checked) {
            card.classList.add('selected');
        }
    });
    
    // Add click handlers to SBU cards
    sbuCards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            
            const checkbox = this.querySelector('.sbu-checkbox');
            if (checkbox) {
                // Toggle checkbox state
                checkbox.checked = !checkbox.checked;
                
                // Toggle visual state
                if (checkbox.checked) {
                    this.classList.add('selected', 'selecting');
                    // Remove selecting class after animation
                    setTimeout(() => {
                        this.classList.remove('selecting');
                    }, 300);
                } else {
                    this.classList.remove('selected');
                }
                
                // Trigger change event to update site options
                checkbox.dispatchEvent(new Event('change'));
            }
        });
        
        // Add hover effects
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'translateY(-2px) scale(1.01)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = '';
            }
        });
    });
});

// Deployment form validation
document.addEventListener('DOMContentLoaded', function() {
    const deploymentForm = document.querySelector('form[action*="update-deployment"]');
    if (deploymentForm) {
        deploymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if at least one SBU is selected
            const selectedSBUs = Array.from(document.querySelectorAll('.sbu-checkbox')).filter(checkbox => checkbox.checked);
            if (selectedSBUs.length === 0) {
                swalWithBootstrapButtons.fire({
                    title: "Error!",
                    text: "Please select at least one SBU.",
                    icon: "error"
                });
                return false;
            }

            // Check if at least one deployment site is selected
            const sitesSelect = document.getElementById('site_ids');
            const selectedSites = Array.from(sitesSelect.selectedOptions);
            if (selectedSites.length === 0) {
                swalWithBootstrapButtons.fire({
                    title: "Error!",
                    text: "Please select at least one deployment site.",
                    icon: "error"
                });
                sitesSelect.focus();
                return false;
            }
            
            // Show confirmation dialog
            swalWithBootstrapButtons.fire({
                title: "Update Deployment Settings?",
                text: "Are you sure you want to update the deployment settings for this survey?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, update it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Continue with form submission
                    deploymentForm.submit();
                }
            });
        });
    }
});

// SBU and Site dropdown relationship
document.addEventListener('DOMContentLoaded', function() {
    const sbuCheckboxes = document.querySelectorAll('.sbu-checkbox');
    const sitesSelect = document.getElementById('site_ids');
    
    // Get current survey sites
    const currentSites = @json($survey->sites->pluck('id'));
    
    // Store all sites organized by SBU for client-side filtering
    const sbuSites = {
        @if($sbus->count() > 0)
            @foreach($sbus as $sbu)
                {{ $sbu->id }}: [
                    @foreach($sbu->sites as $site)
                        {
                            id: {{ $site->id }},
                            name: "{{ $site->name }}",
                            sbu_name: "{{ $sbu->name }}",
                            is_main: {{ $site->is_main ? 'true' : 'false' }}
                        },
                    @endforeach
                ],
            @endforeach
        @endif
    };
    
    // Function to update site options based on selected SBUs
    function updateSiteOptions() {
        const selectedSBUs = Array.from(sbuCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => parseInt(checkbox.value));
        
        // Clear current options
        sitesSelect.innerHTML = '';
        
        if (selectedSBUs.length === 0) {
            // No SBUs selected
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.textContent = 'Select SBU first';
            sitesSelect.appendChild(defaultOption);
        } else {
            // Collect all sites from selected SBUs
            let allSites = [];
            selectedSBUs.forEach(sbuId => {
                if (sbuSites[sbuId]) {
                    allSites = allSites.concat(sbuSites[sbuId]);
                }
            });
            
            // Sort sites: main sites first, then alphabetically, with SBU prefix when multiple SBUs selected
            allSites.sort((a, b) => {
                if (a.is_main !== b.is_main) {
                    return a.is_main ? -1 : 1;
                }
                return a.name.localeCompare(b.name);
            });
            
            // Add sites to dropdown
            allSites.forEach(site => {
                const option = document.createElement('option');
                option.value = site.id;
                
                // Add SBU prefix if multiple SBUs are selected
                const siteLabel = selectedSBUs.length > 1 
                    ? `${site.sbu_name} - ${site.name}${site.is_main ? ' (Main)' : ''}` 
                    : `${site.name}${site.is_main ? ' (Main)' : ''}`;
                
                option.textContent = siteLabel;
                
                // Check if this site is currently selected
                if (currentSites.includes(parseInt(site.id))) {
                    option.selected = true;
                }
                
                sitesSelect.appendChild(option);
            });
        }
        
        // Destroy existing Select2 instance if it exists
        if ($(sitesSelect).data('select2')) {
            $(sitesSelect).select2('destroy');
        }
        
        // Initialize Select2
        $(sitesSelect).select2({
            placeholder: selectedSBUs.length > 0 ? 'Select Sites' : 'Select SBU first',
            allowClear: true,
            width: '100%',
            dropdownParent: $(sitesSelect).parent() // Ensure dropdown is properly positioned
        });
        
        // Update Select All/Deselect All button states
        updateSelectionButtons();
    }
    
    // Function to update Select All/Deselect All button states
    function updateSelectionButtons() {
        const selectAllBtn = document.getElementById('selectAllSites');
        const deselectAllBtn = document.getElementById('deselectAllSites');
        const hasSites = sitesSelect.options.length > 0 && !sitesSelect.options[0].disabled;
        
        if (hasSites) {
            selectAllBtn.disabled = false;
            deselectAllBtn.disabled = false;
            selectAllBtn.classList.remove('disabled');
            deselectAllBtn.classList.remove('disabled');
        } else {
            selectAllBtn.disabled = true;
            deselectAllBtn.disabled = true;
            selectAllBtn.classList.add('disabled');
            deselectAllBtn.classList.add('disabled');
        }
    }
    
    // Select All Sites functionality
    document.getElementById('selectAllSites').addEventListener('click', function() {
        if (this.disabled) return;
        
        const allOptions = Array.from(sitesSelect.options).filter(option => !option.disabled);
        const allValues = allOptions.map(option => option.value);
        
        $(sitesSelect).val(allValues).trigger('change');
        
        // Show feedback
        if (allValues.length > 0) {
            const siteCount = allValues.length;
            $(this).html('<i class="fas fa-check me-1"></i>All Selected');
            setTimeout(() => {
                $(this).html('<i class="fas fa-check-double me-1"></i>Select All');
            }, 2000);
        }
    });
    
    // Deselect All Sites functionality
    document.getElementById('deselectAllSites').addEventListener('click', function() {
        if (this.disabled) return;
        
        $(sitesSelect).val(null).trigger('change');
        
        // Show feedback
        $(this).html('<i class="fas fa-check me-1"></i>All Deselected');
        setTimeout(() => {
            $(this).html('<i class="fas fa-times me-1"></i>Deselect All');
        }, 2000);
    });
    
    // Update button states when selection changes
    $(sitesSelect).on('change', function() {
        const selectedCount = $(this).val() ? $(this).val().length : 0;
        const totalCount = Array.from(this.options).filter(option => !option.disabled).length;
        
        const selectAllBtn = document.getElementById('selectAllSites');
        const deselectAllBtn = document.getElementById('deselectAllSites');
        
        // Update Select All button appearance based on selection state
        if (selectedCount === totalCount && totalCount > 0) {
            selectAllBtn.classList.remove('btn-outline-primary');
            selectAllBtn.classList.add('btn-success');
            selectAllBtn.innerHTML = '<i class="fas fa-check me-1"></i>All Selected';
        } else {
            selectAllBtn.classList.remove('btn-success');
            selectAllBtn.classList.add('btn-outline-primary');
            selectAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i>Select All';
        }
        
        // Update Deselect All button appearance
        if (selectedCount > 0) {
            deselectAllBtn.classList.remove('btn-outline-secondary');
            deselectAllBtn.classList.add('btn-outline-warning');
        } else {
            deselectAllBtn.classList.remove('btn-outline-warning');
            deselectAllBtn.classList.add('btn-outline-secondary');
        }
    });
    
    // Initialize site options based on initial checkbox values
    @if($sbus->count() > 0)
        updateSiteOptions();
        
        // Update site options when SBU checkboxes change
        sbuCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSiteOptions);
        });
    @endif
});
</script>
@endsection