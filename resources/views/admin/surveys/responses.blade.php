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

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="text-color fw-bold mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Response Rate</h5>
                            <div class="display-6 text-primary mb-2">{{ $responses->count() }}</div>
                            <p class="text-muted">Total responses received</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="text-color fw-bold mb-3"><i class="bi bi-calendar-fill text-success me-2"></i>Latest Response</h5>
                            <div class="display-6 text-success mb-2">
                                {{ $responses->first() ? $responses->first()->date->format('M d') : 'N/A' }}
                            </div>
                            <p class="text-muted">Last submission date</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="text-color fw-bold mb-3"><i class="bi bi-person-fill text-info me-2"></i>Unique Respondents</h5>
                            <div class="display-6 text-info mb-2">{{ $responses->unique('account_name')->count() }}</div>
                            <p class="text-muted">Individual participants</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Statistics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h4 class="text-color mb-0 fw-bold">Response Summary</h4>
                        </div>
                        <div class="card-body">
                            @foreach($questions as $question)
                                <div class="question-stats mb-5">
                                    <h5 class="text-color fw-bold mb-3">{{ $question->text }}</h5>
                                    @php
                                        $stats = $statistics[$question->id];
                                        $total = array_sum($stats['responses']);
                                    @endphp
                                    
                                    @if($question->type === 'radio' || $question->type === 'star')
                                        <div class="stats-bars">
                                            @foreach($stats['responses'] as $response => $count)
                                                <div class="stat-row mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>{{ $response == 1 ? 'Poor' : ($response == 2 ? 'Need Improvement' : ($response == 3 ? 'Satisfactory' : ($response == 4 ? 'Very Satisfactory' : 'Excellent'))) }}</span>
                                                        <span class="text-muted">{{ $count }} responses</span>
                                                    </div>
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar {{ $response == 1 ? 'bg-danger' : ($response == 2 ? 'bg-warning' : ($response == 3 ? 'bg-info' : ($response == 4 ? 'bg-primary' : 'bg-success'))) }}" 
                                                             role="progressbar" 
                                                             style="width: {{ ($count / $total) * 100 }}%">
                                                            {{ round(($count / $total) * 100) }}%
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
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
                                <h4 class="text-color mb-0 fw-bold d-flex align-items-center">
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
                                            <th class="ps-4">Account Name</th>
                                            <th>Account Type</th>
                                            <th>Date</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($responses as $response)
                                            <tr class="response-row">
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle bg-primary-subtle text-primary me-3">
                                                            {{ substr($response->account_name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">{{ $response->account_name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge rounded-pill {{ $response->account_type == 'Student' ? 'bg-info-subtle text-info' : ($response->account_type == 'Teacher' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary') }} px-3 py-2">
                                                        <i class="bi {{ $response->account_type == 'Student' ? 'bi-mortarboard-fill' : ($response->account_type == 'Teacher' ? 'bi-person-workspace' : 'bi-person-fill') }} me-1"></i>
                                                        {{ $response->account_type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar-date me-2 text-muted"></i>
                                                        <span>{{ $response->date->format('M d, Y') }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">
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
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
            <!-- DataTables scripts are already included in the layout -->
            <script>
                $(document).ready(function() {
                    // Add hover effect to stat rows
                    document.querySelectorAll('.stat-row').forEach(row => {
                        row.addEventListener('mouseenter', function() {
                            this.querySelector('.progress-bar').style.opacity = '0.9';
                        });
                        row.addEventListener('mouseleave', function() {
                            this.querySelector('.progress-bar').style.opacity = '1';
                        });
                    });
                    
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
                        dom: '<"row mb-4 mt-3"<"col-md-6 d-flex gap-2 ps-4"Bl><"col-md-6 pe-4"f>>rt<"row align-items-center"<"col-md-6"i><"col-md-6"p>>',
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
                                        }
                                    },
                                    {
                                        extend: 'csv',
                                        text: '<i class="bi bi-filetype-csv me-1"></i> CSV',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}'
                                    },
                                    {
                                        extend: 'excel',
                                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}'
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
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        text: '<i class="bi bi-printer me-1"></i> Print',
                                        className: 'export-item',
                                        exportOptions: {
                                            columns: [0, 1, 2]
                                        },
                                        title: 'Survey Responses - {{ $survey->title }}'
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

            .progress {
                border-radius: 100px;
                background-color: #f8f9fa;
                box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
                height: 25px !important;
            }

            .progress-bar {
                border-radius: 100px;
                transition: all 0.4s ease;
                background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
                background-size: 1rem 1rem;
                animation: progress-bar-stripes 1s linear infinite;
            }

            .bg-danger { background-color: #dc3545 !important; }
            .bg-warning { background-color: #ffc107 !important; }
            .bg-info { background-color: #17a2b8 !important; }
            .bg-primary { background-color: #0d6efd !important; }
            .bg-success { background-color: #28a745 !important; }

            .stat-row:hover .progress-bar {
                filter: brightness(1.1);
                transform: scaleX(1.01);
            }
            @keyframes progress-bar-stripes {
                from { background-position: 1rem 0; }
                to { background-position: 0 0; }
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
                margin: 0 3px;
                padding: 2px 16px;
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