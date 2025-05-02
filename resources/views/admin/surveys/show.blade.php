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
                            <form action="{{ route('admin.surveys.toggle-status', $survey) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn {{ $survey->is_active ? 'btn-soft-danger' : 'btn-soft-success' }} btn-sm btn-md">
                                    <i class="bi {{ $survey->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                    <span class="d-none d-md-inline">{{ $survey->is_active ? 'Pause' : 'Activate' }} Survey</span>
                                    <span class="d-md-none">{{ $survey->is_active ? 'Pause' : 'Activate' }}</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm btn-md"
                                    onclick="return confirm('Are you sure you want to delete this survey? This action cannot be undone.')">
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
                                        <button type="submit" name="remove_logo" value="1" class="btn btn-outline-danger ms-0 ms-sm-2 mt-2 mt-sm-0" onclick="return confirm('Are you sure you want to remove the logo?')">
                                            <i class="bi bi-trash me-2"></i>Remove Logo
                                        </button>
                                    @endif
                                </div>
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
                                                    <span class="badge bg-info-subtle text-info rounded-pill">
                                                        <i class="bi bi-tag-fill me-1"></i>{{ ucfirst($question->type) }}
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
                                                <h5 class="mb-2">{{ $question->text }}</h5>
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
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-light btn-sm text-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this question?')"
                                                            title="Delete Question">
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
    color: #4e73df!important;
}

.question-number {
    display: inline-block;
    min-width: 28px;
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
</style>

<script>
// Logo preview functionality
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) { // 2MB limit
            alert('File size must be less than 2MB');
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
</script>
@endsection