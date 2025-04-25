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
                @if(session('account_name'))
                    <p class="text-muted">Welcome back, {{ session('account_name') }}!</p>
                @endif
            </header>
        </div>
    </div>

    <div class="container survey-container mt-4">
        <div class="survey-grid">
            @forelse($surveys as $survey)
                @php
                    $hasResponded = session('account_name') ? App\Models\SurveyResponseHeader::hasResponded($survey->id, session('account_name')) : false;
                @endphp
                <a href="{{ route('surveys.show', $survey) }}" class="survey-card">
                    @if($hasResponded)
                        <div class="completed-badge">
                            <i class="fas fa-check-circle"></i> Completed
                        </div>
                    @endif
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2>{{ strtoupper($survey->title) }}</h2>
                    <p><i class="fas fa-question-circle"></i> {{ $survey->questions->count() }} questions</p>
                    <div class="card-footer">
                        <span class="btn-start">View Survey <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <i class="fas fa-clipboard-question"></i>
                    <h2>No Surveys Available</h2>
                    <p>Check back soon for new surveys!</p>
                </div>
            @endforelse
        </div>
        
        <div class="pagination-container mt-4">
            {{ $surveys->links() }}
        </div>
    </div>

    <style>
    .survey-card {
        position: relative;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .survey-card:hover {
        transform: translateY(-5px);
        text-decoration: none;
        color: inherit;
    }
    .completed-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.9em;
        z-index: 1;
    }
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        min-width: 300px;
    }
    .card-icon {
        transition: all 0.3s ease;
    }
    .btn-start {
        transition: all 0.3s ease;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
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
            }
            if (btnStart) {
                btnStart.style.color = randomColor;
            }
        });
    });
    </script>
@endsection
