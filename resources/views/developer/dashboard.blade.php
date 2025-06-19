<!DOCTYPE html>
<html lang="en">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Developer Portal - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Developer-specific styles -->
<style>
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}
.developer-dashboard {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 100vh;
    color: white;
}

.dev-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    transition: all 0.3s ease;
}

.dev-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.dev-stats-card {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    border: none;
}

.dev-action-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    color: white;
    text-decoration: none;
    display: block;
    height: 100%;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.dev-action-card:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
    color: white;
}

.danger-zone {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border: 2px solid #e74c3c;
}

.admin-zone {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border: 2px solid #f39c12;
}

.user-zone {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border: 2px solid #3498db;
}

.survey-zone {
    background: linear-gradient(135deg, #27ae60, #229954);
    border: 2px solid #27ae60;
}
</style>
</head>
<body>

<div class="developer-dashboard">
    <div class="container-fluid px-4 py-5">
        <!-- Header with logout -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold text-danger">
                    <i class="bi bi-code-slash"></i> Developer Portal
                </h1>
                <p class="text-light">Welcome, {{ Auth::guard('developer')->user()->name }}</p>
            </div>
            <div class="col-md-6 text-end">
                <form method="POST" action="{{ route('developer.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">
                        <i class="bi bi-power"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="dev-stats-card text-center survey-zone">
                    <i class="bi bi-clipboard-data display-4 mb-3"></i>
                    <h3>{{ $stats['total_surveys'] }}</h3>
                    <p>Total Surveys</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dev-stats-card text-center admin-zone">
                    <i class="bi bi-shield-lock display-4 mb-3"></i>
                    <h3>{{ $stats['total_admins'] }}</h3>
                    <p>Total Admins</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dev-stats-card text-center user-zone">
                    <i class="bi bi-people display-4 mb-3"></i>
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dev-stats-card text-center danger-zone">
                    <i class="bi bi-graph-up display-4 mb-3"></i>
                    <h3>{{ $stats['total_responses'] }}</h3>
                    <p>Total Responses</p>
                </div>
            </div>
        </div>

        <!-- Active Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="dev-card p-4 text-center">
                    <i class="bi bi-check-circle text-success display-6"></i>
                    <h4 class="mt-3">{{ $stats['active_surveys'] }}</h4>
                    <p>Active Surveys</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dev-card p-4 text-center">
                    <i class="bi bi-shield-check text-warning display-6"></i>
                    <h4 class="mt-3">{{ $stats['active_admins'] }}</h4>
                    <p>Active Admins</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dev-card p-4 text-center">
                    <i class="bi bi-person-check text-info display-6"></i>
                    <h4 class="mt-3">{{ $stats['active_users'] }}</h4>
                    <p>Active Users</p>
                </div>
            </div>
        </div>        <!-- Management Actions -->
        <div class="row g-4">
            <div class="col-12">
                <h2 class="text-center mb-4 text-danger">
                    <i class="bi bi-exclamation-triangle"></i> FULL SYSTEM CONTROL
                </h2>
            </div>
            
            <div class="col-md-4">
                <a href="{{ route('developer.surveys') }}" class="dev-action-card survey-zone">
                    <div>
                        <i class="bi bi-clipboard-data display-4 mb-3"></i>
                        <h5>Survey Management</h5>
                        <p>View, Edit & Delete All Surveys</p>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="{{ route('developer.admins') }}" class="dev-action-card admin-zone">
                    <div>
                        <i class="bi bi-shield-lock display-4 mb-3"></i>
                        <h5>Admin Management</h5>
                        <p>Manage Admin Accounts & Permissions</p>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="{{ route('developer.users') }}" class="dev-action-card user-zone">
                    <div>
                        <i class="bi bi-people display-4 mb-3"></i>
                        <h5>User Management</h5>
                        <p>Control All User Accounts</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Warning Notice -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-shield-exclamation"></i>
                    <strong>Developer Access:</strong> You have unrestricted access to all system components. Use with extreme caution.
                </div>
            </div>
        </div>    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fade in effect
document.addEventListener('DOMContentLoaded', function() {
    const dashboard = document.querySelector('.developer-dashboard');
    setTimeout(() => {
        dashboard.style.opacity = '1';
    }, 100);
});
</script>

</body>
</html>
