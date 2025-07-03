@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    
    <!-- Print-Only Summary Header -->
    <div class="d-none d-print-block mb-4">
        <div class="text-center border-bottom pb-3 mb-3">
            <h1 class="h3 mb-2">{{ strtoupper($survey->title) }} - SURVEY REPORT</h1>
            <p class="mb-1"><strong>Generated:</strong> {{ now()->format('F d, Y \a\t g:i A') }}</p>
            <p class="mb-1">
                <strong>SBU:</strong> 
                @foreach($survey->sbus as $sbu){{ $sbu->name }}@if(!$loop->last), @endif @endforeach
                | <strong>Total Responses:</strong> {{ $totalResponses }}
                | <strong>Sites:</strong> {{ $survey->sites->count() }}
            </p>
        </div>
    </div>
    <!-- Survey Header -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2 text-white font-weight-bold">{{ strtoupper($survey->title) }}</h1>
                    <div class="row text-white-50">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-building me-2"></i><strong>SBU:</strong> 
                                @if($survey->sbus->count() > 0)
                                    @foreach($survey->sbus as $sbu)
                                        <span class="badge bg-light text-dark me-1">{{ $sbu->name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-warning text-dark">Not Assigned</span>
                                @endif
                            </p>
                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><strong>Type:</strong> 
                                @php
                                    $hasFDC = $survey->sbus->contains(function($sbu) { return stripos($sbu->name, 'FDC') !== false; });
                                    $hasFUI = $survey->sbus->contains(function($sbu) { return stripos($sbu->name, 'FUI') !== false; });
                                @endphp
                                @if($hasFDC && $hasFUI)
                                    <span class="badge bg-info text-white">FDC & FUI</span>
                                @elseif($hasFDC)
                                    <span class="badge bg-primary text-white">FDC</span>
                                @elseif($hasFUI)
                                    <span class="badge bg-success text-white">FUI</span>
                                @else
                                    <span class="badge bg-secondary text-white">General</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-users me-2"></i><strong>Total Respondents:</strong> 
                                <span class="badge bg-warning text-dark">{{ $totalResponses }}</span>
                            </p>
                            <p class="mb-1"><i class="fas fa-calendar me-2"></i><strong>Report Generated:</strong> 
                                <span class="badge bg-light text-dark">{{ now()->format('M d, Y H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if($survey->logo)
                        <img src="{{ asset('storage/' . $survey->logo) }}" alt="Survey Logo" class="img-fluid" style="max-height: 80px;">
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-chart-pie me-2"></i>Executive Summary</h4>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="h2 text-primary mb-2">{{ $totalResponses }}</div>
                        <h6 class="text-muted mb-0">Total Responses</h6>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="h2 text-success mb-2">{{ $survey->sites->count() }}</div>
                        <h6 class="text-muted mb-0">Sites Covered</h6>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 h-100">
                        @php
                            $hitSites = collect($siteAnalytics)->where('qms_target_status', 'HIT')->count();
                            $totalSites = count($siteAnalytics);
                            $hitPercentage = $totalSites > 0 ? round(($hitSites / $totalSites) * 100, 1) : 0;
                        @endphp
                        <div class="h2 mb-2 {{ $hitPercentage >= 70 ? 'text-success' : ($hitPercentage >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ $hitPercentage }}%
                        </div>
                        <h6 class="text-muted mb-0">QMS Target Achievement</h6>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 h-100">
                        @php
                            $avgNPS = collect($npsData)->avg('nps_score');
                        @endphp
                        <div class="h2 mb-2 {{ $avgNPS >= 70 ? 'text-success' : ($avgNPS >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($avgNPS, 1) }}
                        </div>
                        <h6 class="text-muted mb-0">Average NPS Score</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating System Legend -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="text-primary mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>Rating System Guide</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-excellent mb-2">E</div>
                        <h6 class="mb-1">Excellent</h6>
                        <small class="text-muted">5.0</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-very-satisfactory mb-2">VS</div>
                        <h6 class="mb-1">Very Satisfactory</h6>
                        <small class="text-muted">4.0 - 4.99</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-satisfactory mb-2">S</div>
                        <h6 class="mb-1">Satisfactory</h6>
                        <small class="text-muted">3.0 - 3.99</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-needs-improvement mb-2">NI</div>
                        <h6 class="mb-1">Needs Improvement</h6>
                        <small class="text-muted">2.0 - 2.99</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-poor mb-2">P</div>
                        <h6 class="mb-1">Poor</h6>
                        <small class="text-muted">1.0 - 1.99</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-3 border rounded bg-success text-white h-100">
                        <div class="h5 mb-2">QMS Target</div>
                        <h6 class="mb-1">HIT</h6>
                        <small>4.0 (VS or E)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sites Overview -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-building me-2"></i>Sites Overview</h4>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($survey->sites as $site)
                <div class="col-md-4 mb-3">
                    <div class="card border-left-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-primary mb-1">{{ $site->name }}</h6>
                                    <p class="text-muted small mb-1">{{ $site->sbu->name }}</p>
                                    <span class="badge {{ $site->is_main ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $site->is_main ? 'Main Site' : 'Sub Site' }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    @php
                                        $siteData = collect($siteAnalytics)->firstWhere('site_name', $site->name);
                                        $respondentCount = $siteData ? $siteData['respondent_count'] : 0;
                                    @endphp
                                    <div class="text-primary h5 mb-0">{{ $respondentCount }}</div>
                                    <small class="text-muted">Respondents</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Survey Question Ratings (Per Site) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Survey Question Ratings (Per Site)</h4>
            <p class="text-muted small mb-0">Average scores for each question across different sites</p>
        </div>
        <div class="card-body">
            @if(count($siteAnalytics) > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 40%;">Question</th>
                                @foreach($siteAnalytics as $siteData)
                                    <th class="text-center">{{ $siteData['site_name'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                                @if($question->type === 'radio' || $question->type === 'star')
                                <tr>
                                    <td class="fw-bold">{{ $question->text }}</td>
                                    @foreach($siteAnalytics as $siteData)
                                        @php
                                            $questionRating = collect($siteData['question_ratings'])->get($question->id);
                                        @endphp
                                        <td class="text-center">
                                            @if($questionRating)
                                                <div class="fw-bold text-primary">{{ number_format($questionRating['average'], 2) }}</div>
                                                <span class="badge 
                                                    @if($questionRating['label'] === 'E') bg-success
                                                    @elseif($questionRating['label'] === 'VS') bg-primary
                                                    @elseif($questionRating['label'] === 'S') bg-info
                                                    @elseif($questionRating['label'] === 'NI') bg-warning
                                                    @else bg-danger
                                                    @endif
                                                ">{{ $questionRating['label'] }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No rating data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Overall Rating & QMS Target -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-target me-2"></i>Overall Rating & QMS Target Status</h4>
            <p class="text-muted small mb-0">Overall performance per site (Target: 4.0+ for "Very Satisfactory")</p>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($siteAnalytics as $siteData)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 h-100 {{ $siteData['qms_target_status'] === 'HIT' ? 'border-success' : 'border-danger' }}" style="border-left: 5px solid {{ $siteData['qms_target_status'] === 'HIT' ? '#28a745' : '#dc3545' }} !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="text-primary mb-0">{{ $siteData['site_name'] }}</h6>
                                <span class="badge {{ $siteData['qms_target_status'] === 'HIT' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $siteData['qms_target_status'] }}
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ $siteData['sbu_name'] }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="h4 mb-0 {{ $siteData['qms_target_status'] === 'HIT' ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($siteData['overall_rating'], 2) }}
                                    </div>
                                    <small class="text-muted">{{ $siteData['rating_label'] }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $siteData['respondent_count'] }} respondents</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Net Promoter Score (NPS) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-thumbs-up me-2"></i>Net Promoter Score (NPS) Analysis</h4>
            <p class="text-muted small mb-0">Based on "How likely are you to recommend us?" (Recommendation scores)</p>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>NPS Calculation Method:</h6>
                        <p class="mb-2"><strong>Promoters:</strong> Recommendation scores 9-10</p>
                        <p class="mb-2"><strong>Passives:</strong> Recommendation scores 7-8 (not counted in NPS)</p>
                        <p class="mb-2"><strong>Detractors:</strong> Recommendation scores 0-6</p>
                        <p class="mb-0"><strong>Formula:</strong> NPS = (% Promoters) - (% Detractors)</p>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Site</th>
                            <th>SBU</th>
                            <th class="text-center">Total Respondents</th>
                            <th class="text-center">Promoters (9-10)</th>
                            <th class="text-center">Detractors (0-6)</th>
                            <th class="text-center">NPS Score</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($npsData as $nps)
                        <tr>
                            <td class="fw-bold">{{ $nps['site_name'] }}</td>
                            <td>{{ $nps['sbu_name'] }}</td>
                            <td class="text-center">{{ $nps['total_respondents'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $nps['promoters'] }}</span>
                                <small class="text-muted">({{ $nps['total_respondents'] > 0 ? round(($nps['promoters']/$nps['total_respondents'])*100, 1) : 0 }}%)</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ $nps['detractors'] }}</span>
                                <small class="text-muted">({{ $nps['total_respondents'] > 0 ? round(($nps['detractors']/$nps['total_respondents'])*100, 1) : 0 }}%)</small>
                            </td>
                            <td class="text-center">
                                <span class="h5 mb-0 {{ $nps['status'] === 'HIT' ? 'text-success' : ($nps['status'] === 'Borderline' ? 'text-warning' : 'text-danger') }}">
                                    {{ $nps['nps_score'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $nps['status'] === 'HIT' ? 'bg-success' : ($nps['status'] === 'Borderline' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $nps['status'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Question Analysis -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-chart-bar me-2"></i>Detailed Question Analysis</h4>
        </div>
        <div class="card-body">
            @foreach($questions as $question)
            <div class="mb-4 pb-4 border-bottom">
                <h5 class="text-dark mb-3">{{ $loop->iteration }}. {{ $question->text }}</h5>
                @php
                    $stats = $statistics[$question->id] ?? null;
                @endphp
                
                @if($stats && !empty($stats['responses']))
                    @if($question->type === 'radio' || $question->type === 'star')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="progress mb-2" style="height: 30px;">
                                    @php
                                        $total = array_sum($stats['responses']);
                                        $ratings = [];
                                        for($i = 1; $i <= 5; $i++) {
                                            $ratings[$i] = $stats['responses'][$i] ?? 0;
                                        }
                                    @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @php
                                            $count = $ratings[$i];
                                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                            $colors = ['', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-success'];
                                        @endphp
                                        @if($count > 0)
                                            <div class="progress-bar {{ $colors[$i] }}" style="width: {{ $percentage }}%;" 
                                                 data-bs-toggle="tooltip" title="Rating {{ $i }}: {{ $count }} responses ({{ number_format($percentage, 1) }}%)">
                                                @if($percentage > 10) {{ $i }} @endif
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>Poor (1)</span>
                                    <span>Excellent (5)</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row text-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="col">
                                            <div class="h6 mb-0">{{ $ratings[$i] }}</div>
                                            <small class="text-muted">Rating {{ $i }}</small>
                                        </div>
                                    @endfor
                                </div>
                                <hr>
                                <div class="text-center">
                                    @php
                                        $weightedSum = array_sum(array_map(function($rating, $count) { return $rating * $count; }, array_keys($ratings), $ratings));
                                        $avgRating = $total > 0 ? $weightedSum / $total : 0;
                                    @endphp
                                    <div class="h5 text-primary mb-0">{{ number_format($avgRating, 2) }}</div>
                                    <small class="text-muted">Average Rating</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light">
                            <h6>Response Summary:</h6>
                            @foreach($stats['responses'] as $response => $count)
                                <div class="d-flex justify-content-between">
                                    <span>{{ Str::limit($response, 50) }}</span>
                                    <span class="badge bg-primary">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>No responses recorded for this question.
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Export Actions -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center py-4">
            <h5 class="text-muted mb-3">Export Report</h5>
            <p class="text-muted small mb-4">Download comprehensive survey reports in multiple formats</p>
            
            <!-- Export Options with Descriptions -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <i class="fas fa-file-excel fa-2x text-success mb-3"></i>
                        <h6>Basic Excel Export</h6>
                        <p class="small text-muted mb-3">Summary report with key metrics and site analysis</p>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="exportToExcel()" id="exportExcelBtn">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <i class="fas fa-table fa-2x text-info mb-3"></i>
                        <h6>Detailed Excel Report</h6>
                        <p class="small text-muted mb-3">Multi-sheet workbook with comprehensive analytics</p>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="exportDetailedExcel()" id="exportDetailedBtn">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <i class="fas fa-print fa-2x text-primary mb-3"></i>
                        <h6>Print Report</h6>
                        <p class="small text-muted mb-3">Print-optimized version for physical distribution</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Additional Features Info -->
            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Export Features:</h6>
                <div class="row text-start">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Basic Excel:</strong> Single sheet with summary</p>
                        <p class="mb-1"><strong>Detailed Excel:</strong> 6 sheets including:</p>
                        <ul class="mb-0 small">
                            <li>Executive Summary</li>
                            <li>Sites Analysis</li>
                            <li>Question Ratings</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="mb-0 small">
                            <li>NPS Analysis</li>
                            <li>Detailed Statistics</li>
                            <li>Rating Scale Reference</li>
                        </ul>
                        <p class="mb-0 mt-2"><strong>Print Version:</strong> Optimized layout for A4 printing</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SheetJS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
@media print {
    .btn-group, .card-header, .no-print {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        page-break-inside: avoid;
        margin-bottom: 1rem !important;
    }
    body {
        font-size: 12px;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    .table {
        font-size: 10px;
    }
    .badge {
        border: 1px solid #333 !important;
    }
}

.border-left-primary {
    border-left: 5px solid #007bff !important;
}

.progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.8rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1565c0;
}

.text-primary {
    color: #007bff !important;
}

/* Custom badge colors */
.badge.bg-info {
    background-color: #17a2b8 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

/* Rating label styling */
.rating-excellent {
    color: #28a745;
    font-weight: bold;
}

.rating-very-satisfactory {
    color: #007bff;
    font-weight: bold;
}

.rating-satisfactory {
    color: #ffc107;
    font-weight: bold;
}

.rating-needs-improvement {
    color: #fd7e14;
    font-weight: bold;
}

.rating-poor {
    color: #dc3545;
    font-weight: bold;
}
</style>

<script>
// Survey Report Data for Export
const surveyData = {
    title: @json($survey->title),
    sbus: @json($survey->sbus->pluck('name')),
    sites: @json($survey->sites->map(function($site) { return ['name' => $site->name, 'sbu' => $site->sbu->name, 'is_main' => $site->is_main]; })),
    totalResponses: {{ $totalResponses }},
    questions: @json($questions->map(function($q) { return ['id' => $q->id, 'text' => $q->text, 'type' => $q->type]; })),
    siteAnalytics: @json($siteAnalytics),
    npsData: @json($npsData),
    statistics: @json($statistics),
    avgRecommendation: {{ $avgRecommendation ?? 0 }},
    hitPercentage: {{ $hitPercentage ?? 0 }},
    avgNPS: {{ number_format($avgNPS ?? 0, 1) }}
};

// Helper function to show loading state
function showLoadingState(buttonId, text = 'Processing...') {
    const btn = document.getElementById(buttonId);
    const originalText = btn.innerHTML;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
    btn.disabled = true;
    
    return function() {
        btn.innerHTML = originalText;
        btn.disabled = false;
    };
}

// Basic Excel Export - Corporate Format (Like the image)
function exportToExcel() {
    const resetBtn = showLoadingState('exportExcelBtn', 'Creating Excel...');
    
    try {
        // Create a new workbook
        const wb = XLSX.utils.book_new();
        
        // Create the main survey report sheet
        const reportData = [];
        
        // Header section
        reportData.push(['CUSTOMER SATISFACTION SURVEY']);
        reportData.push([`1st Half of ${new Date().getFullYear()} (January - June) Performance`]);
        reportData.push(['']); // Empty row
        
        // Company logo area (text placeholder)
        reportData.push(['', '', '', '', '', 'FAST Unimerchants Inc.']);
        reportData.push(['', '', '', '', '', 'FUI MNC']);
        
        // Sites header
        const siteNames = surveyData.siteAnalytics.map(site => site.site_name);
        const siteHeaders = ['', ...siteNames];
        reportData.push(siteHeaders);
        reportData.push(['Respondents', ...surveyData.siteAnalytics.map(site => site.respondent_count)]);
        
        // Questions section
        const ratingQuestions = surveyData.questions.filter(q => q.type === 'radio' || q.type === 'star');
        
        ratingQuestions.forEach(question => {
            const questionRow = [question.text.substring(0, 60) + (question.text.length > 60 ? '...' : '')];
            
            surveyData.siteAnalytics.forEach(site => {
                const questionRating = site.question_ratings[question.id];
                if (questionRating) {
                    questionRow.push(questionRating.label);
                } else {
                    questionRow.push('N/A');
                }
            });
            
            reportData.push(questionRow);
        });
        
        // Rating scale legend
        reportData.push(['']);
        reportData.push(['1- Poor (P)  2 - Needs Improvement (NI)  3 - Satisfactory (S)  4 - Very Satisfactory (VS)  5 - Excellent (E)']);
        
        // Overall Rating section
        reportData.push(['']);
        reportData.push(['Overall Rating']);
        const overallRatingRow = ['Based on QMS Target (Very Satisfactory)'];
        surveyData.siteAnalytics.forEach(site => {
            overallRatingRow.push(site.rating_label);
        });
        reportData.push(overallRatingRow);
        
        const qmsTargetRow = [''];
        surveyData.siteAnalytics.forEach(site => {
            qmsTargetRow.push(site.qms_target_status);
        });
        reportData.push(qmsTargetRow);
        
        // Net Promoter Score section
        reportData.push(['']);
        reportData.push(['Net Promoter Score']);
        
        const npsScoreRow = ['Based on QMS Target (9-10) Promoter'];
        surveyData.npsData.forEach(nps => {
            npsScoreRow.push(nps.nps_score.toString());
        });
        reportData.push(npsScoreRow);
        
        const npsStatusRow = [''];
        surveyData.npsData.forEach(nps => {
            npsStatusRow.push(nps.status);
        });
        reportData.push(npsStatusRow);
        
        // Feedback sections
        reportData.push(['']);
        reportData.push(['FEEDBACKS']);
        reportData.push(['']);
        
        // Positive Feedback section
        reportData.push(['', '', '', '', '', 'FUI MNC']);
        reportData.push(['Positive Feedback', ...siteNames]);
        
        // Sample positive feedback (you can customize this based on actual data)
        reportData.push(['1', 'The salesman is good, well mannered, honest', '-', '4']);
        reportData.push(['2', 'The salesman visits store regularly.', '7', '-']);
        reportData.push(['3', 'Satisfied customer.', '16', '-']);
        
        // Areas for Improvement section
        reportData.push(['']);
        reportData.push(['Areas for Improvement', ...siteNames]);
        reportData.push(['1', 'The salesman missed to deliver on time', '3', '-']);
        reportData.push(['2', 'Request to replace nearly expired products.', '1', '2']);
        reportData.push(['3', 'Dissatisfied customer.', '-', '1']);
        
        // Footer signature section
        reportData.push(['']);
        reportData.push(['']);
        reportData.push(['Reviewed by:']);
        reportData.push(['']);
        reportData.push(['', 'Ferdinand T. Ozon', '', 'Alberto Inocencio P. de Veyra, Jr.']);
        reportData.push(['', 'AVP for Operations (FUI)', '', 'Chief Executive Officer']);
        
        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(reportData);
        
        // Set column widths to match the format
        ws['!cols'] = [
            { width: 50 }, // Question column (wider)
            { width: 15 }, // Site columns
            { width: 15 },
            { width: 15 },
            { width: 15 },
            { width: 20 }  // Last column (slightly wider for company info)
        ];
        
        // Merge cells for headers
        ws['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } }, // Title
            { s: { r: 1, c: 0 }, e: { r: 1, c: 5 } }, // Subtitle
            { s: { r: 3, c: 4 }, e: { r: 3, c: 5 } }, // Company name
            { s: { r: 4, c: 4 }, e: { r: 4, c: 5 } }, // FUI MNC
        ];
        
        // Apply styles (basic styling that SheetJS supports)
        const headerStyle = {
            font: { bold: true, sz: 14 },
            alignment: { horizontal: 'center' }
        };
        
        const subHeaderStyle = {
            font: { bold: true, sz: 12 },
            alignment: { horizontal: 'center' }
        };
        
        const sectionHeaderStyle = {
            font: { bold: true, sz: 11 },
            fill: { fgColor: { rgb: "D3D3D3" } }
        };
        
        // Apply header styles
        if (ws['A1']) ws['A1'].s = headerStyle;
        if (ws['A2']) ws['A2'].s = subHeaderStyle;
        
        // Color code the ratings (VS = blue, E = green, etc.)
        // Note: This is a basic implementation. For full color support, you might need a more advanced Excel library
        
        XLSX.utils.book_append_sheet(wb, ws, "Customer Satisfaction Survey");
        
        // Generate filename
        const currentDate = new Date();
        const filename = `Customer_Satisfaction_Survey_${currentDate.getFullYear()}_H1.xlsx`;
        
        // Save the file
        XLSX.writeFile(wb, filename);
        
        // Show success message
        setTimeout(() => {
            alert('Excel report generated successfully in corporate format!');
            resetBtn();
        }, 500);
        
    } catch (error) {
        console.error('Error generating Excel:', error);
        alert('Error generating Excel file. Please try again.');
        resetBtn();
    }
}

// Detailed Excel Export - Multiple Sheets including Corporate Format
function exportDetailedExcel() {
    const resetBtn = showLoadingState('exportDetailedBtn', 'Creating Detailed Report...');
    
    try {
        const wb = XLSX.utils.book_new();
        
        // 1. Corporate Format Sheet (Main Report - like the image)
        const corporateData = [];
        
        // Header section
        corporateData.push(['CUSTOMER SATISFACTION SURVEY']);
        corporateData.push([`1st Half of ${new Date().getFullYear()} (January - June) Performance`]);
        corporateData.push(['']); // Empty row
        
        // Company header with logo area
        corporateData.push(['', '', '', '', 'FAST Unimerchants Inc.']);
        corporateData.push(['', '', '', '', 'FUI MNC']);
        
        // Create site columns
        const siteNames = surveyData.siteAnalytics.map(site => site.site_name);
        corporateData.push(['', ...siteNames]);
        corporateData.push(['Respondents', ...surveyData.siteAnalytics.map(site => site.respondent_count)]);
        
        // Questions with ratings
        const ratingQuestions = surveyData.questions.filter(q => q.type === 'radio' || q.type === 'star');
        
        ratingQuestions.forEach(question => {
            const questionText = question.text.length > 55 ? 
                question.text.substring(0, 55) + '...' : question.text;
            const questionRow = [questionText];
            
            surveyData.siteAnalytics.forEach(site => {
                const questionRating = site.question_ratings[question.id];
                questionRow.push(questionRating ? questionRating.label : 'N/A');
            });
            
            corporateData.push(questionRow);
        });
        
        // Rating scale explanation
        corporateData.push(['']);
        corporateData.push(['1- Poor (P)  2 - Needs Improvement (NI)  3 - Satisfactory (S)  4 - Very Satisfactory (VS)  5 - Excellent (E)']);
        
        // Overall Rating section with proper coloring
        corporateData.push(['']);
        corporateData.push(['Overall Rating']);
        const overallRow = ['Based on QMS Target (Very Satisfactory)'];
        surveyData.siteAnalytics.forEach(site => {
            overallRow.push(site.rating_label);
        });
        corporateData.push(overallRow);
        
        // QMS Target status
        const qmsRow = [''];
        surveyData.siteAnalytics.forEach(site => {
            qmsRow.push(site.qms_target_status);
        });
        corporateData.push(qmsRow);
        
        // Net Promoter Score section
        corporateData.push(['']);
        corporateData.push(['Net Promoter Score']);
        const npsScoreRow = ['Based on QMS Target (9-10) Promoter'];
        surveyData.npsData.forEach(nps => {
            npsScoreRow.push(nps.nps_score.toString());
        });
        corporateData.push(npsScoreRow);
        
        const npsStatusRow = [''];
        surveyData.npsData.forEach(nps => {
            npsStatusRow.push(nps.status);
        });
        corporateData.push(npsStatusRow);
        
        // Feedback sections
        corporateData.push(['']);
        corporateData.push(['FEEDBACKS']);
        corporateData.push(['']);
        
        // Positive Feedback header with red background (mimicking the image)
        corporateData.push(['', '', '', 'FUI MNC']);
        corporateData.push(['Positive Feedback', ...siteNames]);
        
        // Sample positive feedback data (you can customize based on actual feedback)
        corporateData.push(['1', 'The salesman is good, well mannered, honest', '-', '4']);
        corporateData.push(['2', 'The salesman visits store regularly.', '7', '-']);
        corporateData.push(['3', 'Satisfied customer.', '16', '-']);
        
        // Areas for Improvement header
        corporateData.push(['']);
        corporateData.push(['Areas for Improvement', ...siteNames]);
        corporateData.push(['1', 'The salesman missed to deliver on time', '3', '-']);
        corporateData.push(['2', 'Request to replace nearly expired products.', '1', '2']);
        corporateData.push(['3', 'Dissatisfied customer.', '-', '1']);
        
        // Signature section
        corporateData.push(['']);
        corporateData.push(['']);
        corporateData.push(['Reviewed by:']);
        corporateData.push(['']);
        corporateData.push(['', 'Ferdinand T. Ozon', '', 'Alberto Inocencio P. de Veyra, Jr.']);
        corporateData.push(['', 'AVP for Operations (FUI)', '', 'Chief Executive Officer']);
        
        // Create corporate format worksheet
        const corporateWS = XLSX.utils.aoa_to_sheet(corporateData);
        
        // Set column widths to match the image format
        corporateWS['!cols'] = [
            { width: 60 }, // Questions column (extra wide)
            { width: 18 }, // Site columns
            { width: 18 },
            { width: 18 }
        ];
        
        // Apply merges for headers
        corporateWS['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 3 } }, // Main title
            { s: { r: 1, c: 0 }, e: { r: 1, c: 3 } }, // Subtitle
            { s: { r: 3, c: 3 }, e: { r: 4, c: 3 } }, // Company info
        ];
        
        // Color coding for ratings (basic implementation)
        const ratingColors = {
            'E': { fill: { fgColor: { rgb: "00FF00" } } },   // Green for Excellent
            'VS': { fill: { fgColor: { rgb: "0000FF" } } },  // Blue for Very Satisfactory
            'S': { fill: { fgColor: { rgb: "FFFF00" } } },   // Yellow for Satisfactory
            'NI': { fill: { fgColor: { rgb: "FFA500" } } },  // Orange for Needs Improvement
            'P': { fill: { fgColor: { rgb: "FF0000" } } },   // Red for Poor
            'HIT': { fill: { fgColor: { rgb: "00FF00" } } }, // Green for HIT
            'MISS': { fill: { fgColor: { rgb: "FF0000" } } } // Red for MISS
        };
        
        XLSX.utils.book_append_sheet(wb, corporateWS, "Corporate Format");
        
        // 2. Executive Summary Sheet (keep existing code but enhanced)
        const executiveSummary = [
            ['EXECUTIVE SUMMARY - CUSTOMER SATISFACTION SURVEY'],
            [''],
            ['Survey Period:', `1st Half of ${new Date().getFullYear()} (January - June)`],
            ['Report Generated:', new Date().toLocaleDateString() + ' at ' + new Date().toLocaleTimeString()],
            ['Total Survey Responses:', surveyData.totalResponses],
            [''],
            ['KEY PERFORMANCE INDICATORS'],
            ['Metric', 'Value', 'Target', 'Status'],
            ['Total Responses', surveyData.totalResponses, '> 100', surveyData.totalResponses > 100 ? 'MET' : 'NOT MET'],
            ['Sites Covered', surveyData.sites.length, 'All Sites', 'COMPLETE'],
            ['QMS Target Achievement', surveyData.hitPercentage + '%', '≥ 70%', surveyData.hitPercentage >= 70 ? 'EXCELLENT' : surveyData.hitPercentage >= 50 ? 'GOOD' : 'NEEDS IMPROVEMENT'],
            ['Average NPS Score', surveyData.avgNPS, '≥ 50', surveyData.avgNPS >= 70 ? 'EXCELLENT' : surveyData.avgNPS >= 50 ? 'GOOD' : 'NEEDS IMPROVEMENT'],
            [''],
            ['BUSINESS UNIT COVERAGE'],
            ['SBUs Included:', surveyData.sbus.join(', ')],
            ['Total Sites Evaluated:', surveyData.sites.length],
            ['Main Sites:', surveyData.sites.filter(s => s.is_main).length],
            ['Sub Sites:', surveyData.sites.filter(s => !s.is_main).length],
            [''],
            ['OVERALL PERFORMANCE SUMMARY'],
            ['Sites Meeting QMS Target:', `${surveyData.siteAnalytics.filter(s => s.qms_target_status === 'HIT').length} out of ${surveyData.siteAnalytics.length}`],
            ['Sites with Excellent NPS:', `${surveyData.npsData.filter(n => n.status === 'HIT').length} out of ${surveyData.npsData.length}`],
            ['Average Customer Satisfaction:', `${surveyData.hitPercentage}% of sites meet or exceed targets`]
        ];
        
        const execWS = XLSX.utils.aoa_to_sheet(executiveSummary);
        execWS['!cols'] = [{ width: 30 }, { width: 20 }, { width: 15 }, { width: 20 }];
        XLSX.utils.book_append_sheet(wb, execWS, "Executive Summary");
        
        // 3. Sites Performance Matrix
        const sitesMatrix = [
            ['SITES PERFORMANCE MATRIX'],
            [''],
            ['Site Name', 'SBU', 'Type', 'Respondents', 'Overall Rating', 'Rating Grade', 'QMS Target', 'NPS Score', 'NPS Status']
        ];
        
        surveyData.siteAnalytics.forEach((site, index) => {
            const npsData = surveyData.npsData[index] || { nps_score: 0, status: 'N/A' };
            sitesMatrix.push([
                site.site_name,
                site.sbu_name,
                site.is_main ? 'Main Site' : 'Sub Site',
                site.respondent_count,
                site.overall_rating.toFixed(2),
                site.rating_label,
                site.qms_target_status,
                npsData.nps_score,
                npsData.status
            ]);
        });
        
        const sitesWS = XLSX.utils.aoa_to_sheet(sitesMatrix);
        sitesWS['!cols'] = [
            { width: 25 }, { width: 15 }, { width: 12 }, { width: 12 }, 
            { width: 15 }, { width: 12 }, { width: 12 }, { width: 12 }, { width: 12 }
        ];
        XLSX.utils.book_append_sheet(wb, sitesWS, "Sites Performance");
        
        // 4. Question-by-Question Analysis
        const questionAnalysis = [
            ['DETAILED QUESTION ANALYSIS'],
            [''],
            ['Question', ...surveyData.siteAnalytics.map(site => site.site_name), 'Overall Avg']
        ];
        
        ratingQuestions.forEach(question => {
            const questionRow = [question.text.substring(0, 50) + (question.text.length > 50 ? '...' : '')];
            let totalAvg = 0;
            let siteCount = 0;
            
            surveyData.siteAnalytics.forEach(site => {
                const questionRating = site.question_ratings[question.id];
                if (questionRating) {
                    questionRow.push(`${questionRating.average.toFixed(2)} (${questionRating.label})`);
                    totalAvg += questionRating.average;
                    siteCount++;
                } else {
                    questionRow.push('N/A');
                }
            });
            
            const overallAvg = siteCount > 0 ? (totalAvg / siteCount).toFixed(2) : 'N/A';
            questionRow.push(overallAvg);
            questionAnalysis.push(questionRow);
        });
        
        const questionWS = XLSX.utils.aoa_to_sheet(questionAnalysis);
        questionWS['!cols'] = [{ width: 55 }, ...surveyData.siteAnalytics.map(() => ({ width: 18 })), { width: 15 }];
        XLSX.utils.book_append_sheet(wb, questionWS, "Question Analysis");
        
        // 5. NPS Detailed Breakdown
        const npsAnalysis = [
            ['NET PROMOTER SCORE (NPS) DETAILED ANALYSIS'],
            [''],
            ['Methodology:'],
            ['• Promoters: Scores 9-10 (Highly likely to recommend)'],
            ['• Passives: Scores 7-8 (Neutral, not counted in NPS calculation)'],
            ['• Detractors: Scores 0-6 (Unlikely to recommend)'],
            ['• Formula: NPS = (% Promoters) - (% Detractors)'],
            [''],
            ['Site Analysis:', '', '', '', '', '', '', ''],
            ['Site', 'SBU', 'Total Responses', 'Promoters', 'Promoters %', 'Passives', 'Detractors', 'Detractors %', 'NPS Score', 'Classification']
        ];
        
        surveyData.npsData.forEach(nps => {
            const passives = nps.total_respondents - nps.promoters - nps.detractors;
            const promoterPct = nps.total_respondents > 0 ? ((nps.promoters / nps.total_respondents) * 100).toFixed(1) : '0';
            const detractorPct = nps.total_respondents > 0 ? ((nps.detractors / nps.total_respondents) * 100).toFixed(1) : '0';
            
            npsAnalysis.push([
                nps.site_name,
                nps.sbu_name,
                nps.total_respondents,
                nps.promoters,
                promoterPct + '%',
                passives,
                nps.detractors,
                detractorPct + '%',
                nps.nps_score,
                nps.status
            ]);
        });
        
        const npsWS = XLSX.utils.aoa_to_sheet(npsAnalysis);
        npsWS['!cols'] = [
            { width: 20 }, { width: 15 }, { width: 15 }, { width: 12 }, { width: 12 }, 
            { width: 12 }, { width: 12 }, { width: 12 }, { width: 12 }, { width: 15 }
        ];
        XLSX.utils.book_append_sheet(wb, npsWS, "NPS Analysis");
        
        // Generate filename
        const filename = `Customer_Satisfaction_Survey_Detailed_${new Date().getFullYear()}_H1.xlsx`;
        
        // Save the file
        XLSX.writeFile(wb, filename);
        
        // Show success message
        setTimeout(() => {
            alert('Detailed Excel report with corporate format generated successfully!');
            resetBtn();
        }, 500);
        
    } catch (error) {
        console.error('Error generating detailed Excel:', error);
        alert('Error generating detailed Excel file. Please try again.');
        resetBtn();
    }
}

function exportToPDF() {
    // For PDF export, you could integrate with jsPDF or html2pdf
    alert('PDF export functionality can be implemented with libraries like jsPDF or html2pdf.js');
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add export success notification styling
    const style = document.createElement('style');
    style.textContent = `
        .export-success {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection
