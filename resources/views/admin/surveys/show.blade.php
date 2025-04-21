@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <!-- Header Section -->
                <div class="card-header bg-white py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2 class="mb-0 h3 h1-md fw-bold text-primary">{{ $survey->title }}</h2>
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
</style>
@endsection