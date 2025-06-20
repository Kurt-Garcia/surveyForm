@extends('layouts.app-welcome')

@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

<!-- Login Section with same background as welcome -->
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
                <!-- Glassmorphism Login Card -->
                <div class="glass-card">
                    <div class="glass-header text-center mb-4">
                        <h2 class="text-gradient mb-2">{{ __('Welcome Back!') }}</h2>
                        <p class="text-light opacity-75">Sign in to continue your journey</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="glass-form">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="glass-label">{{ __('Username') }}</label>
                            <div class="glass-input-group">
                                <i class="bi bi-person input-icon"></i>
                                <input id="name" type="text" class="glass-input @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                    placeholder="Enter your username">
                            </div>
                            @error('name')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="glass-label">{{ __('Password') }}</label>
                            <div class="glass-input-group password-wrapper">
                                <i class="bi bi-lock input-icon password-icon"></i>
                                <div class="password-input-group w-100">
                                    <input id="password" type="password" class="glass-input @error('password') is-invalid @enderror" 
                                        name="password" required autocomplete="current-password" 
                                        placeholder="Enter your password">
                                    <button type="button" class="glass-password-toggle" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="glass-checkbox">
                                <input class="glass-check-input" type="checkbox" name="remember" 
                                    id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="glass-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="glass-btn glass-btn-primary">
                                <span>{{ __('Sign In') }}</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                            @if (Route::has('password.request'))
                                <a class="glass-link text-center" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
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
    /* Login Hero Section */
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

    .glass-input-group.password-wrapper {
        align-items: center;
        position: relative;
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

    .password-icon {
        color: #4a5568 !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
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

    /* Password Toggle for Glass Design */
    .glass-password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.8);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 5;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .glass-password-toggle:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
        transform: translateY(-50%) scale(1.05);
    }

    .password-wrapper .glass-input {
        padding-right: 65px;
    }

    /* Glass Checkbox */
    .glass-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .glass-check-input {
        width: 18px;
        height: 18px;
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        appearance: none;
        transition: all 0.3s ease;
    }

    .glass-check-input:checked {
        background: var(--primary-color, #007bff);
        border-color: var(--primary-color, #007bff);
    }

    .glass-check-input:checked::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .glass-check-label {
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
        user-select: none;
        font-size: 0.9rem;
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
        display: inline-block;
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

    /* AOS Animation Enhancement */
    [data-aos] {
        opacity: 0;
        transition-property: opacity, transform;
    }

    [data-aos].aos-animate {
        opacity: 1;
    }

    [data-aos="fade-up"] {
        transform: translate3d(0, 30px, 0);
    }

    [data-aos="fade-down"] {
        transform: translate3d(0, -30px, 0);
    }

    [data-aos="zoom-in"] {
        transform: scale(0.8);
    }

    [data-aos].aos-animate {
        transform: translate3d(0, 0, 0) scale(1);
    }
</style>

<!-- Initialize AOS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize password toggles for glass design
        document.querySelectorAll('.glass-password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const eyeIcon = this.querySelector('i');
                
                if (targetInput) {
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        eyeIcon.className = 'bi bi-eye-slash';
                    } else {
                        targetInput.type = 'password';
                        eyeIcon.className = 'bi bi-eye';
                    }
                }
            });
        });
    });
</script>
@endsection
