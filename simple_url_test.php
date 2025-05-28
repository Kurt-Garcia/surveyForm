<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing URL generation...\n";

try {
    $survey = App\Models\Survey::where('is_active', true)->first();
    
    if ($survey) {
        echo "Survey found: " . $survey->title . "\n";
        $url = route('customer.survey', $survey->id);
        echo "Generated URL: " . $url . "\n";
        
        // Test URL with cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP Status: " . $httpCode . "\n";
        
        if ($httpCode == 200) {
            echo "✅ Success! Email URLs will now work correctly.\n";
        } else {
            echo "❌ URL returned HTTP " . $httpCode . "\n";
        }
    } else {
        echo "No active surveys found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
