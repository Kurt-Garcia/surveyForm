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
    
    <!-- Company Header Row -->
    <tr>
        <td></td>
        @foreach($siteAnalytics as $index => $site)
            @if($index == count($siteAnalytics) - 2)
                <td>FAST Unimerchants Inc.</td>
            @elseif($index == count($siteAnalytics) - 1)
                <td>FUI MNC</td>
            @else
                <td></td>
            @endif
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
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Overall Rating Header -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">Overall Rating</td>
    </tr>
    
    <!-- Overall Rating Row -->
    <tr>
        <td>Based on QMS Target (Very Satisfactory)</td>
        @foreach($siteAnalytics as $site)
            <td>{{ number_format($site['overall_rating'], 2) }}</td>
        @endforeach
    </tr>
    
    <!-- QMS Target Status -->
    <tr>
        <td></td>
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
    
    <!-- NPS Score Row -->
    <tr>
        <td>Based on QMS Target (9-10) Promoter</td>
        @foreach($npsData as $nps)
            <td>{{ $nps['nps_score'] }}</td>
        @endforeach
    </tr>
    
    <!-- NPS Status Row -->
    <tr>
        <td></td>
        @foreach($npsData as $nps)
            <td>{{ $nps['status'] }}</td>
        @endforeach
    </tr>
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- FEEDBACKS Header -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}">FEEDBACKS</td>
    </tr>
    
    <!-- Empty Row -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Positive Feedback Header Row -->
    <tr>
        <td>Positive Feedback</td>
        @foreach($siteAnalytics as $site)
            <td>{{ $site['site_name'] }}</td>
        @endforeach
    </tr>
    
    <!-- Sample Positive Feedback Data -->
    @php
        $positiveFeedback = [
            'The salesman is good, well mannered, honest',
            'The salesman visits store regularly.',
            'Satisfied customer.',
            'Good service provided.',
            'Fast delivery.'
        ];
        
        $positiveCounts = [
            ['-', '4', '-'],
            ['7', '-', '2'],
            ['16', '-', '3'],
            ['3', '2', '-'],
            ['-', '1', '5']
        ];
    @endphp
    
    @foreach($positiveFeedback as $index => $feedback)
    <tr>
        <td>{{ $index + 1 }}. {{ $feedback }}</td>
        @if(isset($positiveCounts[$index]))
            @foreach($positiveCounts[$index] as $count)
                <td>{{ $count }}</td>
            @endforeach
        @else
            @foreach($siteAnalytics as $site)
                <td>-</td>
            @endforeach
        @endif
    </tr>
    @endforeach
    
    <!-- Empty Rows -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
    <!-- Areas for Improvement Header -->
    <tr>
        <td>Areas for Improvement</td>
        @foreach($siteAnalytics as $site)
            <td>{{ $site['site_name'] }}</td>
        @endforeach
    </tr>
    
    @php
        $improvements = [
            'The salesman missed to deliver on time',
            'Request to replace nearly expired products.',
            'Dissatisfied customer.',
            'Poor product quality.',
            'Delayed response to queries.'
        ];
        
        $improvementCounts = [
            ['3', '-', '1'],
            ['1', '2', '-'],
            ['-', '1', '2'],
            ['2', '-', '-'],
            ['-', '3', '1']
        ];
    @endphp
    
    @foreach($improvements as $index => $improvement)
    <tr>
        <td>{{ $index + 1 }}. {{ $improvement }}</td>
        @if(isset($improvementCounts[$index]))
            @foreach($improvementCounts[$index] as $count)
                <td>{{ $count }}</td>
            @endforeach
        @else
            @foreach($siteAnalytics as $site)
                <td>-</td>
            @endforeach
        @endif
    </tr>
    @endforeach
    
    <!-- Empty Rows -->
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 1 }}"></td>
    </tr>
    
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
