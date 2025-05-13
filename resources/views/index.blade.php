@extends('layouts.app-user')

@section('title', 'Welcome to Survey Form')

@section('content')
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="hero-section">
        <div class="pattern-overlay"></div>
        <div class="container position-relative">
            <header class="welcome-header text-center">
                <h1 class="display-4 fw-bold animate-text">Share Your Thoughts</h1>
                <p class="lead animate-text-delay">Your opinion matters! Select a category below to get started.</p>
            </header>
        </div>
    </div>

    <div class="container survey-container mt-4">
        <div class="search-container mb-4 d-flex justify-content-end">
            <form id="search-form" action="{{ route('index') }}" method="GET" class="w-100 d-flex justify-content-end">
                <div class="input-group search-modern" style="max-width: 400px;">
                    <input type="text" id="survey-search" name="search" class="form-control search-input-modern" placeholder="Search surveys..." value="{{ request('search') }}">
                    <button class="btn btn-search-modern" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="surveys-grid">
            @forelse($surveys as $survey)
                @php
                    $hasResponded = session('account_name') ? App\Models\SurveyResponseHeader::hasResponded($survey->id, session('account_name')) : false;
                    $responseCount = App\Models\SurveyResponseHeader::where('survey_id', $survey->id)->count();
                @endphp
                <div class="col">
                    <div class="card h-100 survey-card shadow-sm hover-lift">
                        <div class="card-body">
                            <div class="survey-logo-wrapper text-center mb-3">
                                @if($survey->logo)
                                    <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo-large">
                                @else
                                    <i class="fas fa-poll fa-2x"></i>
                                @endif
                            </div>
                            <h4 class="card-title">{{ strtoupper($survey->title) }}</h4>
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="survey-info">
                                    <div class="text-muted mb-2">
                                        <i class="fas fa-question-circle me-1"></i>
                                        {{ $survey->questions->count() }} questions
                                    </div>
                                    <div class="text-muted">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        {{ $responseCount }} responses
                                    </div>
                                </div>
                                @if($hasResponded)
                                <div class="responded-badge">
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('surveys.show', $survey) }}" class="btn btn-start btn-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> View Survey
                                </a>
                                <a href="{{ route('surveys.responses.index', $survey) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card empty-state py-5 text-center">
                        <div class="card-body">
                            <i class="fas fa-clipboard-question fs-1 mb-3 text-muted"></i>
                            <h4 class="mb-2">No Surveys Available</h4>
                            <p class="text-muted">Check back soon for new surveys!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="pagination-container mt-4 d-flex justify-content-between align-items-center">
            @if($surveys->hasPages())
            <div class="pagination-info">
                Showing {{ $surveys->firstItem() }} to {{ $surveys->lastItem() }} of {{ $surveys->total() }} surveys
            </div>
            @endif
            {{ $surveys->links() }}
        </div>
    </div>

    <style>
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        min-width: 300px;
    }
    
    /* Modern Search Styles */
    .search-modern {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .search-modern:hover {
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .search-input-modern {
        border: none;
        padding: 12px 20px;
        background: #f8f9fa;
    }
    
    .search-input-modern:focus {
        background: white;
        box-shadow: none;
    }
    
    .btn-search-modern {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0 20px;
        transition: all 0.3s ease;
    }
    
    .btn-search-modern:hover {
        background: var(--primary-color);
        opacity: 0.9;
        transform: scale(1.05);
    }
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }
    .pagination-info {
        font-size: 0.9rem;
        color: #666;
    }
    .pagination-container .pagination {
        margin: 0;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .pagination .page-link {
        color: #333;
        transition: all 0.2s ease;
    }
    .pagination .page-link:hover {
        background-color: #eee;
    }
    
    /* Card Styles */
    .survey-card {
        border-radius: 12px;
        border: none;
        transition: all 0.3s ease;
        height: 100%;
    }
    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .card-icon {
        display: inline-block;
        background-color: rgba(var(--primary-color-rgb), 0.1);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .survey-info {
        font-size: 0.9rem;
    }
    .responded-badge {
        align-self: flex-start;
    }
    .badge {
        font-weight: 500;
        padding: 8px 12px;
        border-radius: 30px;
    }
    .btn-start {
        border-radius: 8px;
        font-weight: 500;
        position: relative;
        z-index: 10;
    }
    .btn-outline-secondary {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        position: relative;
        z-index: 10;
    }
    .empty-state {
        border-radius: 12px;
        border: 2px dashed #e9ecef;
    }
    .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    
    /* Fix for button clickability */
    .survey-card .btn {
        cursor: pointer !important;
        pointer-events: auto !important;
    }
    .d-flex.gap-2.mt-auto {
        position: relative;
        z-index: 10;
    }
    .survey-logo-large {
        max-width: 100px;
        max-height: 100px;
        object-fit: contain;
        margin: 0 auto;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // AJAX Instant Search functionality
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('survey-search');
        const surveysGrid = document.getElementById('surveys-grid');
        const paginationLinks = document.querySelectorAll('.pagination a');
        let searchTimeout = null;
    
        function fetchSurveys(url) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newGrid = doc.getElementById('surveys-grid');
                const newPagination = doc.querySelector('.pagination-container');
                if (newGrid && surveysGrid) {
                    surveysGrid.innerHTML = newGrid.innerHTML;
                }
                if (newPagination) {
                    document.querySelector('.pagination-container').innerHTML = newPagination.innerHTML;
                    attachPaginationEvents();
                }
            });
        }
    
        function attachPaginationEvents() {
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.href;
                    fetchSurveys(url);
                });
            });
        }
    
        if (searchInput && searchForm && surveysGrid) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value;
                searchTimeout = setTimeout(() => {
                    const url = searchForm.action + '?search=' + encodeURIComponent(query);
                    fetchSurveys(url);
                }, 300); // Debounce for 300ms
            });
        }
    
        attachPaginationEvents();
    });
    </script>
@endsection
