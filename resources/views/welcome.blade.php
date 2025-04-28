@extends('layouts.app-welcome')

@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<div class="min-vh-100 d-flex flex-column">
    <!-- Hero Section -->
    <div class="hero-section position-relative overflow-hidden py-5" style="background: url('{{ asset('img/background.jpg') }}') center/cover no-repeat;">
        <div class="overlay"></div>
        <div class="container position-relative">
            <div class="row align-items-center min-vh-75">
                <div class="col-12 col-lg-6 text-white text-center text-lg-start px-4" data-aos="fade-up">
                    <div class="content-card p-4 rounded-4 bg-dark bg-opacity-25 backdrop-blur">
                        <h1 class="display-3 fw-bold mb-4 text-gradient">Your feedback helps us improve.</h1>
                        <p class="lead mb-4 text-light">Share your thoughts and help shape the future</p>
                        <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 rounded-pill shadow-lg pulse-button">
                                Get Started
                                <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                    <div class="image-container position-relative">
                        <img src="{{ asset('img/welcome1.jpg') }}" alt="Survey Platform" class="img-fluid rounded-4 shadow-lg" style="max-width: 100%;">
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
</div>

<!-- Initialize AOS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            once: true
        });
    });
</script>
@endsection
