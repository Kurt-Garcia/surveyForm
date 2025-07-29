@extends('developer.layouts.app')

@section('title', 'Page Visit Logs - Developer Portal')

@section('head')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('additional-styles')
        /* Dark Mode Card Styling */
        .card {
            border-radius: 15px;
            background-color: #2d3748;
            border: 1px solid #4a5568;
            color: #e2e8f0;
            overflow: hidden;
        }
        
        .card-header {
            background-color: #1a202c;
            border-bottom: 1px solid #4a5568;
            color: #e2e8f0;
            border-radius: 15px 15px 0 0;
        }
        
        .card-body {
            background-color: #2d3748;
            color: #e2e8f0;
            border-radius: 0 0 15px 15px;
        }
        
        /* Ensure card content respects border radius */
        .card:not(.card-header) .card-body:first-child {
            border-radius: 15px;
        }
        
        /* Badge Styling */
        .badge-admin { background-color: #6f42c1; }
        .badge-superadmin { background-color: #5a23c8; }
        .badge-user { background-color: #17a2b8; }
        .badge-developer { background-color: #fd7e14; }
        
        /* Dark Mode Table Styling */
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
        
        .table {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
        }
        
        .table td, .table th {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }
        
        .table-dark {
            background-color: #1a202c !important;
            color: #e2e8f0 !important;
        }
        
        .table-dark th, .table-dark td {
            background-color: #1a202c !important;
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) > td {
            background-color: #374151 !important;
        }
        
        .table-striped > tbody > tr:nth-of-type(even) > td {
            background-color: #2d3748 !important;
        }
        
        .table-hover > tbody > tr:hover > td {
            background-color: #4a5568 !important;
        }
        
        /* Force DataTables specific elements to dark mode */
        #pageVisitsTable {
            background-color: #2d3748 !important;
        }
        
        #pageVisitsTable tbody tr {
            background-color: #2d3748 !important;
        }
        
        #pageVisitsTable tbody tr:nth-of-type(odd) {
            background-color: #374151 !important;
        }
        
        #pageVisitsTable tbody tr:hover {
            background-color: #4a5568 !important;
        }
        
        #pageVisitsTable td, #pageVisitsTable th {
            background-color: inherit !important;
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }
        
        /* DataTables Dark Mode */
        .dataTables_wrapper {
            color: #e2e8f0 !important;
            background-color: #2d3748 !important;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            border: 1px solid #4a5568 !important;
            padding: 8px 15px;
            background-color: #374151 !important;
            color: #e2e8f0 !important;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #63b3ed !important;
            box-shadow: 0 0 0 0.2rem rgba(99, 179, 237, 0.25) !important;
            background-color: #374151 !important;
        }
        
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background-color: #374151 !important;
            color: #e2e8f0 !important;
            border: 1px solid #4a5568 !important;
        }
        
        .dataTables_wrapper .dataTables_length select:focus {
            background-color: #374151 !important;
            color: #e2e8f0 !important;
            border-color: #63b3ed !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e2e8f0 !important;
            background-color: #374151 !important;
            border: 1px solid #4a5568 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #4a5568 !important;
            border: 1px solid #63b3ed !important;
            color: #e2e8f0 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #3182ce !important;
            border: 1px solid #3182ce !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dataTables_info {
            color: #a0aec0 !important;
        }
        
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            color: #e2e8f0 !important;
        }
        
        /* DataTables processing indicator */
        .dataTables_processing {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
            border: 1px solid #4a5568 !important;
        }
        
        /* DataTables empty state */
        .dataTables_empty {
            background-color: #2d3748 !important;
            color: #e2e8f0 !important;
        }
        
        /* Override any remaining Bootstrap table styles */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #374151 !important;
        }
        
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #2d3748 !important;
        }
        
        /* DataTables buttons */
        .dt-buttons .btn {
            background-color: #374151 !important;
            border-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }
        
        .dt-buttons .btn:hover {
            background-color: #4a5568 !important;
            border-color: #63b3ed !important;
        }
        
        /* Form Controls Dark Mode */
        .form-control, .form-select {
            background-color: #374151;
            border: 1px solid #4a5568;
            color: #e2e8f0;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #374151;
            border-color: #63b3ed;
            color: #e2e8f0;
            box-shadow: 0 0 0 0.2rem rgba(99, 179, 237, 0.25);
        }
        
        .form-label {
            color: #e2e8f0;
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
        
        /* Text Muted Dark Mode */
        .text-muted {
            color: #a0aec0 !important;
        }
        
        /* Modal Dark Mode */
        .modal-content {
            background-color: #2d3748;
            border: 1px solid #4a5568;
        }
        
        .modal-header {
            background-color: #1a202c;
            border-bottom: 1px solid #4a5568;
            color: #e2e8f0;
        }
        
        .modal-body {
            background-color: #2d3748;
            color: #e2e8f0;
        }
        
        .modal-footer {
            background-color: #2d3748;
            border-top: 1px solid #4a5568;
        }
        
        .bg-light {
            background-color: #374151 !important;
            color: #e2e8f0;
        }
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-eye me-2"></i>Page Visit Logs</h2>
        </div>
        <div class="text-muted">
            <i class="bi bi-clock me-1"></i>
            {{ now()->format('M d, Y H:i') }}
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-eye fs-2 mb-2 text-success"></i>
                    <h4 id="totalVisits">{{ $stats['total_visits'] ?? 0 }}</h4>
                    <p class="mb-0">Total Visits</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-collection fs-2 mb-2 text-primary"></i>
                    <h4 id="uniquePages">{{ $stats['unique_pages'] ?? 0 }}</h4>
                    <p class="mb-0">Unique Pages</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-stopwatch fs-2 mb-2 text-warning"></i>
                    <h4 id="avgDuration">{{ floor($stats['avg_duration'] ?? 0) }}s</h4>
                    <p class="mb-0">Avg Duration</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 mb-2 text-info"></i>
                    <h4 id="activeUsers">{{ ($stats['admin_visits'] ?? 0) + ($stats['user_visits'] ?? 0) + ($stats['developer_visits'] ?? 0) }}</h4>
                    <p class="mb-0">Active Users</p>
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
                    <label for="userTypeFilter" class="form-label">User Type</label>
                    <select id="userTypeFilter" class="form-select">
                        <option value="">All Types</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="developer">Developer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFromFilter" class="form-label">From Date</label>
                    <input type="date" id="dateFromFilter" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="dateToFilter" class="form-label">To Date</label>
                    <input type="date" id="dateToFilter" class="form-control">
                </div>

                <div class="col-md-3">
                    <label for="userFilter" class="form-label">User</label>
                    <input type="text" id="userFilter" class="form-control" placeholder="Name or Email">
                </div>
            </div>
            <div class="row mt-2">
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

    <!-- Page Visit Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Page Visit Activity</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="pageVisitsTable" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User Type</th>
                            <th>User</th>
                            <th>Page</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Visit Details Modal removed since actions column was removed -->
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#pageVisitsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("developer.logs.page-visits.data") }}',
                    data: function(d) {
                        d.user_type = $('#userTypeFilter').val();
                        d.date_from = $('#dateFromFilter').val();
                        d.date_to = $('#dateToFilter').val();
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
                        data: null, 
                        name: 'page_title',
                        render: function(data, type, row) {
                            let html = `<strong>${row.page_title || 'Unknown Page'}</strong>`;
                            if (row.route_name) {
                                html += `<br><small class="text-muted">${row.route_name}</small>`;
                            }
                            return html;
                        }
                    },
                    { 
                        data: 'start_time', 
                        name: 'start_time',
                        render: function(data, type, row) {
                            if (data) {
                                const date = new Date(data);
                                return date.toLocaleString();
                            }
                            return 'N/A';
                        }
                    },
                    { 
                        data: 'end_time', 
                        name: 'end_time',
                        render: function(data, type, row) {
                            if (data) {
                                const date = new Date(data);
                                return date.toLocaleString();
                            }
                            return '<span class="text-muted">Active</span>';
                        }
                    },
                    { 
                        data: 'duration_seconds', 
                        name: 'duration_seconds',
                        render: function(data, type, row) {
                            if (data) {
                                const minutes = Math.floor(data / 60);
                                const seconds = data % 60;
                                return `${minutes}m ${seconds}s`;
                            }
                            return '<span class="text-warning">Active</span>';
                        }
                    }
                ],
                order: [[4, 'desc']], // Sort by Start Time descending
                pageLength: 25,
                responsive: true,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: 'No page visits found',
                    zeroRecords: 'No matching page visits found'
                }
            });
            
            // Filter event handlers
            $('#applyFilters').on('click', function() {
                table.ajax.reload();
            });
            
            $('#clearFilters').on('click', function() {
                $('#userTypeFilter').val('');
                $('#dateFromFilter').val('');
                $('#dateToFilter').val('');
                $('#userFilter').val('');
                table.ajax.reload();
            });
            
            $('#refreshData').on('click', function() {
                table.ajax.reload();
            });
        });
        
        // Visit details modal functionality removed since actions column was removed
    </script>
@endsection