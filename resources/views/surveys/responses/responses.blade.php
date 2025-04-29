@extends('layouts.app-user')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ $survey->title }} - Responses</h2>
                    <a href="{{ route('index', $survey) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Survey
                    </a>
                </div>
                <div class="card-body">
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
                                            <div class="response-icon me-3">
                                                <i class="fas fa-user-circle fa-2x"></i>
                                            </div>
                                            <div class="response-info flex-grow-1">
                                                <h5 class="mb-1">{{ $response->account_name }}</h5>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark me-2">{{ $response->account_type }}</span>
                                                    <span class="text-muted small">
                                                        <i class="fas fa-calendar-alt me-1"></i> {{ $response->date }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="response-actions">
                                                <a href="{{ route('surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
                                                   class="btn btn-primary btn-view-details">
                                                    <i class="fas fa-eye me-1"></i> View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
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
            
            // Add a subtle left border to the item with the random color
            item.style.borderLeftColor = randomColor;
        });
    });
</script>
@endsection