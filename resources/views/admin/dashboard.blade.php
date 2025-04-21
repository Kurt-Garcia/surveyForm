@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-11">
            <!-- Welcome Section -->
            <div class="card border-0 bg-transparent mb-5">
                <div class="card-body px-0">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="display-5 fw-bold text-primary mb-1">Welcome Back, Admin!</h1>
                            <p class="text-muted mb-0">Here's what's happening with your surveys</p>
                        </div>
                        <div class="d-none d-md-flex gap-2">
                            <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary px-4 rounded-3 d-flex align-items-center">
                                <i class="bi bi-plus-lg me-2"></i>New Survey
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="bi bi-bar-chart-fill text-primary fs-4"></i>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Surveys</span>
                            </div>
                            <h3 class="display-6 fw-bold mb-1">{{ $totalSurveys }}</h3>
                            <p class="text-muted mb-0">Total Surveys Created</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="bi bi-people-fill text-success fs-4"></i>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Responses</span>
                            </div>
                            <h3 class="display-6 fw-bold mb-1">{{ $totalResponses }}</h3>
                            <p class="text-muted mb-0">Total Response Collected</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                    <i class="bi bi-lightning-fill text-info fs-4"></i>
                                </div>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">Active</span>
                            </div>
                            <h3 class="display-6 fw-bold mb-1">{{ $activeSurveys }}</h3>
                            <p class="text-muted mb-0">Currently Active Surveys</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Quick Actions</h4>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <a href="{{ route('admin.surveys.index') }}" class="card bg-success bg-opacity-10 border-0 text-decoration-none h-100 hover-shadow">
                                <div class="card-body p-4 text-center">
                                    <i class="bi bi-list-check fs-1 text-success mb-3"></i>
                                    <h5 class="text-success mb-2">View Surveys</h5>
                                    <p class="text-muted mb-0 small">Manage your existing surveys</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="{{ route('admin.admins.create') }}" class="card bg-info bg-opacity-10 border-0 text-decoration-none h-100 hover-shadow">
                                <div class="card-body p-4 text-center">
                                    <i class="bi bi-person-plus fs-1 text-info mb-3"></i>
                                    <h5 class="text-info mb-2">Add Admin</h5>
                                    <p class="text-muted mb-0 small">Create new admin accounts</p>
                                </div>
                            </a>
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
</style>
@endsection