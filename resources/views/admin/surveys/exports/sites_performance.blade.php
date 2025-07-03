<table>
    <tr>
        <td colspan="9">SITES PERFORMANCE MATRIX</td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td>Site Name</td>
        <td>SBU</td>
        <td>Type</td>
        <td>Respondents</td>
        <td>Overall Rating</td>
        <td>Rating Grade</td>
        <td>QMS Target</td>
        <td>NPS Score</td>
        <td>NPS Status</td>
    </tr>
    @foreach($siteAnalytics as $index => $site)
        @php
            $nps = $npsData[$index] ?? ['nps_score' => 0, 'status' => 'N/A'];
        @endphp
        <tr>
            <td>{{ $site['site_name'] }}</td>
            <td>{{ $site['sbu_name'] }}</td>
            <td>{{ $site['is_main'] ? 'Main Site' : 'Sub Site' }}</td>
            <td>{{ $site['respondent_count'] }}</td>
            <td>{{ number_format($site['overall_rating'], 2) }}</td>
            <td>{{ $site['rating_label'] }}</td>
            <td>{{ $site['qms_target_status'] }}</td>
            <td>{{ $nps['nps_score'] }}</td>
            <td>{{ $nps['status'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="9"></td>
    </tr>
    <tr>
        <td colspan="9">PERFORMANCE SUMMARY</td>
    </tr>
    <tr>
        <td>Total Sites</td>
        <td>{{ count($siteAnalytics) }}</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>Sites Meeting QMS Target</td>
        <td>{{ collect($siteAnalytics)->where('qms_target_status', 'HIT')->count() }}</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>QMS Achievement Rate</td>
        <td>{{ count($siteAnalytics) > 0 ? round((collect($siteAnalytics)->where('qms_target_status', 'HIT')->count() / count($siteAnalytics)) * 100, 1) : 0 }}%</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>Average Overall Rating</td>
        <td>{{ count($siteAnalytics) > 0 ? number_format(collect($siteAnalytics)->avg('overall_rating'), 2) : 0 }}</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>Average NPS Score</td>
        <td>{{ count($npsData) > 0 ? number_format(collect($npsData)->avg('nps_score'), 1) : 0 }}</td>
        <td colspan="7"></td>
    </tr>
</table>
