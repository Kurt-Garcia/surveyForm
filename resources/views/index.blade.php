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
            <a href="{{ route('index') }}" class="rating-option">
                <h2>Delivery Rating</h2>
                <p>Rate your delivery experience</p>
            </a>
            <a href="{{ route('index') }}" class="rating-option">
                <h2>Service Rating</h2>
                <p>Rate our customer service</p>
            </a>
        </nav>
    </div>
@endsection
