@extends('layouts.app')

@section('content')
<div class="modern-report-container">
    <!-- Header Section -->
    <div class="report-header">
        <div class="header-content">
            <div class="header-main">
                <div class="survey-info">
                    <h1 class="survey-title">{{ $survey->title }}</h1>
                    <div class="survey-meta">
                        <div class="meta-item">
                            <i class="fas fa-building"></i>
                            <span class="meta-label">SBU:</span>
                            <div class="meta-badges">
                                @if($survey->sbus->count() > 0)
                                    @foreach($survey->sbus as $sbu)
                                        <span class="modern-badge primary">{{ $sbu->name }}</span>
                                    @endforeach
                                @else
                                    <span class="modern-badge warning">Not Assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="meta-label">Sites:</span>
                            <div class="meta-badges">
                                @if($survey->sites->count() > 0)
                                    @php $maxVisibleSites = 3; @endphp
                                    @foreach($survey->sites->take($maxVisibleSites) as $site)
                                        <span class="modern-badge secondary">{{ $site->name }}</span>
                                    @endforeach
                                    @if($survey->sites->count() > $maxVisibleSites)
                                        <span class="modern-badge info" data-bs-toggle="tooltip" title="{{ $survey->sites->skip($maxVisibleSites)->pluck('name')->join(', ') }}">
                                            +{{ $survey->sites->count() - $maxVisibleSites }} More
                                        </span>
                                    @endif
                                @else
                                    <span class="modern-badge muted">No sites deployed</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-stats">
                    <div class="stat-card">
                        <div class="stat-number">{{ $totalResponses }}</div>
                        <div class="stat-label">Total Responses</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ now()->format('M d') }}</div>
                        <div class="stat-label">Generated</div>
                    </div>
                </div>
            </div>
            @if($survey->logo)
                <div class="header-logo">
                    <img src="{{ asset('storage/' . $survey->logo) }}" alt="Survey Logo" class="logo-img">
                </div>
            @endif
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="summary-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-chart-pie"></i>
                Executive Summary
            </h2>
            <p class="section-subtitle">Key performance indicators at a glance</p>
        </div>
        <div class="metrics-grid">
            <div class="metric-card primary">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-number">{{ $totalResponses }}</div>
                    <div class="metric-label">Total Responses</div>
                </div>
            </div>
            <div class="metric-card success">
                <div class="metric-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-number">{{ count($siteAnalytics) }}</div>
                    <div class="metric-label">Active Sites</div>
                </div>
            </div>
            <div class="metric-card {{ $hitPercentage >= 70 ? 'success' : ($hitPercentage >= 50 ? 'warning' : 'danger') }}">
                @php
                    $hitSites = collect($siteAnalytics)->where('qms_target_status', 'HIT')->count();
                    $totalSites = count($siteAnalytics);
                    $hitPercentage = $totalSites > 0 ? round(($hitSites / $totalSites) * 100, 1) : 0;
                @endphp
                <div class="metric-icon">
                    <i class="fas fa-target"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-number">{{ $hitPercentage }}%</div>
                    <div class="metric-label">QMS Target Achievement</div>
                </div>
            </div>
            <div class="metric-card {{ $avgNPS >= 70 ? 'success' : ($avgNPS >= 50 ? 'warning' : 'danger') }}">
                @php
                    $avgNPS = collect($npsData)->avg('nps_score');
                @endphp
                <div class="metric-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-number">{{ number_format($avgNPS, 1) }}</div>
                    <div class="metric-label">Average NPS Score</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating System Legend -->
    <div class="rating-legend-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i>
                Rating System Guide
            </h2>
            <p class="section-subtitle">Understanding our rating scale</p>
        </div>
        <div class="rating-legend-grid">
            <div class="rating-legend-item excellent">
                <div class="rating-badge">E</div>
                <div class="rating-info">
                    <h3>Excellent</h3>
                    <span class="rating-range">5.0</span>
                </div>
            </div>
            <div class="rating-legend-item very-satisfactory">
                <div class="rating-badge">VS</div>
                <div class="rating-info">
                    <h3>Very Satisfactory</h3>
                    <span class="rating-range">4.0 - 4.99</span>
                </div>
            </div>
            <div class="rating-legend-item satisfactory">
                <div class="rating-badge">S</div>
                <div class="rating-info">
                    <h3>Satisfactory</h3>
                    <span class="rating-range">3.0 - 3.99</span>
                </div>
            </div>
            <div class="rating-legend-item needs-improvement">
                <div class="rating-badge">NI</div>
                <div class="rating-info">
                    <h3>Needs Improvement</h3>
                    <span class="rating-range">2.0 - 2.99</span>
                </div>
            </div>
            <div class="rating-legend-item poor">
                <div class="rating-badge">P</div>
                <div class="rating-info">
                    <h3>Poor</h3>
                    <span class="rating-range">1.0 - 1.99</span>
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
    <div class="export-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-download"></i>
                Export Report
            </h2>
            <p class="section-subtitle">Download comprehensive survey report</p>
        </div>
        <div class="export-options">
            <div class="export-card">
                <div class="export-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div class="export-content">
                    <h3>Corporate Excel Export</h3>
                    <p>Professional report matching corporate format with styling</p>
                    <a href="{{ route('admin.surveys.export.excel', $survey) }}" class="export-btn">
                        <i class="fas fa-download"></i>
                        Download Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove SheetJS Library - No longer needed -->

<style>
/* Modern Report Styling with Theme Colors */
:root {
    --report-spacing: 2rem;
    --report-border-radius: 16px;
    --report-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    --report-shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
    --report-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Container */
.modern-report-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: var(--report-spacing);
    background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.02) 0%, rgba(var(--secondary-color-rgb), 0.02) 100%);
    min-height: 100vh;
}

/* Header Section */
.report-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: var(--report-border-radius);
    padding: 2.5rem;
    margin-bottom: var(--report-spacing);
    color: white;
    box-shadow: var(--report-shadow);
    position: relative;
    overflow: hidden;
}

.report-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(50%, -50%);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.header-main {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.survey-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.2;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.survey-meta {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.meta-item i {
    font-size: 1.1rem;
    opacity: 0.9;
    width: 20px;
}

.meta-label {
    font-weight: 600;
    min-width: 60px;
}

.meta-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.modern-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    transition: var(--report-transition);
}

.modern-badge.primary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.modern-badge.secondary {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

.modern-badge.warning {
    background: rgba(255, 193, 7, 0.9);
    color: #212529;
    border-color: rgba(255, 193, 7, 0.5);
}

.modern-badge.info {
    background: rgba(23, 162, 184, 0.9);
    color: white;
    border-color: rgba(23, 162, 184, 0.5);
}

.modern-badge.muted {
    background: rgba(108, 117, 125, 0.9);
    color: white;
    border-color: rgba(108, 117, 125, 0.5);
}

.header-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-card {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-width: 120px;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    font-weight: 500;
}

.header-logo {
    flex-shrink: 0;
}

.logo-img {
    max-height: 80px;
    max-width: 150px;
    object-fit: contain;
    filter: brightness(0) invert(1);
    opacity: 0.9;
}

/* Section Styling */
 .summary-section,
 .rating-legend-section,
 .export-section {
     background: white;
     border-radius: var(--report-border-radius);
     padding: 2rem;
     margin-bottom: var(--report-spacing);
     box-shadow: var(--report-shadow);
     transition: var(--report-transition);
 }
 
 .summary-section:hover,
 .rating-legend-section:hover,
 .export-section:hover {
     box-shadow: var(--report-shadow-hover);
     transform: translateY(-2px);
 }

.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.section-title i {
    font-size: 1.5rem;
}

.section-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

/* Metrics Grid */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.metric-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 16px;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: var(--report-transition);
    border: 1px solid #e9ecef;
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--primary-color);
    transition: var(--report-transition);
}

.metric-card.success::before {
    background: #28a745;
}

.metric-card.warning::before {
    background: #ffc107;
}

.metric-card.danger::before {
    background: #dc3545;
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--report-shadow-hover);
}

.metric-card:hover::before {
    width: 8px;
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: var(--primary-color);
    flex-shrink: 0;
}

.metric-card.success .metric-icon {
    background: #28a745;
}

.metric-card.warning .metric-icon {
    background: #ffc107;
    color: #212529;
}

.metric-card.danger .metric-icon {
    background: #dc3545;
}

.metric-content {
    flex: 1;
}

.metric-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.metric-label {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 500;
}

/* Rating Legend Grid */
.rating-legend-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.rating-legend-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--report-transition);
    border: 2px solid #e9ecef;
    position: relative;
    overflow: hidden;
}

.rating-legend-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    transition: var(--report-transition);
}

.rating-legend-item.excellent::before {
    background: #28a745;
}

.rating-legend-item.very-satisfactory::before {
    background: #007bff;
}

.rating-legend-item.satisfactory::before {
    background: #17a2b8;
}

.rating-legend-item.needs-improvement::before {
    background: #ffc107;
}

.rating-legend-item.poor::before {
    background: #dc3545;
}

.rating-legend-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

.rating-badge {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    color: white;
    flex-shrink: 0;
}

.rating-legend-item.excellent .rating-badge {
    background: #28a745;
}

.rating-legend-item.very-satisfactory .rating-badge {
    background: #007bff;
}

.rating-legend-item.satisfactory .rating-badge {
    background: #17a2b8;
}

.rating-legend-item.needs-improvement .rating-badge {
    background: #ffc107;
    color: #212529;
}

.rating-legend-item.poor .rating-badge {
    background: #dc3545;
}

.rating-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: #2d3748;
}

.rating-range {
     font-size: 0.9rem;
     color: #6c757d;
     font-weight: 500;
 }
 
 /* Export Section */
 .export-options {
     display: flex;
     justify-content: center;
 }
 
 .export-card {
     background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
     border-radius: 16px;
     padding: 2rem;
     display: flex;
     align-items: center;
     gap: 2rem;
     transition: var(--report-transition);
     border: 2px solid #e9ecef;
     max-width: 500px;
     width: 100%;
 }
 
 .export-card:hover {
     transform: translateY(-4px);
     box-shadow: var(--report-shadow-hover);
     border-color: var(--primary-color);
 }
 
 .export-icon {
     width: 80px;
     height: 80px;
     border-radius: 50%;
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: 2rem;
     color: white;
     background: #28a745;
     flex-shrink: 0;
 }
 
 .export-content {
     flex: 1;
     text-align: left;
 }
 
 .export-content h3 {
     font-size: 1.3rem;
     font-weight: 600;
     margin: 0 0 0.5rem 0;
     color: #2d3748;
 }
 
 .export-content p {
     color: #6c757d;
     margin: 0 0 1.5rem 0;
     font-size: 0.95rem;
 }
 
 .export-btn {
     background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
     color: white;
     padding: 0.75rem 1.5rem;
     border-radius: 8px;
     text-decoration: none;
     font-weight: 600;
     display: inline-flex;
     align-items: center;
     gap: 0.5rem;
     transition: var(--report-transition);
     border: none;
 }
 
 .export-btn:hover {
     transform: translateY(-2px);
     box-shadow: 0 4px 15px rgba(var(--primary-color-rgb), 0.3);
     color: white;
     text-decoration: none;
 }

/* Responsive Design */
 @media (max-width: 768px) {
     .modern-report-container {
         padding: 1rem;
     }
     
     .report-header {
         padding: 1.5rem;
     }
     
     .header-content {
         flex-direction: column;
         gap: 1.5rem;
     }
     
     .header-main {
         flex-direction: column;
         gap: 1.5rem;
     }
     
     .survey-title {
         font-size: 1.8rem;
     }
     
     .header-stats {
         justify-content: center;
     }
     
     .metrics-grid {
         grid-template-columns: 1fr;
     }
     
     .rating-legend-grid {
         grid-template-columns: 1fr;
     }
     
     .metric-card {
         padding: 1.5rem;
     }
     
     .export-card {
         flex-direction: column;
         text-align: center;
         padding: 1.5rem;
     }
     
     .export-content {
         text-align: center;
     }
     
     .section-title {
         font-size: 1.5rem;
     }
     
     .summary-section,
     .rating-legend-section,
     .export-section {
         padding: 1.5rem;
     }
 }

/* Legacy card styles for remaining sections */
.card {
    border-radius: var(--report-border-radius);
    border: none;
    box-shadow: var(--report-shadow);
    transition: var(--report-transition);
    margin-bottom: var(--report-spacing);
}

.card:hover {
    box-shadow: var(--report-shadow-hover);
    transform: translateY(-2px);
}

.card-header {
    background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.1) 0%, rgba(var(--secondary-color-rgb), 0.05) 100%);
    border-bottom: 2px solid rgba(var(--primary-color-rgb), 0.1);
    border-radius: var(--report-border-radius) var(--report-border-radius) 0 0 !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

.border-left-primary {
    border-left: 4px solid var(--primary-color) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
}

/* Table improvements */
.table th {
    background: rgba(var(--primary-color-rgb), 0.1);
    color: var(--primary-color);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    border: none;
}

.table-hover tbody tr:hover {
    background-color: rgba(var(--primary-color-rgb), 0.05);
}

/* Progress bars */
.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
    transition: var(--report-transition);
}

/* Badges */
.badge {
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
}

/* Accordion improvements */
.accordion-button {
    background: rgba(var(--primary-color-rgb), 0.05);
    border: none;
    border-radius: 8px !important;
    margin-bottom: 0.5rem;
}

.accordion-button:not(.collapsed) {
    background: rgba(var(--primary-color-rgb), 0.1);
    color: var(--primary-color);
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.25);
}

/* List group improvements */
.list-group-item {
    border-left: 3px solid var(--primary-color);
    border-radius: 8px !important;
    margin-bottom: 0.5rem;
    transition: var(--report-transition);
}

.list-group-item:hover {
    background-color: rgba(var(--primary-color-rgb), 0.05);
    transform: translateX(4px);
}

/* Animation for tooltips */
[data-bs-toggle="tooltip"] {
    cursor: help;
}

/* Print styles */
@media print {
    .modern-report-container {
        background: white;
        padding: 0;
    }
    
    .report-header {
        background: var(--primary-color) !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #dee2e6;
    }
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
