<table>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 2 }}">DETAILED QUESTION ANALYSIS</td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 2 }}"></td>
    </tr>
    <tr>
        <td>Question</td>
        @foreach($siteAnalytics as $site)
            <td>{{ $site['site_name'] }}</td>
        @endforeach
        <td>Overall Avg</td>
    </tr>
    
    @php
        $ratingQuestions = $questions->filter(function($q) { 
            return $q->type === 'radio' || $q->type === 'star'; 
        });
    @endphp
    
    @foreach($ratingQuestions as $question)
        <tr>
            <td>{{ strlen($question->text) > 50 ? substr($question->text, 0, 50) . '...' : $question->text }}</td>
            @php
                $totalAvg = 0;
                $siteCount = 0;
            @endphp
            @foreach($siteAnalytics as $site)
                @php
                    $questionRating = $site['question_ratings'][$question->id] ?? null;
                    if ($questionRating) {
                        $totalAvg += $questionRating['average'];
                        $siteCount++;
                    }
                @endphp
                <td>{{ $questionRating ? number_format($questionRating['average'], 2) . ' (' . $questionRating['label'] . ')' : 'N/A' }}</td>
            @endforeach
            <td>{{ $siteCount > 0 ? number_format($totalAvg / $siteCount, 2) : 'N/A' }}</td>
        </tr>
    @endforeach
    
    <tr>
        <td colspan="{{ count($siteAnalytics) + 2 }}"></td>
    </tr>
    <tr>
        <td colspan="{{ count($siteAnalytics) + 2 }}">RATING SCALE REFERENCE</td>
    </tr>
    <tr>
        <td>Rating</td>
        <td>Label</td>
        <td>Description</td>
        <td>Score Range</td>
        @for($i = 4; $i < count($siteAnalytics) + 2; $i++)
            <td></td>
        @endfor
    </tr>
    <tr>
        <td>5</td>
        <td>E</td>
        <td>Excellent</td>
        <td>5.0</td>
        @for($i = 4; $i < count($siteAnalytics) + 2; $i++)
            <td></td>
        @endfor
    </tr>
    <tr>
        <td>4</td>
        <td>VS</td>
        <td>Very Satisfactory</td>
        <td>4.0 - 4.99</td>
        @for($i = 4; $i < count($siteAnalytics) + 2; $i++)
            <td></td>
        @endfor
    </tr>
    <tr>
        <td>3</td>
        <td>S</td>
        <td>Satisfactory</td>
        <td>3.0 - 3.99</td>
        @for($i = 4; $i < count($siteAnalytics) + 2; $i++)
            <td></td>
        @endfor
    </tr>
    <tr>
        <td>2</td>
        <td>NI</td>
        <td>Needs Improvement</td>
        <td>2.0 - 2.99</td>
        @for($i = 4; $i < count($siteAnalytics) + 2; $i++)
            <td></td>
        @endfor
    </tr>
    <tr>
        <td>1</td>
        <td>P</td>
        <td>Poor</td>
        <td>1.0 - 1.99</td>
        @for($i = 4; $i < count($siteAnalytics) + 2; $i++)
            <td></td>
        @endfor
    </tr>
</table>
