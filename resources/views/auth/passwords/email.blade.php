@extends('layouts.app-welcome')

@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Forgot Password Section with same background as welcome -->
<div class="min-vh-100 d-flex flex-column login-hero" style="background: url('{{ asset('img/background.jpg') }}') center/cover no-repeat;">
    <div class="overlay"></div>
    
    <!-- Flash Messages -->
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show m-3 position-relative" style="z-index: 10;" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3 position-relative" style="z-index: 10;" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container position-relative flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-6 col-lg-5 col-xl-4">
                <!-- Glassmorphism Forgot Password Card -->
                <div class="glass-card">
                    <div class="glass-header text-center mb-4">
                        <h2 class="text-gradient mb-2">{{ __('Reset Password') }}</h2>
                        <p class="text-light opacity-75">Enter your email to receive reset link</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success glass-alert mb-4" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="glass-form">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="glass-label">{{ __('Email Address') }}</label>
                            <div class="glass-input-group">
                                <i class="bi bi-envelope input-icon"></i>
                                <input id="email" type="email" class="glass-input @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                    placeholder="Enter your email">
                            </div>
                            @error('email')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="glass-btn glass-btn-primary">
                                <span>{{ __('Send Password Reset Link') }}</span>
                                <i class="bi bi-send"></i>
                            </button>
                            <a href="{{ route('login') }}" class="glass-link text-center">
                                <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Login') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Animated Wave Effect -->
    <div class="position-absolute bottom-0 start-0 w-100 overflow-hidden">
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 20" preserveAspectRatio="none" shape-rendering="auto">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7)" />
                <use xlink:href="#gentle-wave" x="48" y="1" fill="rgba(255,255,255,0.5)" />
                <use xlink:href="#gentle-wave" x="48" y="2" fill="rgba(255,255,255,0.3)" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="#fff" />
            </g>
        </svg>
    </div>
</div>

<style>
    /* Forgot Password Hero Section */
    .login-hero {
        position: relative;
        overflow: hidden;
        min-height: 100vh;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        z-index: 1;
    }

    /* Glassmorphism Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        position: relative;
        z-index: 2;
        overflow: hidden;
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
        border-radius: 20px;
        z-index: -1;
    }

    /* Text Gradient */
    .text-gradient {
        background: linear-gradient(120deg, #ffffff, #e0e0e0);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
        font-size: 2rem;
    }

    /* Glass Form Elements */
    .glass-label {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
    }

    .glass-input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #4a5568;
        z-index: 3;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .glass-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 15px 15px 15px 45px;
        color: white;
        font-size: 1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .glass-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .glass-input:focus {
        outline: none;
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
    }

    .glass-input.is-invalid {
        border-color: rgba(220, 53, 69, 0.7);
        background: rgba(220, 53, 69, 0.1);
    }

    /* Glass Alert */
    .glass-alert {
        background: rgba(25, 135, 84, 0.15);
        border: 1px solid rgba(25, 135, 84, 0.3);
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.95);
        padding: 15px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
    }

    /* Glass Button */
    .glass-btn {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 15px 30px;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        font-size: 1rem;
    }

    .glass-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .glass-btn:hover::before {
        left: 100%;
    }

    .glass-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.2);
    }

    .glass-btn-primary {
        background: linear-gradient(135deg, var(--primary-color, #007bff), var(--secondary-color, #6c757d));
        border-color: var(--primary-color, #007bff);
    }

    .glass-btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary-color, #6c757d), var(--primary-color, #007bff));
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0, 123, 255, 0.4);
    }

    /* Glass Link */
    .glass-link {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 10px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .glass-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
    }

    /* Wave Animation - same as welcome */
    .waves {
        position: relative;
        width: 100%;
        height: 15vh;
        margin-bottom: -7px;
        min-height: 100px;
        max-height: 150px;
    }

    .parallax > use {
        animation: move-forever 20s cubic-bezier(.35,.35,.35,.35) infinite;
    }

    .parallax > use:nth-child(1) {
        animation-delay: -2s;
        animation-duration: 12s;
    }

    .parallax > use:nth-child(2) {
        animation-delay: -3s;
        animation-duration: 15s;
    }

    .parallax > use:nth-child(3) {
        animation-delay: -4s;
        animation-duration: 18s;
    }

    .parallax > use:nth-child(4) {
        animation-delay: -5s;
        animation-duration: 25s;
    }

    @keyframes move-forever {
        0% {
            transform: translate3d(-90px,0,0);
        }
        100% { 
            transform: translate3d(85px,0,0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .glass-card {
            padding: 2rem 1.5rem;
            margin: 1rem;
        }
        
        .text-gradient {
            font-size: 1.75rem;
        }
        
        .waves {
            height: 30px;
            min-height: 30px;
        }
    }

    @media (max-width: 576px) {
        .glass-card {
            padding: 1.5rem 1rem;
        }
        
        .text-gradient {
            font-size: 1.5rem;
        }
    }
</style>
@endsection
