@extends('layouts.app')

@section('content')
<!-- Add a subtle loading overlay that will be hidden once everything is loaded -->
<div id="dashboard-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--background-color, #f8f9fa); z-index: 9999; opacity: 1; transition: opacity 0.2s ease-out; pointer-events: none;"></div>

<div class="admin-dashboard" style="opacity: 0; transition: opacity 0.2s ease-in-out;" id="dashboard-content">
    <div class="container-fluid px-4 py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Welcome Section with modern design -->
                <div class="welcome-card position-relative mb-5">
                    <div class="welcome-card-bg"></div>
                    <div class="card-body p-5 position-relative">
                        <div class="row align-items-center">
                            <div class="col-lg-8 mb-4 mb-lg-0">
                                <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill mb-3 font-theme">Admin Dashboard</span>
                                <h1 class="display-4 fw-bold text-white mb-2 welcome-title">Welcome Back, {{ explode(' ', Auth::guard('admin')->user()->name)[0] }}!</h1>
                                <p class="text-white text-opacity-90 mb-0 fs-5 font-theme">{{ now()->format('l, F j, Y') }}</p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <a href="{{ route('admin.surveys.create') }}" class="btn btn-light btn-lg px-4 py-3 rounded-pill shadow-sm align-items-center me-2 mb-2 font-theme">
                                    <i class="bi bi-plus-circle-fill me-2 text-primary"></i>Create Survey
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Priority Statistics Section -->
                <div class="row g-4 mb-4">
                    <!-- Most Important Metrics - Highlighted -->
                    <div class="col-12 col-lg-6">
                        <div class="priority-stat-card featured">
                            <div class="priority-stat-body">
                                <div class="priority-icon bg-gradient-primary">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div class="priority-content">
                                    <div class="priority-header">
                                        <span class="priority-badge">PRIORITY METRIC</span>
                                    </div>
                                    <h2 class="priority-number">{{ $totalResponses }}</h2>
                                    <p class="priority-label font-theme">Total Responses</p>
                                    <div class="priority-details">
                                        <div class="detail-item">
                                            <span class="detail-label">Today:</span>
                                            <span class="detail-value text-success">{{ $todayResponses }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">This Week:</span>
                                            <span class="detail-value text-info">{{ $weekResponses }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">This Month:</span>
                                            <span class="detail-value text-warning">{{ $monthResponses }}</span>
                                        </div>
                                    </div>
                                    @if($responseTrend != 0)
                                    <div class="trend-indicator">
                                        <i class="bi bi-{{ $responseTrend > 0 ? 'arrow-up' : 'arrow-down' }} text-{{ $responseTrend > 0 ? 'success' : 'danger' }}"></i>
                                        <span class="text-{{ $responseTrend > 0 ? 'success' : 'danger' }}">{{ abs($responseTrend) }}% vs last month</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-lg-6">
                        <div class="priority-stat-card featured">
                            <div class="priority-stat-body">
                                <div class="priority-icon bg-gradient-success">
                                    <i class="bi bi-clipboard-data"></i>
                                </div>
                                <div class="priority-content">
                                    <div class="priority-header">
                                        <span class="priority-badge">SURVEY STATUS</span>
                                    </div>
                                    <h2 class="priority-number">{{ $activeSurveys }}/{{ $totalSurveys }}</h2>
                                    <p class="priority-label font-theme">Active Surveys</p>
                                    <div class="priority-details">
                                        <div class="detail-item">
                                            <span class="detail-label">Total Created:</span>
                                            <span class="detail-value text-primary">{{ $totalSurveys }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Currently Active:</span>
                                            <span class="detail-value text-success">{{ $activeSurveys }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Inactive:</span>
                                            <span class="detail-value text-muted">{{ $inactiveSurveys }}</span>
                                        </div>
                                    </div>
                                    <div class="completion-bar">
                                        <div class="completion-progress" style="width: {{ $totalSurveys > 0 ? ($activeSurveys / $totalSurveys) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Secondary Statistics -->
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <div class="stat-card">
                                    <div class="stat-card-body">
                                        <div class="stat-icon-large bg-gradient-info">
                                            <i class="bi bi-calculator"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-number">{{ $avgResponsesPerSurvey }}</h3>
                                            <p class="stat-label font-theme">Avg. Responses per Survey</p>
                                            <div class="stat-trend">
                                                <i class="bi bi-bar-chart text-info"></i>
                                                <span class="text-info">Performance</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="stat-card">
                                    <div class="stat-card-body">
                                        <div class="stat-icon-large bg-gradient-warning">
                                            <i class="bi bi-percent"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h3 class="stat-number">{{ $completionRate }}%</h3>
                                            <p class="stat-label font-theme">Survey Completion Rate</p>
                                            <div class="stat-trend">
                                                <i class="bi bi-check-circle text-{{ $completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger') }}"></i>
                                                <span class="text-{{ $completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger') }}">{{ $completionRate >= 80 ? 'Excellent' : ($completionRate >= 60 ? 'Good' : 'Needs Improvement') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Activity Section -->
                        @if($recentResponses->count() > 0)
                        <div class="activity-card mt-4">
                            <div class="card-header p-4">
                                <h4 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Activity</h4>
                            </div>
                            <div class="card-body p-0">
                                @foreach($recentResponses as $response)
                                <div class="activity-item">
                                    <div class="activity-icon bg-gradient-primary">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6 class="activity-title">{{ $response->account_name ?? 'Anonymous' }}</h6>
                                        <p class="activity-description">Responded to "{{ Str::limit($response->survey->title, 40) }}"</p>
                                        <small class="activity-time text-muted">{{ $response->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Performance Overview -->
                    <div class="col-12 col-lg-4">
                        <div class="overview-card h-100">
                            <div class="card-header p-4">
                                <h4 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>Performance Overview</h4>
                            </div>
                            <div class="card-body">
                                @if($mostActiveSurvey)
                                <div class="overview-item">
                                    <div class="overview-icon bg-gradient-success">
                                        <i class="bi bi-trophy"></i>
                                    </div>
                                    <div class="overview-info">
                                        <h6>Most Active Survey</h6>
                                        <span class="text-success fw-bold">{{ Str::limit($mostActiveSurvey->title, 25) }}</span>
                                        <small class="d-block text-muted">{{ $mostActiveSurvey->responses_count }} responses</small>
                                    </div>
                                </div>
                                @endif
                                
                                @if($latestSurvey)
                                <div class="overview-item">
                                    <div class="overview-icon bg-gradient-info">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div class="overview-info">
                                        <h6>Latest Survey</h6>
                                        <span class="text-info fw-bold">{{ Str::limit($latestSurvey->title, 25) }}</span>
                                        <small class="d-block text-muted">Created {{ $latestSurvey->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="overview-item">
                                    <div class="overview-icon bg-gradient-primary">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div class="overview-info">
                                        <h6>Response Rate</h6>
                                        <span class="status-badge bg-{{ $avgResponsesPerSurvey >= 10 ? 'success' : ($avgResponsesPerSurvey >= 5 ? 'warning' : 'danger') }}">{{ $avgResponsesPerSurvey >= 10 ? 'Excellent' : ($avgResponsesPerSurvey >= 5 ? 'Good' : 'Low') }}</span>
                                        <small class="d-block text-muted">{{ $avgResponsesPerSurvey }} avg per survey</small>
                                    </div>
                                </div>
                                
                                <div class="overview-item">
                                    <div class="overview-icon bg-gradient-warning">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="overview-info">
                                        <h6>Survey Engagement</h6>
                                        <span class="status-badge bg-{{ $completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger') }}">{{ $completionRate }}%</span>
                                        <small class="d-block text-muted">Completion rate</small>
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

/* Priority Stat Card Styles */
.priority-stat-card {
    background: white;
    border-radius: var(--card-radius);
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all var(--transition-speed);
    height: 100%;
    position: relative;
}

.priority-stat-card.featured {
    border: 2px solid var(--primary-color);
    box-shadow: 0 10px 35px rgba(var(--primary-color-rgb), 0.15);
    transform: scale(1.02);
}

.priority-stat-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.priority-stat-body {
    padding: 2.5rem;
    display: flex;
    align-items: flex-start;
    gap: 2rem;
}

.priority-icon {
    width: 90px;
    height: 90px;
    border-radius: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}

.priority-content {
    flex: 1;
}

.priority-header {
    margin-bottom: 1rem;
}

.priority-badge {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.priority-number {
    font-size: 3.5rem;
    font-weight: 900;
    color: #333;
    margin: 1rem 0 0.5rem 0;
    line-height: 1;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.priority-label {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.priority-details {
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #6c757d;
    font-size: 0.9rem;
}

.detail-value {
    font-weight: 700;
    font-size: 1rem;
}

.trend-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: rgba(0, 0, 0, 0.02);
    border-radius: 8px;
}

.completion-bar {
    width: 100%;
    height: 8px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 1rem;
}

.completion-progress {
    height: 100%;
    background: linear-gradient(90deg, var(--success-gradient));
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Regular Stat Card Styles */
.stat-card {
    background: white;
    border-radius: var(--card-radius);
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform var(--transition-speed), box-shadow var(--transition-speed);
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.stat-card-body {
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #333;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.stat-label {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Overview Card Styles */
.overview-card {
    background: white;
    border-radius: var(--card-radius);
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform var(--transition-speed), box-shadow var(--transition-speed);
}

.overview-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.overview-card .card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background: white;
    padding: 1.5rem;
}

.overview-card h4 {
    font-weight: 700;
    color: #333;
    margin: 0;
}

.overview-card .card-body {
    padding: 1.5rem;
}

.overview-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.overview-item:last-child {
    border-bottom: none;
}

.overview-icon {
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

.overview-info {
    flex: 1;
}

.overview-info h6 {
    margin-bottom: 0.25rem;
    font-weight: 500;
    color: #333;
    font-size: 0.95rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

/* Activity Card Styles */
.activity-card {
    background: white;
    border-radius: var(--card-radius);
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform var(--transition-speed), box-shadow var(--transition-speed);
}

.activity-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

.activity-card .card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background: white;
    padding: 1.5rem;
}

.activity-card h4 {
    font-weight: 700;
    color: #333;
    margin: 0;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: background-color var(--transition-speed);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background-color: rgba(var(--primary-color-rgb), 0.02);
}

.activity-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    margin-bottom: 0.25rem;
    font-weight: 600;
    color: #333;
    font-size: 1rem;
}

.activity-description {
    margin-bottom: 0.25rem;
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.4;
}

.activity-time {
    font-size: 0.8rem;
    color: #9ca3af;
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

.stat-card, .overview-card {
    animation: fadeInUp 0.3s ease-out forwards;
}

.stat-card {
    animation-delay: 0.1s;
}

.overview-card {
    animation-delay: 0.15s;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .welcome-title {
        font-size: 2.2rem;
    }
    
    .stat-icon-large {
        width: 70px;
        height: 70px;
        font-size: 1.8rem;
    }
    
    .stat-number {
        font-size: 2.2rem;
    }
}

@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .priority-stat-card.featured {
        transform: none;
        margin-bottom: 1rem;
    }
    
    .priority-stat-body {
        padding: 2rem;
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .priority-icon {
        width: 70px;
        height: 70px;
        font-size: 2rem;
    }
    
    .priority-number {
        font-size: 2.8rem;
    }
    
    .stat-card-body {
        padding: 1.5rem;
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .stat-icon-large {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .welcome-card .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .overview-item {
        padding: 0.75rem 0;
    }
    
    .activity-item {
        padding: 1rem;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

.font-theme{
    font-family: var(--body-font);
}

</style>
@endsection