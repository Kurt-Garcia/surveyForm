<table>
    <tr>
        <td colspan="10">NET PROMOTER SCORE (NPS) DETAILED ANALYSIS</td>
    </tr>
    <tr>
        <td colspan="10"></td>
    </tr>
    <tr>
        <td colspan="10">NPS METHODOLOGY</td>
    </tr>
    <tr>
        <td>• Promoters: Scores 9-10 (Highly likely to recommend)</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td>• Passives: Scores 7-8 (Neutral, not counted in NPS calculation)</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td>• Detractors: Scores 0-6 (Unlikely to recommend)</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td>• Formula: NPS = (% Promoters) - (% Detractors)</td>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="10"></td>
    </tr>
    <tr>
        <td>Site</td>
        <td>SBU</td>
        <td>Total Responses</td>
        <td>Promoters</td>
        <td>Promoters %</td>
        <td>Passives</td>
        <td>Detractors</td>
        <td>Detractors %</td>
        <td>NPS Score</td>
        <td>Classification</td>
    </tr>
    @foreach($npsData as $nps)
        @php
            $passives = $nps['total_respondents'] - $nps['promoters'] - $nps['detractors'];
            $promoterPct = $nps['total_respondents'] > 0 ? round(($nps['promoters'] / $nps['total_respondents']) * 100, 1) : 0;
            $detractorPct = $nps['total_respondents'] > 0 ? round(($nps['detractors'] / $nps['total_respondents']) * 100, 1) : 0;
        @endphp
        <tr>
            <td>{{ $nps['site_name'] }}</td>
            <td>{{ $nps['sbu_name'] }}</td>
            <td>{{ $nps['total_respondents'] }}</td>
            <td>{{ $nps['promoters'] }}</td>
            <td>{{ $promoterPct }}%</td>
            <td>{{ $passives }}</td>
            <td>{{ $nps['detractors'] }}</td>
            <td>{{ $detractorPct }}%</td>
            <td>{{ $nps['nps_score'] }}</td>
            <td>{{ $nps['status'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="10"></td>
    </tr>
    <tr>
        <td colspan="10">NPS INTERPRETATION GUIDE</td>
    </tr>
    <tr>
        <td>NPS Score Range</td>
        <td>Classification</td>
        <td>Description</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>70 to 100</td>
        <td>HIT - Excellent</td>
        <td>World-class customer satisfaction</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>50 to 69</td>
        <td>HIT - Good</td>
        <td>Good customer satisfaction</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>30 to 49</td>
        <td>Borderline</td>
        <td>Room for improvement</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>0 to 29</td>
        <td>MISS - Needs Work</td>
        <td>Significant improvements needed</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>-100 to -1</td>
        <td>MISS - Critical</td>
        <td>Critical attention required</td>
        <td colspan="7"></td>
    </tr>
</table>
