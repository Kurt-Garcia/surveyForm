@extends('layouts.app')

@section('content')
<!-- Add a subtle loading overlay that will be hidden once everything is loaded -->
<div id="dashboard-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--background-color, #f8f9fa); z-index: 9999; opacity: 1; transition: opacity 0.2s ease-out; pointer-events: none;"></div>

<div class="admin-dashboard" style="opacity: 0; transition: opacity 0.2s ease-in-out;" id="dashboard-content">
    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Welcome Section with modern design -->
                <div class="welcome-card position-relative mb-5">
                    <div class="welcome-card-bg"></div>
                    <div class="card-body p-5 position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-7 mb-4 mb-lg-0">
                                <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill mb-3 font-theme">Admin Dashboard</span>
                                <h1 class="display-4 fw-bold text-white mb-2 welcome-title">Welcome Back, {{ explode(' ', Auth::guard('admin')->user()->name)[0] }}!</h1>
                                <p class="text-white text-opacity-90 mb-0 fs-5 font-theme">{{ now()->format('l, F j, Y') }}</p>
                            </div>
                            <div class="col-lg-5 text-lg-end">
                                <a href="{{ route('admin.surveys.create') }}" class="btn btn-light btn-lg px-4 py-3 rounded-pill shadow-sm align-items-center me-2 mb-2 font-theme">
                                    <i class="bi bi-plus-circle-fill me-2 text-primary"></i>Create Survey
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row g-4">
                <!-- Quick Actions -->
                <div class="col-12 col-lg-8">
                    <div class="feature-card h-100">
                        <div class="card-header p-4">
                            <h4 class="fw-bold">Quick Actions</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('admin.surveys.index') }}" class="action-card">
                                        <div class="action-icon bg-gradient-success">
                                            <i class="bi bi-eye-fill"></i>
                                        </div>
                                        <div class="action-content">
                                            <h5>View Surveys</h5>
                                            <p class="font-theme">Manage your existing surveys</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('admin.admins.create') }}" class="action-card">
                                        <div class="action-icon bg-gradient-info">
                                            <i class="bi bi-shield-lock-fill"></i>
                                        </div>
                                        <div class="action-content">
                                            <h5>Add Admin</h5>
                                            <p class="font-theme">Create new admin accounts</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('admin.users.create') }}" class="action-card">
                                        <div class="action-icon bg-gradient-warning">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </div>
                                        <div class="action-content">
                                            <h5>Add Surveyor</h5>
                                            <p class="font-theme">Create new surveyor accounts</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="row g-0">
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('admin.customers.index') }}" class="action-card">
                                        <div class="action-icon bg-gradient-primary">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="action-content">
                                            <h5>List of Customers</h5>
                                            <p class="font-theme">See all registered customers</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('admin.themes.index') }}" class="action-card">
                                        <div class="action-icon bg-gradient-primary" style="background: linear-gradient(135deg, #9C27B0, #673AB7);">
                                            <i class="bi bi-palette-fill"></i>
                                        </div>
                                        <div class="action-content">
                                            <h5>Theme Settings</h5>
                                            <p class="font-theme">Customize app appearance</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('admin.logos.index') }}" class="action-card">
                                        <div class="action-icon" style="background: linear-gradient(135deg, #00BCD4, #009688);">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="action-content">
                                            <h5>Logo Manager</h5>
                                            <p class="font-theme">Update application logos</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Stats Summary -->
                <div class="col-12 col-lg-4">
                    <div class="stats-card h-100">
                        <div class="card-header p-4">
                            <h4 class="fw-bold">Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="stat-item">
                                <div class="stat-icon bg-gradient-primary">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="stat-info">
                                    <h6>Total Surveys</h6>
                                    <span class="stat-value">{{ $totalSurveys }}</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon bg-gradient-success">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="stat-info">
                                    <h6>Total Responses</h6>
                                    <span class="stat-value">{{ $totalResponses }}</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon bg-gradient-info">
                                    <i class="bi bi-lightning-fill"></i>
                                </div>
                                <div class="stat-info">
                                    <h6>Active Surveys</h6>
                                    <span class="stat-value">{{ $activeSurveys }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Subtle background pattern -->
    <div class="background-pattern"></div>
</div>

<script>
// Prevent FOUC and add smooth loading animation
document.addEventListener('DOMContentLoaded', function() {
    // Hide the loader and show the dashboard smoothly
    const loader = document.getElementById('dashboard-loader');
    const dashboard = document.getElementById('dashboard-content');
    
    // Use requestAnimationFrame to ensure smooth animation
    requestAnimationFrame(function() {
        // Hide loader
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 200);
        }
        
        // Show dashboard content
        if (dashboard) {
            dashboard.style.opacity = '1';
        }
    });
    
    // Add hover effect to action cards with better performance
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add intersection observer for better animation performance
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });
        
        // Observe all animatable elements
        document.querySelectorAll('.feature-card, .stats-card, .action-card').forEach(el => {
            observer.observe(el);
        });
    }
});

// Prevent page flicker on back/forward navigation
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        const dashboard = document.getElementById('dashboard-content');
        const loader = document.getElementById('dashboard-loader');
        
        if (dashboard) dashboard.style.opacity = '1';
        if (loader) {
            loader.style.opacity = '0';
            loader.style.display = 'none';
        }
    }
});
</script>

<style>
:root {
    --primary-color;
    --success-gradient: linear-gradient(135deg, #0BAB64, #3BB78F);
    --info-gradient: linear-gradient(135deg, #0093E9, #80D0C7);
    --warning-gradient: linear-gradient(135deg, #FF9966, #FF5E62);
    --card-radius: 16px;
    --transition-speed: 0.2s;
}

body {
    font-family: 'Inter', sans-serif;
}

.admin-dashboard {
    background-color: var(--background-color);
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* Welcome Card Styles */
.welcome-card {
    border-radius: var(--card-radius);
    border: none;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    position: relative;
}

.welcome-card-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--primary-gradient);
    z-index: 0;
}

.welcome-title {
    font-weight: 800;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    animation: fadeInUp 0.4s ease-out;
    color: #fff;
    background: none;
    -webkit-background-clip: initial;
    -webkit-text-fill-color: initial;
}

/* Feature Card Styles */
.feature-card, .stats-card {
    background: white;
    border-radius: var(--card-radius);
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform var(--transition-speed), box-shadow var(--transition-speed);
}

.feature-card:hover, .stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.feature-card .card-header, .stats-card .card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background: white;
    padding: 1.5rem;
}

.feature-card h4, .stats-card h4 {
    font-weight: 700;
    color: #333;
    margin: 0;
}

/* Action Card Styles */
.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 1.5rem;
    text-decoration: none;
    color: #333;
    transition: all var(--transition-speed);
    height: 100%;
    border-radius: 12px;
    margin: 0.5rem;
}

.action-card:hover {
    background: rgba(0, 0, 0, 0.02);
    transform: translateY(-5px);
}

.action-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: white;
    font-size: 1.8rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    transition: all var(--transition-speed);
}

.action-card:hover .action-icon {
    transform: scale(1.1) rotate(-5deg);
}

.action-content {
    text-align: center;
}

.action-content h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.action-content p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9rem;
}

/* Stats Card Styles */
.stats-card .card-body {
    padding: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.4rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.stat-info {
    flex: 1;
}

.stat-info h6 {
    margin-bottom: 0.25rem;
    font-weight: 500;
    color: #6c757d;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
}

/* Gradient Backgrounds */
.bg-gradient-primary {
    background: var(--primary-gradient);
}

.bg-gradient-success {
    background: var(--success-gradient);
}

.bg-gradient-info {
    background: var(--info-gradient);
}

.bg-gradient-warning {
    background: var(--warning-gradient);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.feature-card, .stats-card {
    animation: fadeInUp 0.3s ease-out forwards;
}

.feature-card {
    animation-delay: 0.1s;
}

.stats-card {
    animation-delay: 0.15s;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .welcome-title {
        font-size: 2.2rem;
    }
    
    .action-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .stat-value {
        font-size: 1.3rem;
    }
}

@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .action-card {
        margin-bottom: 1rem;
        padding: 1.5rem 1rem;
    }
    
    .welcome-card .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .action-icon {
        margin-bottom: 1rem;
    }
}

.font-theme{
    font-family: var(--body-font);
}

</style>
@endsection