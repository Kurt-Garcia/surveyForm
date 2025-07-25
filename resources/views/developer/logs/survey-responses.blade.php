@extends('developer.layouts.app')

@section('title', 'Survey Response Logs - Developer Portal')

@section('head')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('additional-styles')
        /* Dark Mode Styling */
        .card {
            background-color: #2d3748 !important;
            border: 1px solid #4a5568 !important;
            border-radius: 15px;
            color: #e2e8f0 !important;
            overflow: hidden;
        }
        .card-header {
            background-color: #1a202c !important;
            border-bottom: 1px solid #4a5568 !important;
            color: #e2e8f0 !important;
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
            border-radius: 0 0 15px 15px;
        }
        
        /* Ensure card content respects border radius */
        .card:not(.card-header) .card-body:first-child {
            border-radius: 15px;
        }
        
        /* Badge Styling */
        .badge-customer { background-color: #e83e8c; color: white; }
        .badge-user { background-color: #17a2b8; }
        .badge-answered { background-color: #20c997; color: white; }
        
        /* Table Styling */
        .table {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
        }
        .table th {
            background-color: #1a202c !important;
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }
        .table td {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }
        .table-striped > tbody > tr:nth-of-type(odd) > td {
            background-color: #374151 !important;
        }
        .table-hover > tbody > tr:hover > td {
            background-color: #4a5568 !important;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            background-color: #2d3748 !important;
        }
        
        /* Fix border radius overflow for cards containing table-responsive */
        .card .table-responsive {
            border-radius: 0 0 15px 15px;
            margin: -1rem -1rem -1rem -1rem;
            padding: 1rem;
        }
        
        .card-header + .card-body .table-responsive {
            border-radius: 0;
            margin: -1rem;
            padding: 1rem;
        }
        
        /* DataTables Styling */
        .dataTables_wrapper {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            background-color: #374151 !important;
            border: 1px solid #4a5568 !important;
            color: #e2e8f0 !important;
            border-radius: 20px;
            padding: 8px 15px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #4a5568 !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
        }
        .dataTables_wrapper .dataTables_length select {
            background-color: #374151 !important;
            border: 1px solid #4a5568 !important;
            color: #e2e8f0 !important;
        }
        .dataTables_wrapper .dataTables_info {
            color: #9ca3af !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #374151 !important;
            border: 1px solid #4a5568 !important;
            color: #e2e8f0 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #4a5568 !important;
            border-color: #6366f1 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }
        .dataTables_wrapper .dataTables_processing {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            background-color: #374151 !important;
            border: 1px solid #4a5568 !important;
            color: #e2e8f0 !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #4a5568 !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
            color: #e2e8f0 !important;
        }
        .form-label {
            color: #e2e8f0 !important;
        }
        
        /* Button Styling */
        .btn-filter {
            border-radius: 20px;
            margin: 2px;
        }
        
        /* Stats Cards */
         .stats-card {
             background-color: #2d3748 !important;
             color: white;
             border-radius: 15px;
             border: 1px solid #4a5568 !important;
             overflow: hidden;
         }
         .stats-card .card-body {
             background: transparent !important;
         }
        
        /* Text Colors */
        .text-muted {
            color: #9ca3af !important;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent !important;
        }
        .breadcrumb-item a {
            color: #6366f1 !important;
        }
        .breadcrumb-item.active {
            color: #9ca3af !important;
        }
        
        /* Modal Styling */
        .modal-content {
            background-color: #2d3748 !important;
            border: 1px solid #4a5568 !important;
            color: #e2e8f0 !important;
        }
        .modal-header {
            background-color: #1a202c !important;
            border-bottom: 1px solid #4a5568 !important;
        }
        .modal-body {
            background-color: #2d3748 !important;
        }
        .modal-footer {
            background-color: #1a202c !important;
            border-top: 1px solid #4a5568 !important;
        }
        .bg-light {
            background-color: #374151 !important;
            color: #e2e8f0 !important;
        }
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-clipboard-data me-2"></i>Survey Response Logs</h2>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-day fs-1 mb-2 text-primary"></i>
                    <h3>{{ $stats['today_responses'] }}</h3>
                    <p class="mb-0">Today's Responses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 mb-2 text-info"></i>
                    <h3>{{ $stats['customer_responses'] }}</h3>
                    <p class="mb-0">Customer Responses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-person-check fs-1 mb-2 text-success"></i>
                    <h3>{{ $stats['user_responses'] }}</h3>
                    <p class="mb-0">User Responses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-check fs-1 mb-2 text-warning"></i>
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
                    <button type="button" id="refreshData" class="btn btn-outline-info btn-filter">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
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
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Duration</th>
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
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        data: 'start_time',
                        name: 'start_time',
                        render: function(data, type, row) {
                            if (data === null || data === undefined) {
                                return '<span class="text-muted">N/A</span>';
                            }
                            const date = new Date(data);
                            return date.toLocaleString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: true
                            });
                        }
                    },
                    {
                        data: 'end_time',
                        name: 'end_time',
                        render: function(data, type, row) {
                            if (data === null || data === undefined) {
                                return '<span class="text-muted">N/A</span>';
                            }
                            const date = new Date(data);
                            return date.toLocaleString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                hour12: true
                            });
                        }
                    },
                    { 
                        data: 'duration', 
                        name: 'duration',
                        render: function(data, type, row) {
                            if (!data || data === 0) {
                                return '<span class="text-muted">N/A</span>';
                            }
                            
                            const seconds = parseInt(data);
                            if (seconds < 60) {
                                return `<span class="badge bg-info">${seconds}s</span>`;
                            } else {
                                const minutes = Math.floor(seconds / 60);
                                const remainingSeconds = seconds % 60;
                                if (remainingSeconds === 0) {
                                    return `<span class="badge bg-primary">${minutes}m</span>`;
                                } else {
                                    return `<span class="badge bg-primary">${minutes}m ${remainingSeconds}s</span>`;
                                }
                            }
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

            // Refresh data
            $('#refreshData').click(function() {
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
@endsection