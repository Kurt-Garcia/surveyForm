@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-start">
        <div class="col-12">
            <!-- Welcome Section with improved design -->
            <div class="card border-0 bg-primary bg-opacity-10 mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h1 class="h2 fw-bold text-primary mb-2">Welcome Back, Admin!</h1>
                            <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-flex align-items-center">
                                <i class="bi bi-plus-lg me-2"></i>Create Survey
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Quick Actions -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-header bg-white p-4 border-bottom">
                            <h4 class="m-0">Quick Actions</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="row g-0 h-100">
                                <div class="col-12 col-md-6 border-end mt-5">
                                    <a href="{{ route('admin.surveys.index') }}" class="d-block p-4 text-decoration-none quick-action-link h-100">
                                        <div class="text-center p-3 rounded-4 bg-success bg-opacity-15 mb-3 mx-auto icon-container" style="width: 70px; height: 70px;">
                                            <i class="bi bi-eye-fill text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <h5 class="text-success text-center mb-2">View Surveys</h5>
                                        <p class="text-muted text-center mb-0 small">Manage your existing surveys</p>
                                    </a>
                                </div>
                                <div class="col-12 col-md-6 mt-5">
                                    <a href="{{ route('admin.admins.create') }}" class="d-block p-4 text-decoration-none quick-action-link h-100">
                                        <div class="text-center p-3 rounded-4 bg-info bg-opacity-15 mb-3 mx-auto icon-container" style="width: 70px; height: 70px;">
                                            <i class="bi bi-shield-lock-fill text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <h5 class="text-info text-center mb-2">Add Admin</h5>
                                        <p class="text-muted text-center mb-0 small">Create new admin accounts</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Summary -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-header bg-white p-4 border-bottom">
                            <h4 class="m-0">Statistics</h4>
                        </div>
                        <div class="card-body p-0 d-flex flex-column justify-content-center">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3">
                                            <i class="bi bi-bar-chart-fill text-primary"></i>
                                        </div>
                                        <span>Total Surveys</span>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $totalSurveys }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                            <i class="bi bi-people-fill text-success"></i>
                                        </div>
                                        <span>Total Responses</span>
                                    </div>
                                    <span class="badge bg-success rounded-pill">{{ $totalResponses }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3">
                                            <i class="bi bi-lightning-fill text-info"></i>
                                        </div>
                                        <span>Active Surveys</span>
                                    </div>
                                    <span class="badge bg-info rounded-pill">{{ $activeSurveys }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .card {
        overflow: hidden;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .card-header {
        border-top-left-radius: 15px !important;
        border-top-right-radius: 15px !important;
    }
    .quick-action-link {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    .quick-action-link:hover {
        background-color: rgba(0,0,0,0.025);
        transform: translateY(-5px);
    }
    .icon-container {
        position: relative;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .quick-action-link:hover .icon-container {
        transform: scale(1.1);
    }
    .secondary-icon {
        position: absolute;
        font-size: 1rem;
        right: 10px;
        bottom: 10px;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .quick-action-link:hover .secondary-icon {
        opacity: 1;
    }
</style>
@endsection