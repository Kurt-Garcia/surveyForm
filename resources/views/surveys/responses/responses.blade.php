@extends('layouts.app-user')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ $survey->title }} - Responses</h2>
                    <a href="{{ route('index', $survey) }}" class="notification-close" onclick="confirmClose(event)">
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
                                            </div>
                                            <div class="response-actions">
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD',
            '#D4A5A5', '#9B59B6', '#3498DB', '#E67E22', '#2ECC71',
            '#FF9F43', '#00B894', '#74B9FF', '#A8E6CF', '#FFD93D',
            '#FF6B81', '#6C5CE7', '#00CEC9', '#FD79A8', '#81ECEC'
        ];

        function applyColors() {
            document.querySelectorAll('.response-container').forEach(item => {
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                const icon = item.querySelector('.response-icon i');
                const viewDetailsBtn = item.querySelector('.btn-view-details');
                const recommendationBadge = item.querySelector('.recommendation-badge .badge');
                
                if (icon) {
                    icon.style.color = randomColor;
                }
                
                if (viewDetailsBtn) {
                    viewDetailsBtn.style.backgroundColor = randomColor;
                    viewDetailsBtn.style.borderColor = randomColor;
                }
                
                if (recommendationBadge) {
                    recommendationBadge.style.backgroundColor = randomColor;
                }
                
                item.style.borderLeftColor = randomColor;
            });
        }

        applyColors();

        let typingTimer;
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const responsesList = document.querySelector('.responses-list');
        const paginationStatus = document.querySelector('.mb-3.text-muted');
        const paginationLinks = document.querySelector('.d-flex.justify-content-center');

        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                const searchQuery = this.value.trim();
                
                fetch(`${window.location.pathname}?search=${encodeURIComponent(searchQuery)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    const newResponsesList = tempDiv.querySelector('.responses-list');
                    const newPaginationStatus = tempDiv.querySelector('.mb-3.text-muted');
                    const newPaginationLinks = tempDiv.querySelector('.d-flex.justify-content-center');

                    if (newResponsesList) {
                        responsesList.innerHTML = newResponsesList.innerHTML;
                        applyColors();
                    }
                    
                    if (newPaginationStatus) {
                        paginationStatus.innerHTML = newPaginationStatus.innerHTML;
                    }
                    
                    if (newPaginationLinks) {
                        paginationLinks.innerHTML = newPaginationLinks.innerHTML;
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 300);
        });
    });
</script>
@endsection