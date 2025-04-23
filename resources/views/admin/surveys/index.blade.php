@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0 text-gray-800">{{ __('My Surveys') }}</h2>
                    <p class="text-muted small mb-0">Manage and monitor your survey collection</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('Create Survey') }}
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Surveys Grid -->
            <div class="row g-4">
                @forelse ($surveys as $survey)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm survey-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-truncate mb-0" title="{{ $survey->title }}">
                                        {{ $survey->title }}
                                    </h5>
                                    <span class="badge {{ $survey->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                                        <i class="fas fa-circle me-1"></i>
                                        {{ $survey->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                
                                <div class="d-flex gap-3 mb-3">
                                    <div class="small text-muted">
                                        <i class="bi bi-question-circle me-1"></i>
                                        {{ $survey->questions->count() }} Questions
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $survey->admin->name }}
                                    </div>
                                </div>

                                <div class="small text-muted mb-3">
                                    <i class="bi bi-calendar me-1"></i>
                                    Created {{ $survey->created_at->format('M d, Y') }}
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.surveys.show', $survey) }}" 
                                       class="btn btn-outline-primary btn-sm flex-grow-1">
                                        <i class="bi bi-eye me-1"></i>{{ __('View Details') }}
                                    </a>
                                    <a href="{{ route('admin.surveys.responses.index', $survey) }}" 
                                       class="btn btn-outline-info btn-sm flex-grow-1">
                                        <i class="bi bi-bar-chart me-1"></i>{{ __('Responses') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-clipboard-x display-4 text-muted mb-3"></i>
                                <h5>{{ __('No surveys found') }}</h5>
                                <p class="text-muted mb-0">Start by creating your first survey</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $surveys->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .survey-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>
@endsection