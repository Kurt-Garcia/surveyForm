@extends('layouts.app-user')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ $survey->title }} - Responses</h2>
                    <a href="{{ route('index', $survey) }}" class="notification-close" data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back To Home">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by account name or type..." value="{{ request('search') }}">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div id="searchResults"></div>
                    
                    <div class="responses-container">
                    @if($responses->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h4>No responses yet</h4>
                            <p class="text-muted">Share this survey to get responses</p>
                        </div>
                    @else
                        <div class="list-group responses-list">
                            @foreach($responses as $response)
                                <div class="list-group-item response-item p-0 border-0 mb-3">
                                    <div class="response-container shadow-sm hover-lift">
                                        <div class="d-flex align-items-center response-row">
                                            <div class="response-info flex-grow-1">
                                                <h5 class="mb-1">{{ $response->account_name }}</h5>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark me-2">{{ $response->account_type }}</span>
                                                    <span class="text-muted small">
                                                        <i class="fas fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($response->date)->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            </div>                            <div class="response-actions">
                                <a href="{{ route('surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
                                    class="btn btn-sm" 
                                    style="border-color: var(--primary-color); color: var(--primary-color)"
                                    onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='white'"
                                    onmouseout="this.style.borderColor='var(--primary-color)'; this.style.backgroundColor='white'; this.style.color='var(--primary-color)'">
                                     <i class="bi bi-eye-fill me-1"></i>View Details
                                </a>
                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($responses->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Showing {{ $responses->firstItem() }} to {{ $responses->lastItem() }} of {{ $responses->total() }} entries
                                </div>
                                <div>
                                    {{ $responses->links() }}
                                </div>
                            </div>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Pagination Styling */
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

    .page-link {
        color: var(--primary-color);
    }
    
    #searchInput:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--accent-color-rgb), 0.25);
        outline: 0;
    }
    
    .page-link:hover {
        color: var(--secondary-color);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .btn-outline-light:hover {
        background-color: var(--secondary-color) !important;
        border-color: var(--secondary-color) !important;
        color: #fff !important;
    }
    
    .responses-list {
        border: none !important;
    }
    
    .response-container {
        border-radius: 12px;
        padding: 1rem;
        position: relative;
        transition: all 0.3s ease;
        border-left-width: 4px;
        border-left-style: solid;
    }
    
    .response-container:hover {
        border-left-color: var(--secondary-color) !important;
    }
    
    .response-row {
        width: 100%;
    }
    
    
    @media (max-width: 768px) {
        .response-row {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .response-info {
            margin-bottom: 1rem;
        }
        
        .response-actions {
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-view-details {
            width: 100%;
        }
        
        .recommendation-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
    }
    
    .response-icon {
        color: #4ECDC4;
    }
    
    .recommendation-badge .badge {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
        border-radius: 30px;
    }
    
    .btn-view-details {
        border-radius: 8px;
        font-weight: 500;
        position: relative;
        z-index: 10;
        pointer-events: auto;
    }
    
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    
    /* Smooth Pagination Additional Styles */
    .responses-container {
        min-height: 200px;
    }
    
    .fade-transition {
        transition: all 0.3s ease;
    }
</style>

<script src="{{ asset('js/lib/smooth-pagination.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply primary color styling
        function applyColors() {
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
            
            document.querySelectorAll('.response-container').forEach(item => {
                const icon = item.querySelector('.response-icon i');
                const viewDetailsBtn = item.querySelector('.btn-view-details');
                const recommendationBadge = item.querySelector('.recommendation-badge .badge');
                
                if (icon) {
                    icon.style.color = primaryColor;
                }
                
                if (viewDetailsBtn) {
                    viewDetailsBtn.style.backgroundColor = primaryColor;
                    viewDetailsBtn.style.borderColor = primaryColor;
                }
                
                if (recommendationBadge) {
                    recommendationBadge.style.backgroundColor = primaryColor;
                }
                
                item.style.borderLeftColor = primaryColor;
            });
        }

        // Initialize smooth pagination
        const smoothPagination = new SmoothPagination({
            contentSelector: '.responses-container',
            paginationSelector: '.pagination',
            scrollToTop: false,
            loadingIndicator: false,
            animationSpeed: 500,
            onBeforeLoad: function() {
                // Any action before loading
            },
            onAfterLoad: function() {
                // Apply colors to newly loaded content
                applyColors();
            }
        });

        // Apply colors initially
        applyColors();

        // Handle search functionality
        let typingTimer;
        const searchInput = document.getElementById('searchInput');
        
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                const searchQuery = this.value.trim();
                const searchUrl = `${window.location.pathname}?search=${encodeURIComponent(searchQuery)}`;
                
                // Use smooth pagination to load search results
                smoothPagination.loadPage(searchUrl);
            }, 300);
        });
    });
</script>
@endsection