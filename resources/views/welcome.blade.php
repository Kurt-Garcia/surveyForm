@extends('layouts.app')

@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<div class="min-vh-100 d-flex flex-column">
    <!-- Hero Section -->
    <div class="hero-section position-relative overflow-hidden py-4 py-md-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6 text-white text-center text-lg-start px-4" data-aos="fade-right">
                    <h1 class="display-3 display-md-2 fw-bold mb-3 mb-md-4">Your feedback helps us improve.</h1>
                    <p class="lead mb-3 mb-md-4">It's time for you to write a feedback</p>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 rounded-pill shadow-sm">
                            Get Started
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-4 mt-lg-0 d-flex justify-content-center" data-aos="fade-left">
                    <img src="{{ asset('img/welcome.jpg') }}" alt="Survey Platform" class="img-fluid rounded-4 shadow-lg" style="max-width: 90%;">
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
