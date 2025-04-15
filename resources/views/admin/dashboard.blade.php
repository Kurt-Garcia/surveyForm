@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<div class="container py-3 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg mb-4 border-0" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                <div class="card-body text-center py-4 py-md-5">
                    <h1 class="display-4 text-primary mb-3 mb-md-4 fw-bold fs-2 fs-md-1" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Welcome Admin!</h1>
                    <p class="lead text-muted mb-4 mb-md-5">Manage your surveys and analyze responses with ease</p>
                    
                    <div class="row g-3 mb-4 mb-md-5">
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm h-100 bg-white bg-opacity-75">
                                <div class="card-body p-3">
                                    <i class="bi bi-bar-chart-fill text-primary display-4 mb-2 mb-md-3"></i>
                                    <h3 class="fw-bold fs-4">Total Surveys</h3>
                                    <p class="display-6 text-primary mb-0">{{ $totalSurveys }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm h-100 bg-white bg-opacity-75">
                                <div class="card-body p-3">
                                    <i class="bi bi-people-fill text-success display-4 mb-2 mb-md-3"></i>
                                    <h3 class="fw-bold fs-4">Total Responses</h3>
                                    <p class="display-6 text-success mb-0">{{ $totalResponses }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm h-100 bg-white bg-opacity-75">
                                <div class="card-body p-3">
                                    <i class="bi bi-clock-fill text-info display-4 mb-2 mb-md-3"></i>
                                    <h3 class="fw-bold fs-4">Active Surveys</h3>
                                    <p class="display-6 text-info mb-0">{{ $activeSurveys }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3 gap-md-4 mt-3 mt-md-4">
                        <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary px-4 py-2 py-md-3 rounded-pill shadow-sm" style="transition: all 0.3s ease;">
                            <i class="bi bi-plus-circle me-2"></i>Create Survey
                        </a>
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-outline-primary px-4 py-2 py-md-3 rounded-pill shadow-sm" style="transition: all 0.3s ease;">
                            <i class="bi bi-view-list me-2"></i>View Survey
                        </a>
                        <a href="#" class="btn btn-outline-primary px-4 py-2 py-md-3 rounded-pill shadow-sm" style="transition: all 0.3s ease;">
                            <i class="bi bi-bar-chart me-2"></i>View Responses
                        </a>
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-outline-primary px-4 py-2 py-md-3 rounded-pill shadow-sm" style="transition: all 0.3s ease;">
                            <i class="bi bi-person-plus me-2"></i>Add Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection