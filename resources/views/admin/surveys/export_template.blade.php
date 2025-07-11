<table>
    <!-- Title Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">CUSTOMER SATISFACTION SURVEY</td>
    </tr>
    
    <!-- Subtitle Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">1st Half of {{ date('Y') }} (January - June) Performance</td>
    </tr>
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Company Full Names Row -->
    <tr>
        <td></td>
        @foreach($siteAnalytics as $site)
            @php
                $sbuName = $site['sbu_name'] ?? '';
                $fullCompanyName = '';
                if (stripos($sbuName, 'FUI') !== false) {
                    $fullCompanyName = 'Fast Unimerchants Inc.';
                } elseif (stripos($sbuName, 'FDC') !== false) {
                    $fullCompanyName = 'Fast Distribution Corporation';
                }
            @endphp
            <td>{{ $fullCompanyName }}</td>
        @endforeach
    </tr>
    
    <!-- Company Acronyms Row -->
    <tr>
        <td></td>
        @foreach($siteAnalytics as $site)
            @php
                $sbuName = $site['sbu_name'] ?? '';
                $siteName = $site['site_name'] ?? '';
                
                // Create the full SBU identifier by combining SBU name with site suffix
                $fullSbuName = $sbuName;
                
                // Extract potential suffixes from site name (looking at the beginning of the site name)
                if ($siteName) {
                    // Common patterns for suffixes - check if site name starts with these patterns
                    if (stripos($siteName, 'MNC') === 0) {
                        $fullSbuName = $sbuName . ' MNC';
                    } elseif (stripos($siteName, 'NAI') === 0) {
                        $fullSbuName = $sbuName . ' NAI';
                    } elseif (stripos($siteName, 'Shell') === 0) {
                        $fullSbuName = $sbuName . ' Shell';
                    } elseif (stripos($siteName, 'Luzon') === 0) {
                        $fullSbuName = $sbuName . ' Luzon';
                    } elseif (stripos($siteName, 'Visayas') === 0) {
                        $fullSbuName = $sbuName . ' Visayas';
                    } elseif (stripos($siteName, 'Mindanao') === 0) {
                        $fullSbuName = $sbuName . ' Mindanao';
                    }
                    // Add more patterns as needed
                }
            @endphp
            <td>{{ $fullSbuName }}</td>
        @endforeach
    </tr>
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Site Headers Row -->
    <tr>
        <td></td>
        @foreach($siteAnalytics as $site)
            <td>{{ $site['site_name'] }}</td>
        @endforeach
    </tr>
    
    <!-- Respondents Row -->
    <tr>
        <td>Respondents</td>
        @foreach($siteAnalytics as $site)
            <td>{{ $site['respondent_count'] }}</td>
        @endforeach
    </tr>
    
    <!-- Question Ratings -->
    @php
        $ratingQuestions = $questions->filter(function($q) { 
            return $q->type === 'radio' || $q->type === 'star'; 
        });
    @endphp
    
    @foreach($ratingQuestions as $question)
    <tr>
        <td>{{ $question->text }}</td>
        @foreach($siteAnalytics as $site)
            @php
                $questionRating = $site['question_ratings'][$question->id] ?? null;
            @endphp
            <td>{{ $questionRating ? $questionRating['label'] : 'N/A' }}</td>
        @endforeach
    </tr>
    @endforeach
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Rating Scale Legend -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">1- Poor (P)  2 - Needs Improvement (NI)  3 - Satisfactory (S)  4 - Very Satisfactory (VS)  5 - Excellent (E)</td>
    </tr>
    
    <!-- Rating Range Legend -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">1 - 1.99 (P) | 2 - 2.99 (NI) | 3 - 3.99 (S) | 4 - 4.99 (VS) | 5 - (E)</td>
    </tr>

    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Overall Rating Header -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">Overall Rating</td>
    </tr>
    
    <!-- Overall Rating Numeric Values Row -->
    <tr>
        <td></td>
        @foreach($siteAnalytics as $site)
            <td>{{ number_format($site['overall_rating'], 2) }}</td>
        @endforeach
    </tr>
    
    <!-- Overall Rating Labels Row -->
    <tr>
        <td></td>
        @foreach($siteAnalytics as $site)
            @php
                $rating = $site['overall_rating'];
                $ratingLabel = '';
                if ($rating >= 1 && $rating < 2) {
                    $ratingLabel = 'P';
                } elseif ($rating >= 2 && $rating < 3) {
                    $ratingLabel = 'NI';
                } elseif ($rating >= 3 && $rating < 4) {
                    $ratingLabel = 'S';
                } elseif ($rating >= 4 && $rating < 5) {
                    $ratingLabel = 'VS';
                } elseif ($rating == 5) {
                    $ratingLabel = 'E';
                } else {
                    $ratingLabel = 'N/A';
                }
            @endphp
            <td>{{ $ratingLabel }}</td>
        @endforeach
    </tr>
    
    <!-- QMS Target Status -->
    <tr>
        <td>Based on QMS Target (Very Satisfactory)</td>
        @foreach($siteAnalytics as $site)
            <td>{{ $site['qms_target_status'] }}</td>
        @endforeach
    </tr>
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Net Promoter Score Header -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">Net Promoter Score</td>
    </tr>
    
    <!-- NPS Legend Row showing detractors, passives, promoters -->
    <tr>
        <td>0-6 || 7-8 || 9-10</td>
        @foreach($npsData as $nps)
            <td></td>
        @endforeach
    </tr>
    
    <!-- NPS Score Row -->
    <tr>
        <td></td>
        @foreach($npsData as $nps)
            <td>{{ $nps['nps_score'] }}</td>
        @endforeach
    </tr>
    
    <!-- NPS Status Row -->
    <tr>
        <td>Based on QMS Target (9-10) Promoter</td>
        @foreach($npsData as $nps)
            <td>{{ $nps['status'] }}</td>
        @endforeach
    </tr>
    
    <!-- Empty rows before signature -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    @if($recommendationStats && isset($recommendationStats['overall']['total_responses']) && $recommendationStats['overall']['total_responses'] > 0)
    <!-- Recommendation Analysis Section -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">RECOMMENDATION ANALYSIS</td>
    </tr>
    
    <!-- Average Recommendation Score -->
    <tr>
        <td>Average Recommendation Score (1-10)</td>
        @foreach($siteAnalytics as $siteData)
            @php
                $siteRecommendation = null;
                if (isset($recommendationStats['overall']['by_site'])) {
                    foreach ($recommendationStats['overall']['by_site'] as $site) {
                        if ($site['site_name'] === $siteData['site_name']) {
                            $siteRecommendation = $site['average_score'];
                            break;
                        }
                    }
                }
            @endphp
            <td>{{ $siteRecommendation ? number_format($siteRecommendation, 2) : 'N/A' }}</td>
        @endforeach
    </tr>
    
    <!-- Promoters Count (9-10) -->
    <tr>
        <td>Promoters (9-10)</td>
        @foreach($siteAnalytics as $siteData)
            @php
                $promoters = 0;
                if (isset($recommendationStats['overall']['by_site'])) {
                    foreach ($recommendationStats['overall']['by_site'] as $site) {
                        if ($site['site_name'] === $siteData['site_name']) {
                            $promoters = ($site['distribution'][9]['count'] ?? 0) + ($site['distribution'][10]['count'] ?? 0);
                            break;
                        }
                    }
                }
            @endphp
            <td>{{ $promoters }}</td>
        @endforeach
    </tr>
    
    <!-- Passives Count (7-8) -->
    <tr>
        <td>Passives (7-8)</td>
        @foreach($siteAnalytics as $siteData)
            @php
                $passives = 0;
                if (isset($recommendationStats['overall']['by_site'])) {
                    foreach ($recommendationStats['overall']['by_site'] as $site) {
                        if ($site['site_name'] === $siteData['site_name']) {
                            $passives = ($site['distribution'][7]['count'] ?? 0) + ($site['distribution'][8]['count'] ?? 0);
                            break;
                        }
                    }
                }
            @endphp
            <td>{{ $passives }}</td>
        @endforeach
    </tr>
    
    <!-- Detractors Count (1-6) -->
    <tr>
        <td>Detractors (1-6)</td>
        @foreach($siteAnalytics as $siteData)
            @php
                $detractors = 0;
                if (isset($recommendationStats['overall']['by_site'])) {
                    foreach ($recommendationStats['overall']['by_site'] as $site) {
                        if ($site['site_name'] === $siteData['site_name']) {
                            for ($i = 1; $i <= 6; $i++) {
                                $detractors += $site['distribution'][$i]['count'] ?? 0;
                            }
                            break;
                        }
                    }
                }
            @endphp
            <td>{{ $detractors }}</td>
        @endforeach
    </tr>
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    @endif
    
    @if($improvementAreasStats && isset($improvementAreasStats['categories']) && count($improvementAreasStats['categories']) > 0)
    <!-- Areas for Improvement Section -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">AREAS FOR IMPROVEMENT</td>
    </tr>
    
    <!-- Top Improvement Categories -->
    @php
        $topCategories = array_slice($improvementAreasStats['top_categories'], 0, 5, true);
    @endphp
    @foreach($topCategories as $categoryName => $categoryData)
    <tr>
        <td>{{ str_replace('_', ' ', ucfirst($categoryName)) }}</td>
        @foreach($siteAnalytics as $siteData)
            @php
                $categoryCount = 0;
                if (isset($improvementAreasStats['by_site'])) {
                    foreach ($improvementAreasStats['by_site'] as $site) {
                        if ($site['site_name'] === $siteData['site_name']) {
                            $categoryCount = $site['categories'][$categoryName]['count'] ?? 0;
                            break;
                        }
                    }
                }
            @endphp
            <td>{{ $categoryCount }}</td>
        @endforeach
    </tr>
    @endforeach
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    @endif
    
    <!-- Signature Section -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">Reviewed by:</td>
    </tr>
    
    <!-- Empty Rows for signature space -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Signature Names -->
    <tr>
        <td></td>
        <td>Ferdinand T. Ozon</td>
        <td></td>
        <td>Alberto Inocencio P. de Veyra, Jr.</td>
        @for($i = 4; $i <= count($siteAnalytics); $i++)
            <td></td>
        @endfor
    </tr>
    
    <!-- Signature Titles -->
    <tr>
        <td></td>
        <td>AVP for Operations (FUI)</td>
        <td></td>
        <td>Chief Executive Officer</td>
        @for($i = 4; $i <= count($siteAnalytics); $i++)
            <td></td>
        @endfor
    </tr>
</table>
