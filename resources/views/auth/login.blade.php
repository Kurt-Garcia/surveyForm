@extends('layouts.app-welcome')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white text-center py-3">
                    <h3 class="mb-0 text-white">{{ __('Welcome Back!') }}</h3>
                </div>

                <div class="card-body p-5">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">{{ __('Username') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                    placeholder="Enter your username">
                            </div>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password" 
                                    placeholder="Enter your password">
                            </div>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" 
                                    id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg btn-primary">
                                {{ __('Sign In') }}
                            </button>
                            @if (Route::has('password.request'))
                                <a class="btn btn-link text-center" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Theme-specific styles for the login page */
    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    .card-header {
        background-color: var(--primary-color) !important;
        border-bottom: none;
    }
    
    .form-label {
        color: var(--text-color);
        font-weight: 500;
        font-family: var(--body-font);
    }
    
    .input-group-text {
        background-color: transparent;
        border-right: none;
        color: var(--text-color);
    }
    
    .input-group input {
        border-left: none;
        font-family: var(--body-font);
    }
    
    .input-group input:focus {
        box-shadow: none;
        border-color: var(--accent-color);
    }
    
    .form-check-label {
        color: var(--text-color);
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-link {
        color: var(--primary-color);
    }
    
    .btn-link:hover {
        color: var(--secondary-color);
    }
    
    .btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #ffffff !important;
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color) !important;
        border-color: var(--secondary-color) !important;
    }
</style>
@endsection
