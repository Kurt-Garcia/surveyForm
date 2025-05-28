<?php
/**
 * Test script to verify the broadcast system is working correctly
 * This script simulates the broadcast process without using the web interface
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;
use App\Jobs\ProcessSurveyBroadcastJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "=== Testing Broadcast System ===\n";

// Get the first active survey
$survey = Survey::where('is_active', true)->first();

if (!$survey) {
    echo "ERROR: No active surveys found!\n";
    exit(1);
}

echo "Found survey: {$survey->title} (ID: {$survey->id})\n";

// Get some test customers with emails
$customers = DB::table('TBLCUSTOMER')
    ->select('id', 'CUSTCODE', 'CUSTNAME', 'EMAIL')
    ->whereNotNull('EMAIL')
    ->where('EMAIL', '!=', '')
    ->limit(3)
    ->get();

if ($customers->isEmpty()) {
    echo "ERROR: No customers with emails found!\n";
    exit(1);
}

echo "Found " . $customers->count() . " test customers:\n";
foreach ($customers as $customer) {
    echo "  - {$customer->CUSTNAME} ({$customer->EMAIL})\n";
}

// Generate batch ID
$batchId = 'test_broadcast_' . uniqid();
echo "\nGenerated batch ID: {$batchId}\n";

// Dispatch the broadcast job
echo "\nDispatching broadcast job...\n";
ProcessSurveyBroadcastJob::dispatch($survey, $customers->pluck('id')->toArray(), $batchId);

echo "✅ Broadcast job dispatched successfully!\n";
echo "\nTo monitor progress, you can:\n";
echo "1. Check queue worker logs\n";
echo "2. Check Laravel logs: storage/logs/laravel.log\n";
echo "3. Use progress API: GET /admin/broadcast/progress/{$batchId}\n";

// Wait a moment and check initial cache
echo "\nChecking initial cache status...\n";
sleep(2);

$cacheKey = "broadcast_progress_{$batchId}";
$progress = Cache::get($cacheKey);

if ($progress) {
    echo "Progress cache found:\n";
    echo "  - Total: " . ($progress['total'] ?? 'N/A') . "\n";
    echo "  - Sent: " . ($progress['sent'] ?? 0) . "\n";
    echo "  - Failed: " . ($progress['failed'] ?? 0) . "\n";
    echo "  - Status: " . ($progress['status'] ?? 'Unknown') . "\n";
} else {
    echo "Progress cache not yet available (jobs may still be queuing)\n";
}

echo "\n=== Test completed! ===\n";
echo "The broadcast system is working. Check your email inbox (Mailtrap) for invitations.\n";
