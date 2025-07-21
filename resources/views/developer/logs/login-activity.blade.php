<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Activity Logs - Developer Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
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
        }
        .badge-login { background-color: #28a745; }
        .badge-logout { background-color: #dc3545; }
        .badge-admin { background-color: #6f42c1; }
        .badge-user { background-color: #17a2b8; }
        .badge-developer { background-color: #fd7e14; }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            border: 1px solid #ddd;
            padding: 8px 15px;
        }
        .btn-filter {
            border-radius: 20px;
            margin: 2px;
        }
        .ip-address {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
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
                    <a class="nav-link" href="{{ route('developer.translations.index') }}">
                        <i class="bi bi-translate me-2"></i> Translations
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
                    <div>
                        <h2><i class="bi bi-box-arrow-in-right me-2"></i>Login Logs</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('developer.logs.index') }}">User Logs</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Login Activity</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        {{ now()->format('M d, Y H:i') }}
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-box-arrow-in-right fs-2 mb-2"></i>
                                <h4 id="todayLogins">{{ $stats['today_logins'] ?? 0 }}</h4>
                                <p class="mb-0">Today's Logins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-2 mb-2"></i>
                                <h4 id="uniqueUsers">{{ $stats['unique_users'] ?? 0 }}</h4>
                                <p class="mb-0">Unique Users Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-clock-history fs-2 mb-2"></i>
                                <h4 id="recentActivity">{{ $stats['recent_activity'] ?? 0 }}</h4>
                                <p class="mb-0">Last Hour</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-geo-alt fs-2 mb-2"></i>
                                <h4 id="uniqueIPs">{{ $stats['unique_ips'] ?? 0 }}</h4>
                                <p class="mb-0">Unique IPs Today</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-funnel me-2"></i>Filters</h6>
                        <div class="row">
                            <div class="col-md-2">
                                <label for="userTypeFilter" class="form-label">User Type</label>
                                <select id="userTypeFilter" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="developer">Developer</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="actionFilter" class="form-label">Action</label>
                                <select id="actionFilter" class="form-select">
                                    <option value="">All Actions</option>
                                    <option value="login">Login</option>
                                    <option value="logout">Logout</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="dateFromFilter" class="form-label">From Date</label>
                                <input type="date" id="dateFromFilter" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="dateToFilter" class="form-label">To Date</label>
                                <input type="date" id="dateToFilter" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="ipFilter" class="form-label">IP Address</label>
                                <input type="text" id="ipFilter" class="form-control" placeholder="e.g., 192.168.1.1">
                            </div>
                            <div class="col-md-2">
                                <label for="userFilter" class="form-label">User</label>
                                <input type="text" id="userFilter" class="form-control" placeholder="Name or Email">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="applyFilters" class="btn btn-primary btn-filter">
                                    <i class="bi bi-search me-2"></i>Apply Filters
                                </button>
                                <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-filter">
                                    <i class="bi bi-x-circle me-2"></i>Clear Filters
                                </button>
                                <button type="button" id="refreshData" class="btn btn-outline-info btn-filter">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Activity Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Login/Logout Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="loginTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>User Type</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                        <th>Date/Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Agent Details Modal -->
    <div class="modal fade" id="userAgentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>User Agent Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="userAgentDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#loginTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("developer.logs.login-activity.data") }}',
                    data: function(d) {
                        d.user_type = $('#userTypeFilter').val();
                        d.action = $('#actionFilter').val();
                        d.date_from = $('#dateFromFilter').val();
                        d.date_to = $('#dateToFilter').val();
                        d.ip_address = $('#ipFilter').val();
                        d.user_search = $('#userFilter').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { 
                        data: 'user_type', 
                        name: 'user_type',
                        render: function(data, type, row) {
                            let badgeClass = 'bg-secondary';
                            let displayText = data || 'Unknown';
                            
                            if (data === 'admin') {
                                badgeClass = 'badge-admin';
                                displayText = 'Admin';
                            } else if (data === 'user') {
                                badgeClass = 'badge-user';
                                displayText = 'User';
                            } else if (data === 'developer') {
                                badgeClass = 'badge-developer';
                                displayText = 'Developer';
                            }
                            
                            return `<span class="badge ${badgeClass}">${displayText}</span>`;
                        }
                    },
                    { 
                        data: null, 
                        name: 'user_name',
                        render: function(data, type, row) {
                            let html = `<strong>${row.user_name || 'Unknown'}</strong>`;
                            if (row.user_email) {
                                html += `<br><small class="text-muted">${row.user_email}</small>`;
                            }
                            return html;
                        }
                    },
                    { 
                        data: 'action', 
                        name: 'action',
                        render: function(data, type, row) {
                            let badgeClass = data === 'login' ? 'badge-login' : 'badge-logout';
                            let icon = data === 'login' ? 'box-arrow-in-right' : 'box-arrow-right';
                            
                            return `<span class="badge ${badgeClass}"><i class="bi bi-${icon} me-1"></i>${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    { 
                        data: 'ip_address', 
                        name: 'ip_address',
                        render: function(data, type, row) {
                            return data ? `<span class="ip-address">${data}</span>` : '<em class="text-muted">Unknown</em>';
                        }
                    },
                    { 
                        data: 'user_agent', 
                        name: 'user_agent',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!data) return '<em class="text-muted">Unknown</em>';
                            
                            let shortAgent = data.length > 50 ? data.substring(0, 50) + '...' : data;
                            return `<span title="${data}" style="cursor: pointer;" onclick="showUserAgent('${encodeURIComponent(data)}')">${shortAgent}</span>`;
                        }
                    },
                    { 
                        data: 'action_time', 
                        name: 'action_time',
                        render: function(data, type, row) {
                            return new Date(data).toLocaleString();
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-text"></i> Export CSV',
                        className: 'btn btn-info btn-sm'
                    }
                ]
            });

            // Apply filters
            $('#applyFilters').click(function() {
                table.ajax.reload();
            });

            // Clear filters
            $('#clearFilters').click(function() {
                $('#userTypeFilter').val('');
                $('#actionFilter').val('');
                $('#dateFromFilter').val('');
                $('#dateToFilter').val('');
                $('#ipFilter').val('');
                $('#userFilter').val('');
                table.ajax.reload();
            });

            // Refresh data
            $('#refreshData').click(function() {
                table.ajax.reload();
            });

            // Auto-apply filters on change
            $('#userTypeFilter, #actionFilter, #dateFromFilter, #dateToFilter').change(function() {
                table.ajax.reload();
            });

            // Apply filters on Enter key for text inputs
            $('#ipFilter, #userFilter').keypress(function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });

            // Auto-refresh every 30 seconds
            setInterval(function() {
                table.ajax.reload(null, false); // false = don't reset paging
            }, 30000);
        });

        function showUserAgent(userAgent) {
            const decodedAgent = decodeURIComponent(userAgent);
            
            // Parse user agent for better display
            let html = '<div class="row">';
            html += '<div class="col-12">';
            html += '<h6>Full User Agent String</h6>';
            html += `<div class="bg-light p-3 rounded" style="word-break: break-all;">${decodedAgent}</div>`;
            html += '</div>';
            html += '</div>';
            
            // Try to extract browser and OS info
            html += '<div class="row mt-3">';
            html += '<div class="col-md-6">';
            html += '<h6>Browser Information</h6>';
            
            if (decodedAgent.includes('Chrome')) {
                html += '<p><i class="bi bi-browser-chrome text-warning"></i> Chrome Browser</p>';
            } else if (decodedAgent.includes('Firefox')) {
                html += '<p><i class="bi bi-browser-firefox text-danger"></i> Firefox Browser</p>';
            } else if (decodedAgent.includes('Safari') && !decodedAgent.includes('Chrome')) {
                html += '<p><i class="bi bi-browser-safari text-primary"></i> Safari Browser</p>';
            } else if (decodedAgent.includes('Edge')) {
                html += '<p><i class="bi bi-browser-edge text-info"></i> Edge Browser</p>';
            } else {
                html += '<p><i class="bi bi-question-circle"></i> Unknown Browser</p>';
            }
            
            html += '</div>';
            html += '<div class="col-md-6">';
            html += '<h6>Operating System</h6>';
            
            if (decodedAgent.includes('Windows')) {
                html += '<p><i class="bi bi-windows text-primary"></i> Windows</p>';
            } else if (decodedAgent.includes('Mac')) {
                html += '<p><i class="bi bi-apple text-dark"></i> macOS</p>';
            } else if (decodedAgent.includes('Linux')) {
                html += '<p><i class="bi bi-ubuntu text-warning"></i> Linux</p>';
            } else if (decodedAgent.includes('Android')) {
                html += '<p><i class="bi bi-android text-success"></i> Android</p>';
            } else if (decodedAgent.includes('iOS')) {
                html += '<p><i class="bi bi-phone text-dark"></i> iOS</p>';
            } else {
                html += '<p><i class="bi bi-question-circle"></i> Unknown OS</p>';
            }
            
            html += '</div>';
            html += '</div>';
            
            $('#userAgentDetails').html(html);
            $('#userAgentModal').modal('show');
        }
    </script>
</body>
</html>