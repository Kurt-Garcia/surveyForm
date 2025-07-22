<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Logs Dashboard - Developer Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            color: white;
        }
        .stat-card-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stat-card-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-code-slash fs-3 text-white me-2"></i>
                    <h5 class="text-white mb-0">Developer Portal</h5>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('developer.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('developer.surveys') }}">
                        <i class="bi bi-clipboard-data me-2"></i> Surveys
                    </a>
                    <a class="nav-link" href="{{ route('developer.admins') }}">
                        <i class="bi bi-people me-2"></i> Admins
                    </a>
                    <a class="nav-link" href="{{ route('developer.users') }}">
                        <i class="bi bi-person-check me-2"></i> Users
                    </a>
                    <a class="nav-link active" href="{{ route('developer.logs.index') }}">
                        <i class="bi bi-journal-text me-2"></i> User Logs
                    </a>

                    <hr class="text-white-50">
                    <form action="{{ route('developer.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-journal-text me-2"></i>User Logs Dashboard</h2>
                    <div class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        {{ now()->format('M d, Y H:i') }}
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="bi bi-person-check fs-1 mb-2"></i>
                                <h3 id="totalLogins">{{ $stats['total_logins'] ?? 0 }}</h3>
                                <p class="mb-0">Total Logins Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-success">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <h3 id="activeUsers">{{ $stats['active_users'] ?? 0 }}</h3>
                                <p class="mb-0">Active Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-warning">
                            <div class="card-body text-center">
                                <i class="bi bi-activity fs-1 mb-2"></i>
                                <h3 id="totalActivities">{{ $stats['total_activities'] ?? 0 }}</h3>
                                <p class="mb-0">Activities Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card-info">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up fs-1 mb-2"></i>
                                <h3 id="avgSessionTime">{{ $stats['avg_session_time'] ?? '0m' }}</h3>
                                <p class="mb-0">Avg Session Time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-activity text-primary fs-1 mb-3"></i>
                                <h5>User Activity Logs</h5>
                                <p class="text-muted">View detailed user activity logs including model changes and system interactions</p>
                                <a href="{{ route('developer.logs.user-activity') }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-2"></i>View Activity Logs
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-box-arrow-in-right text-success fs-1 mb-3"></i>
                                <h5>Login/Logout Activity</h5>
                                <p class="text-muted">Monitor user login and logout activities with IP addresses and timestamps</p>
                                <a href="{{ route('developer.logs.login-activity') }}" class="btn btn-success">
                                    <i class="bi bi-eye me-2"></i>View Login Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Login Activity (Last 7 Days)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="loginChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Login Activity Chart
        const ctx = document.getElementById('loginChart').getContext('2d');
        const loginChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Admin Logins',
                    data: {!! json_encode($chartData['admin_logins'] ?? []) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'User Logins',
                    data: {!! json_encode($chartData['user_logins'] ?? []) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Developer Logins',
                    data: {!! json_encode($chartData['developer_logins'] ?? []) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Daily Login Activity'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>