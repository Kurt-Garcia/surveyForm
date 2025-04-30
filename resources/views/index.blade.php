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
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($surveys as $survey)
                @php
                    $hasResponded = session('account_name') ? App\Models\SurveyResponseHeader::hasResponded($survey->id, session('account_name')) : false;
                    $responseCount = App\Models\SurveyResponseHeader::where('survey_id', $survey->id)->count();
                @endphp
                <div class="col">
                    <div class="card h-100 survey-card shadow-sm hover-lift">
                        <div class="card-body">
                            <div class="card-icon mb-3">
                                <i class="fas fa-poll fa-2x"></i>
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
        
        <div class="pagination-container mt-4">
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
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }
    .pagination-container .pagination {
        margin: 0;
    }
    .pagination .page-item.active .page-link {
        background-color: #4ECDC4;
        border-color: #4ECDC4;
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
        background-color: rgba(78, 205, 196, 0.1);
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
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD',
            '#D4A5A5', '#9B59B6', '#3498DB', '#E67E22', '#2ECC71',
            '#FF9F43', '#00B894', '#74B9FF', '#A8E6CF', '#FFD93D',
            '#FF6B81', '#6C5CE7', '#00CEC9', '#FD79A8', '#81ECEC'
        ];

        document.querySelectorAll('.survey-card').forEach(card => {
            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            const icon = card.querySelector('.card-icon');
            const btnStart = card.querySelector('.btn-start');
            
            if (icon) {
                icon.style.color = randomColor;
                icon.style.backgroundColor = `${randomColor}15`; // Very light background of the same color
            }
            
            if (btnStart) {
                btnStart.style.backgroundColor = randomColor;
                btnStart.style.borderColor = randomColor;
                
                // Make sure the button is clickable
                btnStart.style.pointerEvents = 'auto';
                btnStart.style.position = 'relative';
                btnStart.style.zIndex = '2';
            }
            
            // Add a subtle left border to the card with the random color
            card.style.borderLeft = `4px solid ${randomColor}`;
            
            // Ensure the card's hover effect doesn't interfere with button clicks
            const buttons = card.querySelectorAll('a.btn');
            buttons.forEach(btn => {
                btn.style.pointerEvents = 'auto';
                btn.style.position = 'relative';
                btn.style.zIndex = '2';
            });
        });
    });
    </script>
@endsection
