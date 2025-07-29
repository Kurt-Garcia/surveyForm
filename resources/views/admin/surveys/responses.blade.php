@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-start">
        <div class="col-12">
            <!-- Survey Title Section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-6 fw-bold mb-3 text-color">{{ strtoupper($survey->title) }} - RESPONSES</h2>
                        <div class="d-flex gap-3 text-muted">
                            <div><i class="bi bi-people-fill me-2"></i>{{ $responses->count() }} Respondents</div>
                            <div><i class="bi bi-star-fill me-2"></i>Average Rating: {{ number_format($avgRecommendation, 1) }}/10</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-gradient-primary">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="action-icon me-3">
                                        <i class="bi bi-speedometer2 text-white fs-2"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-white mb-1 fw-bold">Survey Analytics Dashboard</h5>
                                        <p class="text-white-50 mb-0">Comprehensive insights and reporting tools</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-3">
                                    <a href="{{ route('admin.surveys.report', $survey->id) }}" 
                                       class="btn btn-light btn-lg rounded-pill px-4 shadow-sm report-btn-new">
                                        <i class="bi bi-file-earmark-bar-graph me-2"></i>View Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stats-card-modern">
                        <div class="card-body position-relative">
                            <div class="stats-icon bg-primary-subtle text-primary">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <div class="stats-content">
                                <h3 class="display-5 fw-bold text-primary mb-2">{{ $responses->count() }}</h3>
                                <h6 class="text-color fw-semibold mb-1">Total Responses</h6>
                                <p class="text-muted small mb-0">Responses received for this survey</p>
                            </div>
                            <div class="stats-trend">
                                <span class="badge bg-primary-subtle text-primary rounded-pill">
                                    <i class="bi bi-arrow-up me-1"></i>Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 stats-card-modern">
                        <div class="card-body position-relative">
                            <div class="stats-icon bg-success-subtle text-success">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="stats-content">
                                <h3 class="display-5 fw-bold text-success mb-2">
                                    {{ $responses->first() ? $responses->first()->date->format('M d') : 'N/A' }}
                                </h3>
                                <h6 class="text-color fw-semibold mb-1">Latest Response</h6>
                                <p class="text-muted small mb-0">Most recent submission date</p>
                            </div>
                            <div class="stats-trend">
                                <span class="badge bg-success-subtle text-success rounded-pill">
                                    <i class="bi bi-clock me-1"></i>Recent
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card border-0 shadow-sm h-100 stats-card-modern">
                        <div class="card-body position-relative">
                            <div class="stats-icon bg-info-subtle text-info">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stats-content">
                                <h3 class="display-5 fw-bold text-info mb-2">{{ $responses->unique('account_name')->count() }}</h3>
                                <h6 class="text-color fw-semibold mb-1">Unique Respondents</h6>
                                <p class="text-muted small mb-0">Individual participants engaged</p>
                            </div>
                            <div class="stats-trend">
                                <span class="badge bg-info-subtle text-info rounded-pill">
                                    <i class="bi bi-person-check me-1"></i>Unique
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Individual Responses Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" id="individual-responses">
                        <div class="card-header bg-gradient-primary py-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-people-fill me-2 text-primary"></i>Individual Responses
                                </h4>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 me-2">
                                        <i class="bi bi-person-check-fill me-1"></i> {{ $responses->count() }} Total
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle modern-table" id="responsesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4 align-middle">Account Name</th>
                                            <th class="align-middle">Account Type</th>
                                            <th class="align-middle">Date</th>
                                            <th class="align-middle text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($responses as $response)
                                            <tr class="response-row">
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            @php
                                                                $customer = DB::table('TBLCUSTOMER')->where('CUSTNAME', $response->account_name)->first();
                                                                $custcode = $customer ? $customer->CUSTCODE : '';
                                                                $displayAccountName = $custcode ? $custcode . ' - ' . $response->account_name : $response->account_name;
                                                            @endphp
                                                            <h6 class="mb-0 fw-semibold">{{ $displayAccountName }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $response->account_type }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar-date me-2 text-muted"></i>
                                                        <span>{{ $response->date->format('M d, Y') }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end align-middle pe-4">
                                                    <a href="{{ route('admin.surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
                                                       class="btn btn-primary btn-sm rounded-pill px-3 action-btn">
                                                        <i class="bi bi-eye-fill me-1"></i>View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
            <!-- DataTables scripts are already included in the layout -->
            <script>
                $(document).ready(function() {
                    
                    // Initialize DataTables with proper configuration and export buttons
                    $('#responsesTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        ordering: false,
                        language: {
                            search: "<i class='bi bi-search'></i>",
                            searchPlaceholder: "Search responses...",
                            lengthMenu: "_MENU_ per page",
                            info: "Showing <span class='fw-semibold'>_START_</span> to <span class='fw-semibold'>_END_</span> of <span class='fw-semibold'>_TOTAL_</span> responses",
                            paginate: {
                                first: "<i class='bi bi-chevron-double-left'></i>",
                                last: "<i class='bi bi-chevron-double-right'></i>",
                                next: "<i class='bi bi-chevron-right'></i>",
                                previous: "<i class='bi bi-chevron-left'></i>"
                            },
                            emptyTable: "<div class='text-center py-5'><i class='bi bi-inbox-fill text-muted fs-1 mb-3'></i><p class='text-muted'>No responses found</p></div>"
                        },
                        dom: '<"row mb-4 mt-3"<"col-md-6 d-flex gap-2 ps-4"Bl><"col-md-6 pe-4"f>>rt<"row align-items-center py-4 mt-3"<"col-md-6"i><"col-md-6"p>>',
                        buttons: [
                            {
                                extend: 'collection',
                                text: '<i class="bi bi-download me-1"></i> Export Data',
                                className: 'btn btn-primary btn-sm rounded-pill px-3 export-btn',
                                buttons: [
                                    {
                                        extend: 'copy',
                                        text: '<i class="bi bi-clipboard me-1"></i> Copy',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        action: function(e, dt, button, config) {
                                            try {
                                                // Log the export activity
                                                $.ajax({
                                                    url: '{{ route("admin.log.export") }}',
                                                    method: 'POST',
                                                    data: {
                                                        _token: '{{ csrf_token() }}',
                                                        export_type: 'copy',
                                                        entity_type: 'responses',
                                                        survey_title: '{{ $survey->title }}'
                                                    },
                                                    async: false,
                                                    success: function() {
                                                        // Call the original action after successful logging
                                                        $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this),
                                                    error: function(xhr, status, error) {
                                                        console.error('Logging error:', error);
                                                        // Still proceed with export even if logging fails
                                                        $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this)
                                                });
                                            } catch (error) {
                                                console.error('Copy export error:', error);
                                                alert('Error copying data: ' + error.message);
                                            }
                                        }
                                    },
                                    {
                                        extend: 'csv',
                                        text: '<i class="bi bi-filetype-csv me-1"></i> CSV',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}',
                                        action: function(e, dt, button, config) {
                                            try {
                                                // Log the export activity
                                                $.ajax({
                                                    url: '{{ route("admin.log.export") }}',
                                                    method: 'POST',
                                                    data: {
                                                        _token: '{{ csrf_token() }}',
                                                        export_type: 'csv',
                                                        entity_type: 'responses',
                                                        survey_title: '{{ $survey->title }}'
                                                    },
                                                    async: false,
                                                    success: function() {
                                                        // Call the original action after successful logging
                                                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this),
                                                    error: function(xhr, status, error) {
                                                        console.error('Logging error:', error);
                                                        // Still proceed with export even if logging fails
                                                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this)
                                                });
                                            } catch (error) {
                                                console.error('CSV export error:', error);
                                                alert('Error exporting CSV: ' + error.message);
                                            }
                                        }
                                    },
                                    {
                                        extend: 'excel',
                                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}',
                                        action: function(e, dt, button, config) {
                                            try {
                                                // Log the export activity
                                                $.ajax({
                                                    url: '{{ route("admin.log.export") }}',
                                                    method: 'POST',
                                                    data: {
                                                        _token: '{{ csrf_token() }}',
                                                        export_type: 'excel',
                                                        entity_type: 'responses',
                                                        survey_title: '{{ $survey->title }}'
                                                    },
                                                    async: false,
                                                    success: function() {
                                                        // Call the original action after successful logging
                                                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this),
                                                    error: function(xhr, status, error) {
                                                        console.error('Logging error:', error);
                                                        // Still proceed with export even if logging fails
                                                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this)
                                                });
                                            } catch (error) {
                                                console.error('Excel export error:', error);
                                                alert('Error exporting Excel: ' + error.message);
                                            }
                                        }
                                    },
                                    {
                                        extend: 'pdf',
                                        text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}',
                                        customize: function(doc) {
                                            doc.defaultStyle.fontSize = 10;
                                            doc.styles.tableHeader.fontSize = 11;
                                            doc.styles.tableHeader.alignment = 'left';
                                            doc.content[1].table.widths = ['*', '*', '*'];
                                        },
                                        action: function(e, dt, button, config) {
                                            try {
                                                // Log the export activity
                                                $.ajax({
                                                    url: '{{ route("admin.log.export") }}',
                                                    method: 'POST',
                                                    data: {
                                                        _token: '{{ csrf_token() }}',
                                                        export_type: 'pdf',
                                                        entity_type: 'responses',
                                                        survey_title: '{{ $survey->title }}'
                                                    },
                                                    async: false,
                                                    success: function() {
                                                        // Call the original action after successful logging
                                                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this),
                                                    error: function(xhr, status, error) {
                                                        console.error('Logging error:', error);
                                                        // Still proceed with export even if logging fails
                                                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                                                    }.bind(this)
                                                });
                                            } catch (error) {
                                                console.error('PDF export error:', error);
                                                alert('Error exporting PDF: ' + error.message);
                                            }
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        text: '<i class="bi bi-printer me-1"></i> Print',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}',
                                        action: function(e, dt, button, config) {
                                            try {
                                                // Log the export activity
                                                $.ajax({
                                                    url: '{{ route("admin.log.export") }}',
                                                    method: 'POST',
                                                    data: {
                                                        _token: '{{ csrf_token() }}',
                                                        export_type: 'print',
                                                        entity_type: 'responses',
                                                        survey_title: '{{ $survey->title }}'
                                                    },
                                                    async: false,
                                                    success: function() {
                                                        // Call the original action after successful logging
                                                        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                                                    }.bind(this),
                                                    error: function(xhr, status, error) {
                                                        console.error('Logging error:', error);
                                                        // Still proceed with export even if logging fails
                                                        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                                                    }.bind(this)
                                                });
                                            } catch (error) {
                                                console.error('Print export error:', error);
                                                alert('Error printing: ' + error.message);
                                            }
                                        }
                                    }
                                ]
                            }
                        ],
                        initComplete: function() {
                            // Style the search input
                            $('.dataTables_filter input').addClass('form-control search-input');
                            $('.dataTables_filter label').addClass('position-relative');
                            $('.dataTables_filter i').addClass('search-icon');
                            
                            // Style the length select
                            $('.dataTables_length select').addClass('form-select rounded-pill btn-sm');
                            $('.dataTables_length').addClass('d-flex align-items-center');
                            
                            // Add animation to rows
                            $('.response-row').each(function(index) {
                                $(this).css({
                                    'animation-delay': (index * 0.05) + 's'
                                });
                            });
                        },
                        // Apply custom styling to match the modern design
                        drawCallback: function() {
                            // Style pagination buttons
                            $('.paginate_button').addClass('rounded-pill');
                            $('.paginate_button.current').addClass('active-page');
                            
                            // Add spacing to pagination container
                            $('.dataTables_paginate').addClass('pe-4');
                            $('.dataTables_info').addClass('ps-4');
                            
                            // Add hover effect to rows
                            $('.response-row').off('mouseenter mouseleave');
                            $('.response-row').hover(
                                function() {
                                    $(this).addClass('row-hover');
                                },
                                function() {
                                    $(this).removeClass('row-hover');
                                }
                            );
                        }
                    });
                    
                    // Smooth scroll to table when URL has fragment
                    if (window.location.hash === '#individual-responses') {
                        const element = document.querySelector('#individual-responses');
                        if (element) {
                            element.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
                
                // Refresh data function
                function refreshData() {
                    // Show loading state
                    const refreshBtn = document.querySelector('.btn-outline-light');
                    const originalContent = refreshBtn.innerHTML;
                    refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2 spin"></i>Refreshing...';
                    refreshBtn.disabled = true;
                    
                    // Simulate refresh (reload page)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            </script>
            @endpush

            <style>
            .text-color {
                color: var(--text-color);
            }

            .question-stats {
                border-bottom: 1px solid #eee;
                padding-bottom: 2rem;
            }

            .question-stats:last-child {
                border-bottom: none;
            }

            .chart-container {
                margin-bottom: 1rem;
                border-radius: 8px;
                background-color: #ffffff;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                padding: 10px;
            }

            .legend-container {
                padding: 15px;
                border-radius: 8px;
                background-color: #ffffff;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .legend-item {
                padding: 8px 12px;
                border-radius: 6px;
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .legend-color {
                width: 16px;
                height: 16px;
                border-radius: 50%;
                display: inline-block;
            }

            .question-chart {
                transition: all 0.3s ease;
            }

            .question-chart:hover {
                transform: scale(1.02);
            }

            .question-stats {
                transition: all 0.3s ease;
            }

            .stat-row {
                transition: all 0.3s ease;
                padding: 10px;
                border-radius: 8px;
            }

            .stat-row:hover {
                background-color: #f8f9fa;
            }

            .card {
                transition: transform 0.2s;
            }

            .card:hover {
                transform: translateY(-5px);
            }
            
            /* Modern Action Bar Styling */
            .bg-gradient-primary {
                background: linear-gradient(135deg, var(--primary-color, #007bff) 0%, var(--secondary-color, #6610f2) 100%) !important;
            }
            
            .action-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                backdrop-filter: blur(10px);
            }
            
            .report-btn-new {
                background: #fff;
                color: var(--primary-color, #007bff);
                border: none;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            
            .report-btn-new:hover {
                background: #f8f9fa;
                color: var(--primary-color, #007bff);
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            }
            
            /* Modern Statistics Cards */
            .stats-card-modern {
                transition: all 0.3s ease;
                border-radius: 16px;
                overflow: hidden;
                position: relative;
            }
            
            .stats-card-modern:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }
            
            .stats-card-modern .card-body {
                padding: 2rem;
            }
            
            .stats-icon {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                transition: all 0.3s ease;
            }
            
            .stats-card-modern:hover .stats-icon {
                transform: scale(1.1) rotate(5deg);
            }
            
            .stats-content {
                margin-right: 70px;
            }
            
            .stats-trend {
                position: absolute;
                bottom: 1.5rem;
                right: 1.5rem;
            }
            
            .stats-trend .badge {
                font-size: 0.75rem;
                padding: 0.5rem 0.75rem;
            }
            
            /* Spinning animation for refresh button */
            .spin {
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            /* Enhanced responsive design */
            @media (max-width: 992px) {
                .action-icon {
                    width: 50px;
                    height: 50px;
                }
                
                .stats-card-modern .card-body {
                    padding: 1.5rem;
                }
                
                .stats-icon {
                    width: 40px;
                    height: 40px;
                    font-size: 1.25rem;
                }
                
                .stats-content {
                    margin-right: 60px;
                }
            }
            
            @media (max-width: 768px) {
                .bg-gradient-primary .card-body {
                    padding: 1.5rem;
                }
                
                .d-flex.justify-content-between {
                    flex-direction: column;
                    gap: 1rem;
                }
                
                .d-flex.gap-3 {
                    justify-content: center;
                    width: 100%;
                }
                
                .report-btn-new, .btn-outline-light {
                    flex: 1;
                    min-width: 140px;
                }
                
                .stats-card-modern .card-body {
                    padding: 1.25rem;
                }
                
                .stats-icon {
                    position: relative;
                    top: auto;
                    right: auto;
                    margin-bottom: 1rem;
                    align-self: flex-start;
                }
                
                .stats-content {
                    margin-right: 0;
                }
                
                .stats-trend {
                    position: relative;
                    bottom: auto;
                    right: auto;
                    margin-top: 1rem;
                }
            }
            
            /* Modern Table Styling */
            .modern-table {
                border-collapse: separate;
                border-spacing: 0;
                width: 100%;
                border: none;
            }
            
            .modern-table thead th {
                font-weight: 600;
                color: var(--text-color);
                border-bottom: 2px solid #f0f0f0;
                padding: 1rem;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .response-row {
                transition: all 0.3s ease;
                animation: fadeIn 0.5s ease forwards;
                opacity: 0;
                border-bottom: 1px solid #f5f5f5;
            }
            
            .response-row td {
                padding: 1rem;
                vertical-align: middle;
            }
            
            .row-hover {
                background-color: rgba(var(--bs-primary-rgb), 0.03);
                box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            }
            
            /* Avatar Circle */
            .avatar-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 1.2rem;
                text-transform: uppercase;
            }
            
            /* Action Button */
            .action-btn {
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb), 0.2);
            }
            
            .action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.3);
            }
            
            /* Search Input Styling */
            .search-input {
                border-radius: 50px !important;
                padding-left: 40px !important;
                padding-right: 15px !important;
                border: 1px solid #e0e0e0;
                height: 40px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                transition: all 0.3s ease;
            }
            
            .search-input:focus {
                box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.15);
                border-color: var(--primary-color);
            }
            
            .search-icon {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #aaa;
                z-index: 10;
            }
            
            /* Card Header Gradient */
            .bg-gradient-primary {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                color: white;
            }
            
            /* Animation */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            /* Export Button Styling */
            .export-btn {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                color: white !important;
                border: none;
                box-shadow: 0 4px 15px rgba(var(--bs-primary-rgb), 0.2);
                transition: all 0.3s ease;
            }
            
            .export-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(var(--bs-primary-rgb), 0.3);
            }
            
            .export-item {
                transition: all 0.2s ease;
                padding: 8px 16px;
                border-radius: 4px;
            }
            
            .export-item:hover {
                background-color: rgba(var(--bs-primary-rgb), 0.1);
                color: var(--primary-color) !important;
            }
            
            /* Pagination Styling */
            .paginate_button {
                margin: 0 3px;
                transition: all 0.3s ease;
            }
            
            .active-page {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
                color: white !important;
                border: none !important;
                box-shadow: 0 4px 10px rgba(var(--bs-primary-rgb), 0.2);
            }
            
            /* DataTables Info Styling */
            .dataTables_info {
                color: #6c757d;
                font-size: 0.9rem;
                padding-top: 1rem;
            }
            
            /* Responsive Adjustments */
            @media (max-width: 768px) {
                .avatar-circle {
                    width: 35px;
                    height: 35px;
                    font-size: 1rem;
                }
                
                .modern-table thead th {
                    padding: 0.75rem;
                    font-size: 0.8rem;
                }
                
                .response-row td {
                    padding: 0.75rem;
                }
            }
            
            /* Modern Export Buttons */
            .dt-button {
                background: linear-gradient(90deg, #fff 0%, #f0f4fa 100%);
                color: var(--text-color) !important;
                border: 1px solid var(--primary-color);
                border-radius: 18px !important;
                box-shadow: 0 1px 4px rgba(0,0,0,0.07);
                padding: 8px 22px !important;
                margin: 0 6px 8px 0;
                font-weight: 600;
                transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.2s;
                outline: none;
            }
            .dt-button:hover, .dt-button:focus {
                background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%) !important;
                color: #fff !important;
                box-shadow: 0 4px 16px rgba(0,0,0,0.13);
                transform: translateY(-2px) scale(1.04);
                border: none;
            }
            .dt-button:active {
                background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                transform: scale(0.98);
                color: #fff !important;
            }

            /* Modern Pagination Buttons */
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                cursor: pointer;
                background: linear-gradient(90deg, #fff 0%, #f0f4fa 100%);
                border: 1px solid var(--primary-color);
                color: var(--primary-color) !important;
                border-radius: 18px !important;
                margin: 0 5px;
                padding: 0.5em 1em !important;
                font-weight: 500;
                box-shadow: 0 1px 4px rgba(0,0,0,0.07);
                transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.2s;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current,
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
                color: #fff !important;
                border: none;
                box-shadow: 0 2px 8px rgba(0,0,0,0.10);
                transform: translateY(-1px) scale(1.03);
            }
            
            /* Additional spacing for pagination area */
            .dataTables_wrapper .row:last-child {
                margin-bottom: 1.5rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(0,0,0,0.05);
            }
            
            .dataTables_info {
                padding-top: 0.5rem !important;
                margin-left: 0.5rem;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
                background: #f8f9fa !important;
                color: #bdbdbd !important;
                border: 1px solid #e0e0e0 !important;
                box-shadow: none;
                cursor: not-allowed;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:active {
                transform: scale(0.97);
            }
            </style>
        </div>
    </div>
</div>
@endsection