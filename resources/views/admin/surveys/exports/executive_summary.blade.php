<table>
    <tr>
        <td colspan="4">EXECUTIVE SUMMARY - CUSTOMER SATISFACTION SURVEY</td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4">KEY PERFORMANCE INDICATORS</td>
    </tr>
    <tr>
        <td>Metric</td>
        <td>Value</td>
        <td>Target</td>
        <td>Status</td>
    </tr>
    <tr>
        <td>Survey Period</td>
        <td>1st Half of {{ date('Y') }} (January - June)</td>
        <td>-</td>
        <td>ACTIVE</td>
    </tr>
    <tr>
        <td>Total Responses</td>
        <td>{{ $totalResponses }}</td>
        <td>> 100</td>
        <td>{{ $totalResponses > 100 ? 'MET' : 'NOT MET' }}</td>
    </tr>
    <tr>
        <td>Sites Covered</td>
        <td>{{ $sitesCount }}</td>
        <td>All Sites</td>
        <td>COMPLETE</td>
    </tr>
    <tr>
        <td>QMS Target Achievement</td>
        <td>{{ $hitPercentage }}%</td>
        <td>≥ 70%</td>
        <td>{{ $hitPercentage >= 70 ? 'EXCELLENT' : ($hitPercentage >= 50 ? 'GOOD' : 'NEEDS IMPROVEMENT') }}</td>
    </tr>
    <tr>
        <td>Average NPS Score</td>
        <td>{{ number_format($avgNPS, 1) }}</td>
        <td>≥ 50</td>
        <td>{{ $avgNPS >= 70 ? 'EXCELLENT' : ($avgNPS >= 50 ? 'GOOD' : 'NEEDS IMPROVEMENT') }}</td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4">BUSINESS UNIT COVERAGE</td>
    </tr>
    <tr>
        <td>SBUs with Responses</td>
        <td>{{ $survey->sbus->pluck('name')->join(', ') }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Sites with Responses</td>
        <td>{{ count($siteAnalytics) }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td colspan="4">OVERALL PERFORMANCE SUMMARY</td>
    </tr>
    <tr>
        <td>Sites Meeting QMS Target</td>
        <td>{{ collect($siteAnalytics)->where('qms_target_status', 'HIT')->count() }} out of {{ count($siteAnalytics) }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Sites with Excellent NPS</td>
        <td>{{ collect($npsData)->where('status', 'HIT')->count() }} out of {{ count($npsData) }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Average Customer Satisfaction</td>
        <td>{{ $hitPercentage }}% of sites meet or exceed targets</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td>Report Generated</td>
        <td>{{ now()->format('F d, Y \a\t g:i A') }}</td>
        <td>-</td>
        <td>-</td>
    </tr>
</table>
