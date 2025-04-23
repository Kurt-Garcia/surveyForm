@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Survey Title Section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-6 fw-bold text-primary mb-3">{{ $survey->title }} - Responses</h2>
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
                            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Response Rate</h5>
                            <div class="display-6 text-primary mb-2">{{ $responses->count() }}</div>
                            <p class="text-muted">Total responses received</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-fill text-success me-2"></i>Latest Response</h5>
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
                            <h5 class="fw-bold mb-3"><i class="bi bi-person-fill text-info me-2"></i>Unique Respondents</h5>
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
                            <h4 class="mb-0 fw-bold">Response Summary</h4>
                        </div>
                        <div class="card-body">
                            @foreach($questions as $question)
                                <div class="question-stats mb-5">
                                    <h5 class="fw-bold mb-3">{{ $question->text }}</h5>
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
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 fw-bold">Individual Responses</h4>
                            <div class="search-container w-100 w-md-50 w-lg-25">
                                <form method="GET" action="{{ route('admin.surveys.responses.index', $survey) }}">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control border-start-0" 
                                            value="{{ request('search') }}" 
                                            placeholder="Search by name, type or date..." 
                                            style="border-radius: 0 20px 20px 0;">
                                    </div>
                                </form>
                            </div>
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
                                                       class="btn btn-sm btn-outline-primary">
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
                
                // Auto-submit search form after a short delay when typing
                const searchForm = document.querySelector('.search-container form');
                const searchInput = searchForm.querySelector('input[name="search"]');
                
                let timeout = null;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        searchForm.submit();
                    }, 500);
                });
            });
            </script>
            @endpush

            <style>
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
            
            .search-container {
                max-width: 100%;
                transition: all 0.3s ease;
            }

            @media (min-width: 768px) {
                .search-container {
                    max-width: 50%;
                }
            }

            @media (min-width: 992px) {
                .search-container {
                    max-width: 300px;
                }
            }
            
            .response-row {
                transition: all 0.2s ease;
            }
            
            .response-row:hover {
                background-color: #f8f9fa;
            }
            </style>
        </div>
    </div>
</div>
@endsection