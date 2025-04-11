@extends('layouts.app')

@section('title', 'Welcome to Survey Form')

@section('content')
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
        <div class="survey-grid">
            @forelse($surveys as $survey)
                <a href="{{ route('surveys.show', $survey) }}" class="survey-card">
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h2>{{ $survey->title }}</h2>
                    <p><i class="fas fa-question-circle"></i> {{ $survey->questions->count() }} questions</p>
                    <div class="card-footer">
                        <span class="btn-start">Start Survey <i class="fas fa-arrow-right"></i></span>
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
    </div>
@endsection
