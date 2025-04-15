@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0 display-6 fw-bold text-primary">{{ $survey->title }}</h2>
                        <p class="text-muted mb-0"><i class="fas fa-calendar-alt me-2"></i>Created {{ $survey->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Survey
                        </a>
                        <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" 
                                onclick="return confirm('Are you sure you want to delete this survey?')">
                                <i class="fas fa-trash-alt me-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h3 class="mb-4 fw-bold">
                        <i class="fas fa-questions me-2"></i>Questions
                        <span class="badge bg-primary rounded-pill ms-2">{{ $survey->questions->count() }}</span>
                    </h3>

                    @if($survey->questions->count() > 0)
                        <div class="list-group">
                            @foreach($survey->questions as $question)
                                <div class="list-group-item list-group-item-action border-0 mb-3 rounded shadow-sm hover-shadow transition">
                                    <div class="d-flex justify-content-between align-items-center p-3">
                                        <div>
                                            <h5 class="mb-1 fw-bold">{{ $question->text }}</h5>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-tag me-1"></i>{{ ucfirst($question->type) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-question fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No questions added to this survey yet.</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Surveys
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.transition {
    transition: all .3s ease;
}
</style>
@endsection