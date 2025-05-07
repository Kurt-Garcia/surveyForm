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
                    <div class="card shadow-sm border-0" id="individual-responses">
                        <div class="card-header bg-white py-3">
                            <h4 class="text-color mb-0 fw-bold">Individual Responses</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="responsesTable">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Account Type</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($responses as $response)
                                            <tr class="response-row">
                                                <td>{{ $response->account_name }}</td>
                                                <td>{{ $response->account_type }}</td>
                                                <td>{{ $response->date->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
                                                       class="btn btn-sm" 
                                                       style="border-color: var(--primary-color); color: var(--primary-color)"
                                                       onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='white'"
                                                       onmouseout="this.style.borderColor='var(--primary-color)'; this.style.backgroundColor='white'; this.style.color='var(--primary-color)'">
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
            <!-- DataTables scripts are already included in the layout -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                // Add hover effect to stat rows
                document.querySelectorAll('.stat-row').forEach(row => {
                    row.addEventListener('mouseenter', function() {
                        this.querySelector('.progress-bar').style.opacity = '0.9';
                    });
                    row.addEventListener('mouseleave', function() {
                        this.querySelector('.progress-bar').style.opacity = '1';
                    });
                });
                
                // Initialize DataTables
                $('#responsesTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search by name, type or date..."
                    },
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><""p>>',
                    initComplete: function() {
                        // Style the search input
                        $('.dataTables_filter input').addClass('form-control');
                        $('.dataTables_filter input').css({
                            'border-radius': '20px',
                            'padding-left': '15px',
                            'border-color': '#ced4da'
                        });
                        
                        // Style the length select
                        $('.dataTables_length select').addClass('form-select');
                        $('.dataTables_length select').css({
                            'border-radius': '20px',
                            'padding-left': '10px',
                            'border-color': '#ced4da'
                        });
                    },
                    // Apply custom styling to match the existing design
                    drawCallback: function() {
                        $('.paginate_button.current').css({
                            'background-color': 'var(--primary-color)',
                            'border-color': 'var(--primary-color)',
                            'color': 'white'
                        });
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
            
            .response-row {
                transition: all 0.2s ease;
            }
            
            .response-row:hover {
                background-color: #f8f9fa;
            }
            
            /* DataTables Styling */
            .dataTables_wrapper .dataTables_length, 
            .dataTables_wrapper .dataTables_filter, 
            .dataTables_wrapper .dataTables_info, 
            .dataTables_wrapper .dataTables_processing, 
            .dataTables_wrapper .dataTables_paginate {
                color: var(--text-color);
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background: var(--primary-color) !important;
                color: white !important;
                border-color: var(--primary-color) !important;
                border-radius: 4px;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: var(--primary-color) !important;
                color: white !important;
                border-color: var(--primary-color) !important;
                border-radius: 4px;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                margin: 0 3px;
                border-radius: 4px;
                transition: all 0.3s ease;
            }
            
            .dataTables_filter input:focus {
                border-color: var(--accent-color) !important;
                box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.25) !important;
            }
            
            .dataTables_length select:focus {
                border-color: var(--accent-color) !important;
                box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.25) !important;
            }
            
            table.dataTable tbody tr.even {
                background-color: #f8f9fa;
            }
            
            table.dataTable tbody tr.odd {
                background-color: white;
            }
            </style>
        </div>
    </div>
</div>
@endsection