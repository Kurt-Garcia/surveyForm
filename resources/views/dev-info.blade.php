@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-code-slash"></i> Developer Access Information
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Important:</strong> This page should be removed in production!
                    </div>

                    <h4>Developer Portal Access</h4>
                    <p>The developer portal is accessible via a special secret URL:</p>
                    
                    <div class="bg-dark text-light p-3 rounded mb-3">
                        <code>{{ url('secret-dev-access-kurt-2025/login') }}</code>
                    </div>

                    <h5>Developer Credentials:</h5>
                    <ul>
                        <li><strong>Username:</strong> Kurt_Gwapo</li>
                        <li><strong>Email:</strong> jobgkurtkainne@gmail.com</li>
                        <li><strong>Password:</strong> Admin123</li>
                    </ul>

                    <div class="alert alert-info" role="alert">
                        <h6>Developer Capabilities:</h6>
                        <ul class="mb-0">
                            <li>View, edit, and delete all surveys</li>
                            <li>Manage all admin accounts (enable/disable/delete)</li>
                            <li>Manage all user accounts (enable/disable/delete)</li>
                            <li>Access to all system components</li>
                            <li>Separate authentication system from admin</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ url('secret-dev-access-kurt-2025/login') }}" class="btn btn-danger btn-lg">
                            <i class="bi bi-shield-lock"></i> Access Developer Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
