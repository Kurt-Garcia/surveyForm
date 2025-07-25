@extends('developer.layouts.app')

@section('title', 'Login Activity Logs - Developer Portal')

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
        .badge-login { background-color: #28a745; }
        .badge-logout { background-color: #dc3545; }
        .badge-admin { background-color: #6f42c1; }
        .badge-superadmin { background-color: #5a23c8; }
        .badge-user { background-color: #17a2b8; }
        .badge-developer { background-color: #fd7e14; }
        .badge-resubmission_allowed { background-color: #28a745; color: white; }
        .badge-resubmission_disabled { background-color: #dc3545; color: white; }
        
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
        #loginTable {
            background-color: #2d3748 !important;
        }
        
        #loginTable tbody tr {
            background-color: #2d3748 !important;
        }
        
        #loginTable tbody tr:nth-of-type(odd) {
            background-color: #374151 !important;
        }
        
        #loginTable tbody tr:hover {
            background-color: #4a5568 !important;
        }
        
        #loginTable td, #loginTable th {
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
        
        /* IP Address Styling */
        .ip-address {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            background-color: #374151;
            color: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #4a5568;
        }
        
        /* Text Muted Dark Mode */
        .text-muted {
            color: #a0aec0 !important;
        }
        
        /* Breadcrumb Dark Mode */
        .breadcrumb {
            background-color: transparent;
        }
        
        .breadcrumb-item a {
            color: #63b3ed;
        }
        
        .breadcrumb-item.active {
            color: #a0aec0;
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
        
        /* Alert Dark Mode */
        .alert-info {
            background-color: #2c5aa0;
            border-color: #3182ce;
            color: #e2e8f0;
        }
        
        .alert-secondary {
            background-color: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
        }
        
        .bg-light {
            background-color: #374151 !important;
            color: #e2e8f0;
        }
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-box-arrow-in-right me-2"></i>Login Logs</h2>
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
                    <i class="bi bi-box-arrow-in-right fs-2 mb-2 text-success"></i>
                    <h4 id="todayLogins">{{ $stats['today_logins'] ?? 0 }}</h4>
                    <p class="mb-0">Today's Logins</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 mb-2 text-primary"></i>
                    <h4 id="uniqueUsers">{{ $stats['unique_users'] ?? 0 }}</h4>
                    <p class="mb-0">Unique Users Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-2 mb-2 text-warning"></i>
                    <h4 id="recentActivity">{{ $stats['recent_activity'] ?? 0 }}</h4>
                    <p class="mb-0">Last Hour</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt fs-2 mb-2 text-info"></i>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Browser & OS Details</h5>
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
                                // Check if the admin is a superadmin
                                if (row.is_superadmin) {
                                    badgeClass = 'badge-superadmin';
                                    displayText = 'Super Admin';
                                } else {
                                    badgeClass = 'badge-admin';
                                    displayText = 'Admin';
                                }
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
                            
                            // Extract browser information
                            let browser = 'Unknown';
                            let browserIcon = 'question-circle';
                            
                            if (data.includes('Chrome') && !data.includes('Edg/')) {
                                browser = 'Chrome';
                                browserIcon = 'browser-chrome text-warning';
                            } else if (data.includes('Firefox')) {
                                browser = 'Firefox';
                                browserIcon = 'browser-firefox text-danger';
                            } else if (data.includes('Safari') && !data.includes('Chrome')) {
                                browser = 'Safari';
                                browserIcon = 'browser-safari text-primary';
                            } else if (data.includes('Edg/')) {
                                browser = 'Edge';
                                browserIcon = 'browser-edge text-info';
                            }
                            
                            // Extract OS information
                            let os = 'Unknown';
                            let osIcon = 'question-circle';
                            
                            if (data.includes('Windows')) {
                                os = 'Windows';
                                osIcon = 'windows text-primary';
                            } else if (data.includes('Mac')) {
                                os = 'macOS';
                                osIcon = 'apple text-dark';
                            } else if (data.includes('Linux')) {
                                os = 'Linux';
                                osIcon = 'ubuntu text-warning';
                            } else if (data.includes('Android')) {
                                os = 'Android';
                                osIcon = 'android text-success';
                            } else if (data.includes('iPhone') || data.includes('iPad') || data.includes('iOS')) {
                                os = 'iOS';
                                osIcon = 'phone text-dark';
                            }
                            
                            // Display browser and OS using custom PNG images and Bootstrap icons
                            let browserImg = '';
                            
                            // Use custom PNG images for browsers
                            if (browser === 'Chrome') {
                                browserImg = `<img src="{{ asset('img/chrome.png') }}" height="24" title="${browser}" alt="Chrome Browser">`;
                            } else if (browser === 'Firefox') {
                                browserImg = `<img src="{{ asset('img/firefox.png') }}" height="24" title="${browser}" alt="Firefox Browser">`;
                            } else if (browser === 'Safari') {
                                browserImg = `<img src="{{ asset('img/safari.png') }}" height="24" title="${browser}" alt="Safari Browser">`;
                            } else if (browser === 'Edge') {
                                browserImg = `<img src="{{ asset('img/edge.png') }}" height="24" title="${browser}" alt="Edge Browser">`;
                            } else {
                                browserImg = `<i class="bi bi-${browserIcon} fs-5" title="${browser}"></i>`;
                            }
                            
                            return `<div title="${browser} / ${os}" style="cursor: pointer;" onclick="showUserAgent('${encodeURIComponent(data)}')">
                                ${browserImg} / <i class="bi bi-${osIcon} fs-5" title="${os}"></i>
                            </div>`;
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
            
            // Extract browser information
            let browser = 'Unknown';
            let browserIcon = 'question-circle';
            let browserVersion = '';
            
            if (decodedAgent.includes('Chrome') && !decodedAgent.includes('Edg/')) {
                browser = 'Chrome';
                browserIcon = 'browser-chrome text-warning';
                const chromeMatch = decodedAgent.match(/Chrome\/(\d+\.\d+)/i);
                if (chromeMatch) browserVersion = chromeMatch[1];
            } else if (decodedAgent.includes('Firefox')) {
                browser = 'Firefox';
                browserIcon = 'browser-firefox text-danger';
                const firefoxMatch = decodedAgent.match(/Firefox\/(\d+\.\d+)/i);
                if (firefoxMatch) browserVersion = firefoxMatch[1];
            } else if (decodedAgent.includes('Safari') && !decodedAgent.includes('Chrome')) {
                browser = 'Safari';
                browserIcon = 'browser-safari text-primary';
                const safariMatch = decodedAgent.match(/Version\/(\d+\.\d+)/i);
                if (safariMatch) browserVersion = safariMatch[1];
            } else if (decodedAgent.includes('Edg/')) {
                browser = 'Edge';
                browserIcon = 'browser-edge text-info';
                const edgeMatch = decodedAgent.match(/Edg\/(\d+\.\d+)/i);
                if (edgeMatch) browserVersion = edgeMatch[1];
            }
            
            // Extract OS information
            let os = 'Unknown';
            let osIcon = 'question-circle';
            let osVersion = '';
            
            if (decodedAgent.includes('Windows')) {
                os = 'Windows';
                osIcon = 'windows text-primary';
                const windowsMatch = decodedAgent.match(/Windows NT (\d+\.\d+)/i);
                if (windowsMatch) {
                    const ntVersion = windowsMatch[1];
                    switch(ntVersion) {
                        case '10.0': osVersion = '10/11'; break;
                        case '6.3': osVersion = '8.1'; break;
                        case '6.2': osVersion = '8'; break;
                        case '6.1': osVersion = '7'; break;
                        case '6.0': osVersion = 'Vista'; break;
                        case '5.1': osVersion = 'XP'; break;
                        default: osVersion = ntVersion;
                    }
                }
            } else if (decodedAgent.includes('Mac')) {
                os = 'macOS';
                osIcon = 'apple text-dark';
                const macMatch = decodedAgent.match(/Mac OS X (\d+[._]\d+)/i);
                if (macMatch) osVersion = macMatch[1].replace('_', '.');
            } else if (decodedAgent.includes('Linux')) {
                os = 'Linux';
                osIcon = 'ubuntu text-warning';
            } else if (decodedAgent.includes('Android')) {
                os = 'Android';
                osIcon = 'android text-success';
                const androidMatch = decodedAgent.match(/Android (\d+\.\d+)/i);
                if (androidMatch) osVersion = androidMatch[1];
            } else if (decodedAgent.includes('iPhone') || decodedAgent.includes('iPad') || decodedAgent.includes('iOS')) {
                os = 'iOS';
                osIcon = 'phone text-dark';
                const iosMatch = decodedAgent.match(/OS (\d+[._]\d+)/i);
                if (iosMatch) osVersion = iosMatch[1].replace('_', '.');
            }
            
            // Display detailed browser and OS info
            html += '<div class="row mt-3">';
            html += '<div class="col-md-6">';
            html += '<div class="card h-100">';
            html += '<div class="card-body">';
            html += '<h6 class="card-title"><i class="bi bi-globe2"></i> Browser Information</h6>';
            // Use custom PNG images for browsers in the modal
            let browserIconHtml = '';
            if (browser === 'Chrome') {
                browserIconHtml = `<img src="{{ asset('img/chrome.png') }}" height="48" class="me-3" alt="Chrome Browser">`;
            } else if (browser === 'Firefox') {
                browserIconHtml = `<img src="{{ asset('img/firefox.png') }}" height="48" class="me-3" alt="Firefox Browser">`;
            } else if (browser === 'Safari') {
                browserIconHtml = `<img src="{{ asset('img/safari.png') }}" height="48" class="me-3" alt="Safari Browser">`;
            } else if (browser === 'Edge') {
                browserIconHtml = `<img src="{{ asset('img/edge.png') }}" height="48" class="me-3" alt="Edge Browser">`;
            } else {
                browserIconHtml = `<i class="bi bi-${browserIcon} fs-1 me-3"></i>`;
            }
            
            html += `<div class="d-flex align-items-center mb-3">
                ${browserIconHtml}
                <div>
                    <h5 class="mb-0">${browser}</h5>
                    ${browserVersion ? `<span class="text-muted">Version ${browserVersion}</span>` : ''}
                </div>
            </div>`;
            
            // Try to extract additional browser details
            if (decodedAgent.includes('Mobile')) {
                html += '<div class="alert alert-info"><i class="bi bi-phone"></i> Mobile Browser</div>';
            }
            
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="col-md-6">';
            html += '<div class="card h-100">';
            html += '<div class="card-body">';
            html += '<h6 class="card-title"><i class="bi bi-cpu"></i> Operating System</h6>';
            html += `<div class="d-flex align-items-center mb-3">
                <i class="bi bi-${osIcon} fs-1 me-3"></i>
                <div>
                    <h5 class="mb-0">${os}</h5>
                    ${osVersion ? `<span class="text-muted">Version ${osVersion}</span>` : ''}
                </div>
            </div>`;
            
            // Try to extract additional OS details
            if (decodedAgent.includes('x64') || decodedAgent.includes('x86_64')) {
                html += '<div class="alert alert-secondary"><i class="bi bi-cpu"></i> 64-bit Architecture</div>';
            } else if (decodedAgent.includes('x86') || decodedAgent.includes('i686')) {
                html += '<div class="alert alert-secondary"><i class="bi bi-cpu"></i> 32-bit Architecture</div>';
            }
            
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            $('#userAgentDetails').html(html);
            $('#userAgentModal').modal('show');
        }
    </script>
@endsection