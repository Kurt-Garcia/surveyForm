<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Survey Response Logs - Developer Portal</title>
    
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
        .badge-customer { background-color: #e83e8c; color: white; }
        .badge-user { background-color: #17a2b8; }
        .badge-answered { background-color: #20c997; color: white; }
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
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
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
                    <div>
                        <h2><i class="bi bi-clipboard-check me-2"></i>Survey Response Logs</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('developer.logs.index') }}">User Logs</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Survey Responses</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        {{ now()->format('M d, Y H:i') }}
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-day fs-1 mb-2"></i>
                                <h3>{{ $stats['today_responses'] }}</h3>
                                <p class="mb-0">Today's Responses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <h3>{{ $stats['customer_responses'] }}</h3>
                                <p class="mb-0">Customer Responses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-person-check fs-1 mb-2"></i>
                                <h3>{{ $stats['user_responses'] }}</h3>
                                <p class="mb-0">User Responses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-clipboard-check fs-1 mb-2"></i>
                                <h3>{{ $stats['total_responses'] }}</h3>
                                <p class="mb-0">Total Responses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title"><i class="bi bi-funnel me-2"></i>Filters</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="responseTypeFilter" class="form-label">Response Type</label>
                                <select id="responseTypeFilter" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="customer">Customer</option>
                                    <option value="user">Authenticated User</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="customerSearchFilter" class="form-label">Customer Search</label>
                                <input type="text" id="customerSearchFilter" class="form-control" placeholder="Search customer name...">
                            </div>
                            <div class="col-md-3">
                                <label for="dateFromFilter" class="form-label">From Date</label>
                                <input type="date" id="dateFromFilter" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="dateToFilter" class="form-label">To Date</label>
                                <input type="date" id="dateToFilter" class="form-control">
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
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Survey Response Logs Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Survey Response Logs</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="responseTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer/User</th>
                                        <th>Type</th>
                                        <th>Survey</th>
                                        <th>Score</th>
                                        <th>Details</th>
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

    <!-- Response Details Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Response Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="responseDetails"></div>
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
            var table = $('#responseTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("developer.logs.survey-responses.data") }}',
                    data: function(d) {
                        d.response_type = $('#responseTypeFilter').val();
                        d.customer_search = $('#customerSearchFilter').val();
                        d.date_from = $('#dateFromFilter').val();
                        d.date_to = $('#dateToFilter').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { 
                        data: 'customer_info', 
                        name: 'customer_info',
                        render: function(data, type, row) {
                            const name = data.name || 'Unknown';
                            const email = data.email || '';
                            return `<strong>${name}</strong><br><small class="text-muted">${email}</small>`;
                        }
                    },
                    { 
                        data: 'customer_info', 
                        name: 'customer_type',
                        render: function(data, type, row) {
                            const customerType = data.type || 'Unknown';
                            let badgeClass = 'bg-secondary';
                            
                            if (customerType === 'Customer') {
                                badgeClass = 'badge-customer';
                            } else if (customerType === 'Authenticated User') {
                                badgeClass = 'badge-user';
                            }
                            
                            return `<span class="badge ${badgeClass}">${customerType}</span>`;
                        }
                    },
                    { 
                        data: 'survey_info', 
                        name: 'survey_info',
                        render: function(data, type, row) {
                            const title = data.title || 'Unknown Survey';
                            const id = data.id || 'N/A';
                            return `<strong>${title}</strong><br><small class="text-muted">ID: ${id}</small>`;
                        }
                    },
                    { 
                        data: 'survey_info', 
                        name: 'recommendation_score',
                        render: function(data, type, row) {
                            const score = data.recommendation_score;
                            if (score === null || score === undefined) {
                                return '<span class="text-muted">N/A</span>';
                            }
                            
                            let badgeClass = 'bg-secondary';
                            if (score >= 9) badgeClass = 'bg-success';
                            else if (score >= 7) badgeClass = 'bg-warning';
                            else if (score >= 0) badgeClass = 'bg-danger';
                            
                            return `<span class="badge ${badgeClass}">${score}/10</span>`;
                        }
                    },
                    { 
                        data: 'properties', 
                        name: 'properties',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-outline-info" onclick="showResponseDetails(${row.id})"><i class="bi bi-eye"></i> View</button>`;
                        }
                    },
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data, type, row) {
                            if (!data) return 'N/A';
                            try {
                                return new Date(data).toLocaleString();
                            } catch (e) {
                                return 'Invalid date';
                            }
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
                $('#responseTypeFilter').val('');
                $('#customerSearchFilter').val('');
                $('#dateFromFilter').val('');
                $('#dateToFilter').val('');
                table.ajax.reload();
            });

            // Auto-apply filters on change
            $('#responseTypeFilter, #dateFromFilter, #dateToFilter').change(function() {
                table.ajax.reload();
            });
            
            // Apply search filter with delay
            let searchTimeout;
            $('#customerSearchFilter').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    table.ajax.reload();
                }, 500);
            });
        });

        function showResponseDetails(activityId) {
            // Fetch response details via AJAX
            $.get(`{{ route('developer.logs.survey-responses.data') }}`, { activity_id: activityId })
                .done(function(response) {
                    let html = '<div class="row">';
                    
                    if (response.data && response.data.length > 0) {
                        const activity = response.data[0];
                        
                        html += '<div class="col-md-6">';
                        html += '<h6>Response Information</h6>';
                        html += `<p><strong>ID:</strong> ${activity.id}</p>`;
                        html += `<p><strong>Event:</strong> ${activity.event}</p>`;
                        html += `<p><strong>Description:</strong> ${activity.description || 'N/A'}</p>`;
                        html += `<p><strong>Date:</strong> ${new Date(activity.created_at).toLocaleString()}</p>`;
                        html += '</div>';
                        
                        html += '<div class="col-md-6">';
                        html += '<h6>Response Details</h6>';
                        
                        // Handle properties data
                        let properties = activity.properties;
                        if (typeof properties === 'string') {
                            try {
                                properties = JSON.parse(properties);
                            } catch (e) {
                                properties = {};
                            }
                        }
                        
                        if (properties && typeof properties === 'object' && Object.keys(properties).length > 0) {
                            html += '<pre class="bg-light p-2 rounded" style="max-height: 300px; overflow-y: auto;">' + JSON.stringify(properties, null, 2) + '</pre>';
                        } else {
                            html += '<p class="text-muted">No details recorded</p>';
                        }
                        html += '</div>';
                    } else {
                        html += '<div class="col-12"><p class="text-muted">No details available</p></div>';
                    }
                    
                    html += '</div>';
                    
                    $('#responseDetails').html(html);
                    $('#responseModal').modal('show');
                })
                .fail(function() {
                    $('#responseDetails').html('<p class="text-danger">Error loading response details</p>');
                    $('#responseModal').modal('show');
                });
        }
    </script>
</body>
</html>