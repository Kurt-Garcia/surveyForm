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
                | <strong>Sites with Responses:</strong> {{ count($siteAnalytics) }}
            </p>
            @if(isset($recommendationStats['overall']) && $recommendationStats['overall']['total_responses'] > 0)
            <p class="mb-1">
                <strong>Average Recommendation:</strong> {{ $recommendationStats['overall']['average_score'] }}/10
                @if(isset($improvementAreasStats['top_categories']) && count($improvementAreasStats['top_categories']) > 0)
                | <strong>Top Improvement Area:</strong> {{ str_replace('_', ' ', array_keys($improvementAreasStats['top_categories'])[0]) }}
                @endif
            </p>
            @endif
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
                        <div class="h2 text-success mb-2">{{ count($siteAnalytics) }}</div>
                        <h6 class="text-muted mb-0">Sites with Responses</h6>
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
            <div class="row text-center justify-content-center">
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-excellent mb-2">E</div>
                        <h6 class="mb-1">Excellent</h6>
                        <small class="text-muted">5.0</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-very-satisfactory mb-2">VS</div>
                        <h6 class="mb-1">Very Satisfactory</h6>
                        <small class="text-muted">4.0 - 4.99</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-satisfactory mb-2">S</div>
                        <h6 class="mb-1">Satisfactory</h6>
                        <small class="text-muted">3.0 - 3.99</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-needs-improvement mb-2">NI</div>
                        <h6 class="mb-1">Needs Improvement</h6>
                        <small class="text-muted">2.0 - 2.99</small>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="h5 rating-poor mb-2">P</div>
                        <h6 class="mb-1">Poor</h6>
                        <small class="text-muted">1.0 - 1.99</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sites Overview -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-building me-2"></i>Sites Overview</h4>
            <p class="text-muted small mb-0">Sites with survey responses</p>
        </div>
        <div class="card-body">
            @if(count($siteAnalytics) > 0)
                <div class="row">
                    @foreach($siteAnalytics as $siteData)
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-primary mb-1">{{ $siteData['site_name'] }}</h6>
                                        <p class="text-muted small mb-1">{{ $siteData['sbu_name'] }}</p>
                                        <span class="badge {{ $siteData['is_main'] ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $siteData['is_main'] ? 'Main Site' : 'Sub Site' }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-primary h5 mb-0">{{ $siteData['respondent_count'] }}</div>
                                        <small class="text-muted">Respondents</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No sites have survey responses yet</p>
                </div>
            @endif
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

    <!-- Recommendation Analysis -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-star me-2"></i>Recommendation Analysis</h4>
            <p class="text-muted small mb-0">Detailed analysis of recommendation scores (1-10 scale)</p>
        </div>
        <div class="card-body">
            @if(isset($recommendationStats['overall']) && $recommendationStats['overall']['total_responses'] > 0)
                <!-- Overall Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center border rounded p-3">
                            <div class="h2 text-primary mb-2">{{ $recommendationStats['overall']['average_score'] }}</div>
                            <h6 class="text-muted mb-0">Average Score</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center border rounded p-3">
                            <div class="h2 text-info mb-2">{{ $recommendationStats['overall']['total_responses'] }}</div>
                            <h6 class="text-muted mb-0">Total Responses</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center border rounded p-3">
                            @php
                                $promoters = collect($recommendationStats['overall']['distribution'])->filter(function($item, $score) {
                                    return $score >= 9;
                                })->sum('count');
                                $promoterPercentage = $recommendationStats['overall']['total_responses'] > 0 ? 
                                    round(($promoters / $recommendationStats['overall']['total_responses']) * 100, 1) : 0;
                            @endphp
                            <div class="h2 text-success mb-2">{{ $promoterPercentage }}%</div>
                            <h6 class="text-muted mb-0">Promoters (9-10)</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center border rounded p-3">
                            @php
                                $detractors = collect($recommendationStats['overall']['distribution'])->filter(function($item, $score) {
                                    return $score <= 6;
                                })->sum('count');
                                $detractorPercentage = $recommendationStats['overall']['total_responses'] > 0 ? 
                                    round(($detractors / $recommendationStats['overall']['total_responses']) * 100, 1) : 0;
                            @endphp
                            <div class="h2 text-danger mb-2">{{ $detractorPercentage }}%</div>
                            <h6 class="text-muted mb-0">Detractors (1-6)</h6>
                        </div>
                    </div>
                </div>

                <!-- Score Distribution Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">Score Distribution (1-10 Scale)</h6>
                        <div class="score-distribution">
                            @for($i = 1; $i <= 10; $i++)
                                @php
                                    $data = $recommendationStats['overall']['distribution'][$i] ?? ['count' => 0, 'percentage' => 0];
                                    $colorClass = '';
                                    if($i <= 6) $colorClass = 'bg-danger';
                                    elseif($i <= 8) $colorClass = 'bg-warning';
                                    else $colorClass = 'bg-success';
                                @endphp
                                <div class="score-item mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Score {{ $i }}:</span>
                                        <span>{{ $data['count'] }} responses ({{ $data['percentage'] }}%)</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $colorClass }}" style="width: {{ $data['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Site-wise Recommendation Analysis -->
                @if(count($recommendationStats['overall']['by_site']) > 0)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Recommendation Analysis by Site</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Site</th>
                                            <th>SBU</th>
                                            <th class="text-center">Total Responses</th>
                                            <th class="text-center">Average Score</th>
                                            <th class="text-center">Promoters (9-10)</th>
                                            <th class="text-center">Passives (7-8)</th>
                                            <th class="text-center">Detractors (1-6)</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recommendationStats['overall']['by_site'] as $siteData)
                                            <tr>
                                                <td class="fw-bold">{{ $siteData['site_name'] }}</td>
                                                <td>{{ $siteData['sbu_name'] }}</td>
                                                <td class="text-center">{{ $siteData['total_responses'] }}</td>
                                                <td class="text-center">
                                                    <span class="h6 mb-0 {{ $siteData['average_score'] >= 8 ? 'text-success' : ($siteData['average_score'] >= 6 ? 'text-warning' : 'text-danger') }}">
                                                        {{ $siteData['average_score'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $sitePromoters = collect($siteData['distribution'])->filter(function($item, $score) {
                                                            return $score >= 9;
                                                        })->sum('count');
                                                        $sitePromoterPercentage = $siteData['total_responses'] > 0 ? 
                                                            round(($sitePromoters / $siteData['total_responses']) * 100, 1) : 0;
                                                    @endphp
                                                    <span class="badge bg-success">{{ $sitePromoters }}</span>
                                                    <small class="text-muted">({{ $sitePromoterPercentage }}%)</small>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $sitePassives = collect($siteData['distribution'])->filter(function($item, $score) {
                                                            return $score >= 7 && $score <= 8;
                                                        })->sum('count');
                                                        $sitePassivePercentage = $siteData['total_responses'] > 0 ? 
                                                            round(($sitePassives / $siteData['total_responses']) * 100, 1) : 0;
                                                    @endphp
                                                    <span class="badge bg-info">{{ $sitePassives }}</span>
                                                    <small class="text-muted">({{ $sitePassivePercentage }}%)</small>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $siteDetractors = collect($siteData['distribution'])->filter(function($item, $score) {
                                                            return $score <= 6;
                                                        })->sum('count');
                                                        $siteDetractorPercentage = $siteData['total_responses'] > 0 ? 
                                                            round(($siteDetractors / $siteData['total_responses']) * 100, 1) : 0;
                                                    @endphp
                                                    <span class="badge bg-danger">{{ $siteDetractors }}</span>
                                                    <small class="text-muted">({{ $siteDetractorPercentage }}%)</small>
                                                </td>
                                                <td class="text-center fw-bold {{ $siteData['average_score'] >= 8 ? 'text-success' : 'text-danger' }}">
                                                    {{ $siteData['average_score'] >= 8 ? 'HIT' : 'MISS' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No recommendation data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Areas for Improvement Analysis -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="text-primary mb-0 fw-bold"><i class="fas fa-tools me-2"></i>Areas for Improvement Analysis</h4>
            <p class="text-muted small mb-0">Customer feedback on areas that need attention</p>
        </div>
        <div class="card-body">
            @if(isset($improvementAreasStats['categories']) && count($improvementAreasStats['categories']) > 0)
                <!-- Top Issues Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">Top Improvement Categories</h6>
                        <div class="row">
                            @foreach($improvementAreasStats['top_categories'] as $categoryName => $data)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-warning h-100">
                                        <div class="card-body">
                                            <h6 class="text-warning text-capitalize">{{ str_replace('_', ' ', $categoryName) }}</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="h5 text-warning mb-0">{{ $data['count'] }}</div>
                                                    <small class="text-muted">mentions</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="h6 text-warning mb-0">{{ $data['percentage'] }}%</div>
                                                    <small class="text-muted">of responses</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Detailed Category Analysis -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">All Categories with Details</h6>
                        <div class="accordion" id="improvementAccordion">
                            @foreach($improvementAreasStats['categories'] as $categoryName => $categoryData)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" 
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $loop->index }}">
                                            <strong class="text-capitalize">{{ str_replace('_', ' ', $categoryName) }}</strong>
                                            <span class="badge bg-warning ms-2">{{ $categoryData['count'] }} mentions ({{ $categoryData['percentage'] }}%)</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                         aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#improvementAccordion">
                                        <div class="accordion-body">
                                            @if(isset($improvementAreasStats['details_by_category'][$categoryName]) && count($improvementAreasStats['details_by_category'][$categoryName]) > 0)
                                                <h6 class="mb-3">Specific Issues Mentioned:</h6>
                                                <div class="list-group">
                                                    @foreach($improvementAreasStats['details_by_category'][$categoryName] as $detail)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span>{{ $detail['detail_text'] }}</span>
                                                            <div>
                                                                <span class="badge bg-primary rounded-pill">{{ $detail['count'] }}</span>
                                                                <small class="text-muted ms-2">({{ $detail['percentage'] }}%)</small>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">No specific details available for this category.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Site-wise Improvement Analysis -->
                @if(count($improvementAreasStats['by_site']) > 0)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">Improvement Areas by Site</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Site</th>
                                            <th>SBU</th>
                                            <th class="text-center">Total Mentions</th>
                                            <th>Top Categories</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($improvementAreasStats['by_site'] as $siteData)
                                            <tr>
                                                <td class="fw-bold">{{ $siteData['site_name'] }}</td>
                                                <td>{{ $siteData['sbu_name'] }}</td>
                                                <td class="text-center">{{ $siteData['total_responses'] }}</td>
                                                <td>
                                                    @php
                                                        $topSiteCategories = collect($siteData['categories'])->sortByDesc('count')->take(3);
                                                    @endphp
                                                    @foreach($topSiteCategories as $categoryName => $categoryData)
                                                        <span class="badge bg-warning me-1">
                                                            {{ str_replace('_', ' ', $categoryName) }} ({{ $categoryData['count'] }})
                                                        </span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No improvement areas data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Export Actions -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body text-center py-4">
            <h5 class="text-muted mb-3">Export Report</h5>
            <p class="text-muted small mb-4">Download comprehensive survey reports in multiple formats</p>
            
            <!-- Export Options with Descriptions -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <i class="fas fa-file-excel fa-2x text-success mb-3"></i>
                        <h6>Corporate Excel Export</h6>
                        <p class="small text-muted mb-3">Professional report matching corporate format with styling</p>
                        <a href="{{ route('admin.surveys.export.excel', $survey) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
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
        </div>
    </div>
</div>

<!-- Remove SheetJS Library - No longer needed -->

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

/* Score distribution styling */
.score-distribution .score-item {
    border-left: 3px solid #e9ecef;
    padding-left: 10px;
}

.score-distribution .score-item:hover {
    border-left-color: #007bff;
    background-color: #f8f9fa;
}

/* Improvement areas styling */
.accordion-button {
    background-color: #f8f9fa;
}

.accordion-button:not(.collapsed) {
    background-color: #e3f2fd;
    color: #1565c0;
}

.list-group-item {
    border-left: 3px solid #ffc107;
    margin-bottom: 2px;
}

.list-group-item:hover {
    background-color: #fff3cd;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
