@extends('layouts.app')

@section('content')
<script src="{{ asset('js/lib/smooth-pagination.js') }}"></script>
<div class="container-fluid py-4 px-4" style="background-color: var(--background-color)">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0 text-gray-800">{{ __('My Surveys') }}</h2>
                    <p class="text-muted small mb-0 mt-2">Total Surveys: {{ $totalSurveys }}</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <form method="GET" action="{{ route('admin.surveys.index') }}" class="search-form me-2">                <div class="search-container">
                    <input type="text" name="search" id="survey-search" class="search-input" placeholder="Search surveys..." value="{{ request('search') }}">
                    <button type="button" class="search-button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                    </form>
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary" style="background-color: var(--primary-color); border-radius: 8px; text-transform: none; font-weight: 500;">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('Create Survey') }}
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="MuiAlert-root MuiAlert-standardSuccess MuiAlert-filled" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeMedium" aria-label="Close"><svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg></button>
                </div>
            @endif

            <!-- Surveys Grid -->
            <div class="surveys-content">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @forelse ($surveys as $survey)
                    <div class="col">
                        <div class="card h-100 survey-card shadow-sm hover-lift" style="border-radius: 16px; background-color: white;">
                            <div class="card-body">
                                <div class="survey-logo-wrapper text-center mb-3">
                                    @if($survey->logo)
                                        <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo-large">
                                    @else
                                        <i class="bi bi-bar-chart-fill" style="font-size:2.5rem;"></i>
                                    @endif
                                </div>
                                <h4 class="card-title text-center">{{ strtoupper($survey->title) }}</h4>
                                <div class="d-flex justify-content-between mt-3 mb-3">
                                    <div class="survey-info">
                                        <div class=" small text-muted mb-1">
                                            <i class="fas fa-question-circle me-1"></i>
                                            {{ $survey->questions->count() }} questions
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-person me-1"></i>
                                            {{ $survey->admin->name }}
                                        </div>
                                    </div>
                                    <div class="responded-badge">
                                        <span class="badge {{ $survey->is_active ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas fa-circle me-1"></i>
                                            {{ $survey->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column mb-3">
                                    <div class="small text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Created {{ $survey->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-bar-chart-fill me-1"></i>
                                        {{ $survey->responseHeaders->count() }} responses
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-start btn-primary flex-grow-1"> {{--add a (btn-primary if you want to turn --primary-color)--}}
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                    <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-outline-secondary btn-view-responses flex-grow-1">
                                        <i class="fas fa-chart-bar me-1"></i> View Responses
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
                                <h4 class="mb-2">No Surveys Found</h4>
                                <p class="text-muted">Start by creating your first survey</p>
                            </div>
                        </div>
                    </div>
                @endforelse
                </div>

                <div class="pagination-container mt-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-muted small text-start mb-2 mb-md-0">
                            Showing {{ $surveys->firstItem() }} to {{ $surveys->lastItem() }} of {{ $surveys->total() }} entries
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $surveys->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

.search-form {
    min-width: 280px;
    width: 100%;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 10px 15px 10px 40px;
    border-radius: 30px;
    border: none;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    font-size: 14px;
}

@media (max-width: 768px) {
    .search-form {
        min-width: auto;
        margin-bottom: 10px;
    }
    
    .d-flex.gap-2.align-items-center {
        flex-direction: column;
        align-items: stretch;
    }
}

.search-input:focus {
    outline: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    background: linear-gradient(135deg, #ffffff 0%, #f0f4f7 100%);
}

.search-button {
    position: absolute;
    left: 10px;
    background: transparent;
    border: none;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-button:hover {
    color: var(--primary-color);
    transform: scale(1.1);
}

.pagination {
    justify-content: center;
}

.pagination .page-item .page-link {
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
    margin: 0 5px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination .page-item .page-link:hover {
    background-color: var(--primary-color);
    color: white;
}

.pagination-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 2rem;
}
.pagination-container .row {
    width: 100%;
}
.pagination-container .pagination {
    justify-content: center;
    margin: 0;
}
.surveys-content {
    min-height: 300px;
}

.pagination .page-link {
    color: #333;
    transition: all 0.2s ease;
}
.pagination .page-link:hover {
    background-color: #eee;
}
.survey-card {
    border-radius: 12px;
    border: none;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
    height: 100%;
}
.survey-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.card-icon {
    display: inline-block;
    background-color: rgba(78, 205, 196, 0.1);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.survey-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 50%;
    padding: 5px;
}
.survey-logo-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.survey-logo-large {
    width: 96px;
    height: 64px;
    object-fit: contain;
    /* Removed border-radius, background, box-shadow, and padding for a cleaner logo */
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
    text-align: center;
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
.survey-card .btn {
    cursor: pointer !important;
    pointer-events: auto !important;
}
.d-flex.gap-2.mt-auto {
    position: relative;
    z-index: 10;
}
.btn-view-responses {
    min-width: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    position: relative;
    z-index: 10;
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Smooth Pagination
        const smoothPagination = new SmoothPagination({
            contentSelector: '.surveys-content',
            paginationSelector: '.pagination',
            scrollToTop: false,
            loadingIndicator: false,
            onAfterLoad: function() {
                // Apply styling to survey cards after loading
                applyStylingToCards();
            }
        });
        
        // Function to apply styling to survey cards
        function applyStylingToCards() {
            document.querySelectorAll('.survey-card').forEach(card => {
                const btnStart = card.querySelector('.btn-start');
                if (btnStart) {
                    btnStart.style.pointerEvents = 'auto';
                    btnStart.style.position = 'relative';
                    btnStart.style.zIndex = '2';
                }
                const buttons = card.querySelectorAll('a.btn');
                buttons.forEach(btn => {
                    btn.style.pointerEvents = 'auto';
                    btn.style.position = 'relative';
                    btn.style.zIndex = '2';
                });
            });
        }
        
        // Handle search functionality
        const searchInput = document.getElementById('survey-search');
        const searchForm = document.querySelector('.search-form');
        const searchButton = document.querySelector('.search-button');
        
        if (searchInput && searchForm) {
            let searchTimeout;
            
            // Handle input typing
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', query);
                    smoothPagination.loadPage(url.toString());
                }, 300); // Debounce for 300ms
            });
            
            // Handle search button click
            if (searchButton) {
                searchButton.addEventListener('click', function() {
                    const query = searchInput.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', query);
                    smoothPagination.loadPage(url.toString());
                });
            }
            
            // Prevent form submission and use SmoothPagination instead
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const query = searchInput.value;
                const url = new URL(this.action);
                url.searchParams.set('search', query);
                smoothPagination.loadPage(url.toString());
            });
        }
        
        // Apply initial styling
        applyStylingToCards();
    });
</script>
@endsection