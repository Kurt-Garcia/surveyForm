@extends('developer.layouts.app')

@section('title', 'User Logs Dashboard - Developer Portal')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-journal-text me-2"></i>User Logs Dashboard</h2>
        <div class="text-muted">
            <i class="bi bi-clock me-1"></i>
            {{ now()->format('M d, Y H:i') }}
        </div>
    </div>

    <!-- Enhanced Statistics Dashboard -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px;">
                <div class="card-body text-center py-4">
                    <h3 class="text-white mb-3"><i class="bi bi-speedometer2 me-2"></i>System Overview</h3>
                    <p class="text-white-50 mb-0">Real-time monitoring of user activities and system performance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Highlighted Statistics Cards -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-lg h-100" style="transform: scale(1.02); transition: all 0.3s ease; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px;">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                         <i class="bi bi-person-check" style="font-size: 3.5rem; color: #01cadd;"></i>
                     </div>
                    <h2 id="totalLogins" class="display-4 fw-bold mb-2 text-white">{{ $stats['total_logins'] ?? 0 }}</h2>
                    <p class="mb-0 text-uppercase fw-semibold text-white" style="letter-spacing: 1px;">Total Logins Today</p>
                    <div class="mt-2">
                          <small style="color: #01cadd;"><i class="bi bi-arrow-up"></i> Active</small>
                      </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-lg h-100" style="transform: scale(1.02); transition: all 0.3s ease; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px;">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-people" style="font-size: 3.5rem; color: #27ae60;"></i>
                    </div>
                    <h2 id="activeUsers" class="display-4 fw-bold mb-2 text-white">{{ $stats['active_users'] ?? 0 }}</h2>
                    <p class="mb-0 text-uppercase fw-semibold text-white" style="letter-spacing: 1px;">Active Users</p>
                    <div class="mt-2">
                        <small class="text-success"><i class="bi bi-circle-fill"></i> Online</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-lg h-100" style="transform: scale(1.02); transition: all 0.3s ease; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px;">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-activity" style="font-size: 3.5rem; color: #f39c12;"></i>
                    </div>
                    <h2 id="totalActivities" class="display-4 fw-bold mb-2 text-white">{{ $stats['total_activities'] ?? 0 }}</h2>
                    <p class="mb-0 text-uppercase fw-semibold text-white" style="letter-spacing: 1px;">Activities Today</p>
                    <div class="mt-2">
                        <small class="text-warning"><i class="bi bi-lightning-fill"></i> Live</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-lg h-100" style="transform: scale(1.02); transition: all 0.3s ease; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px;">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-graph-up" style="font-size: 3.5rem; color: #3498db;"></i>
                    </div>
                    <h2 id="avgSessionTime" class="display-4 fw-bold mb-2 text-white">{{ $stats['avg_session_time'] ?? '0m' }}</h2>
                    <p class="mb-0 text-uppercase fw-semibold text-white" style="letter-spacing: 1px;">Avg Session Time</p>
                    <div class="mt-2">
                        <small class="text-info"  style="color: #3498db;"><i class="bi bi-clock-fill"></i> Duration</small>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Enhanced Activity Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);">
                 <div class="card-header border-0 text-center py-4" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 20px 20px 0 0;">
                    <h3 class="mb-2 text-white"><i class="bi bi-graph-up-arrow me-3"></i>Login Activity Analytics</h3>
                    <p class="mb-0 text-white-50">Comprehensive view of user login patterns over the last 7 days</p>
                </div>
                <div class="card-body p-4" style="min-height: 400px;">
                    <div class="row mb-3">
                        <div class="col-md-3 text-center">
                            <div class="p-3 rounded" style="background: rgba(255, 206, 84, 0.1); border: 2px solid rgba(255, 206, 84, 0.3);">
                                <i class="bi bi-shield-shaded fs-4 mb-2" style="color: rgb(255, 206, 84);"></i>
                                <h6 class="text-white">Super Admin Logins</h6>
                                <small class="text-muted">Highest level access</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="p-3 rounded" style="background: rgba(75, 192, 192, 0.1); border: 2px solid rgba(75, 192, 192, 0.3);">
                                <i class="bi bi-shield-check text-info fs-4 mb-2"></i>
                                <h6 class="text-white">Admin Logins</h6>
                                <small class="text-muted">Secure access monitoring</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="p-3 rounded" style="background: rgba(255, 99, 132, 0.1); border: 2px solid rgba(255, 99, 132, 0.3);">
                                <i class="bi bi-people text-danger fs-4 mb-2"></i>
                                <h6 class="text-white">User Logins</h6>
                                <small class="text-muted">Customer activity tracking</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="p-3 rounded" style="background: rgba(54, 162, 235, 0.1); border: 2px solid rgba(54, 162, 235, 0.3);">
                                <i class="bi bi-code-slash text-primary fs-4 mb-2"></i>
                                <h6 class="text-white">Developer Logins</h6>
                                <small class="text-muted">System administration</small>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="loginChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Enhanced Login Activity Chart
        const ctx = document.getElementById('loginChart').getContext('2d');
        const loginChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Super Admin Logins',
                    data: {!! json_encode($chartData['super_admin_logins'] ?? []) !!},
                    borderColor: 'rgb(255, 206, 84)',
                    backgroundColor: 'rgba(255, 206, 84, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(255, 206, 84)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Admin Logins',
                    data: {!! json_encode($chartData['admin_logins'] ?? []) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'User Logins',
                    data: {!! json_encode($chartData['user_logins'] ?? []) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(255, 99, 132)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Developer Logins',
                    data: {!! json_encode($chartData['developer_logins'] ?? []) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#fff',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 10,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)',
                            borderColor: 'rgba(255, 255, 255, 0.2)'
                        },
                        ticks: {
                            color: '#fff',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)',
                            borderColor: 'rgba(255, 255, 255, 0.2)'
                        },
                        ticks: {
                            color: '#fff',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#fff'
                    }
                }
            }
        });

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card, .stat-card-success, .stat-card-warning, .stat-card-info').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05) translateY(-5px)';
                this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.3)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1.02)';
                this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.2)';
            });
        });
    </script>
@endsection