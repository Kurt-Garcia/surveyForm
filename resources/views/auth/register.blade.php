@extends('layouts.app-welcome')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">{{ __('Create Account') }}</h3>
                </div>

                <div class="card-body p-5">
                    @if($errors->has('security'))
                        <div class="alert alert-danger">
                            {{ $errors->first('security') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your name">
                            </div>
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">
                            </div>
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your password">
                            </div>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('Register') }}
                            </button>
                            <div class="text-center mt-3">
                                <span>Already have an account?</span>
                                <a href="{{ route('login') }}" class="text-primary">Sign In</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9);
    }
    .input-group-text {
        background-color: transparent;
        border-right: none;
    }
    .input-group input {
        border-left: none;
    }
    .input-group input:focus {
        box-shadow: none;
        border-color: #ced4da;
    }
</style>
@endsection
