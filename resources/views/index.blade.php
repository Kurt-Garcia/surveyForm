@extends('layouts.app')

@section('title', 'Welcome to Survey Form')

@section('content')
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <div class="container">
        <header class="welcome-header">
            <h1>Welcome User</h1>
            <p>Please select your rating category</p>
        </header>

        <nav class="rating-navigation">
            @forelse($surveys as $survey)
                <a href="{{ route('surveys.show', $survey) }}" class="rating-option">
                    <h2>{{ $survey->title }}</h2>
                    <p>{{ $survey->questions->count() }} questions</p>
                </a>
            @empty
                <div class="rating-option">
                    <h2>No surveys available</h2>
                    <p>Please check back later</p>
                </div>
            @endforelse
        </nav>
    </div>
@endsection
