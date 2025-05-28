<?php

namespace App\Jobs;

use App\Models\Survey;
use App\Jobs\SendSurveyInvitationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessSurveyBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 1;

    protected $survey;
    protected $customerIds;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(Survey $survey, array $customerIds, string $batchId)
    {
        $this->survey = $survey;
        $this->customerIds = $customerIds;
        $this->batchId = $batchId;
        $this->onQueue('medium'); // Use medium priority queue for batch processing
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get customers from database
            $customers = DB::table('TBLCUSTOMER')
                ->select('id', 'CUSTCODE', 'CUSTNAME', 'EMAIL', 'CUSTTYPE')
                ->whereIn('id', $this->customerIds)
                ->whereNotNull('EMAIL')
                ->where('EMAIL', '!=', '')
                ->get();

            $totalCustomers = $customers->count();
            
            // Initialize progress tracking
            $this->initializeProgress($totalCustomers);
            
            Log::info("Starting broadcast for survey {$this->survey->title} to {$totalCustomers} customers");
            
            // Dispatch individual email jobs
            foreach ($customers as $customer) {
                SendSurveyInvitationJob::dispatch($this->survey, $customer, $this->batchId)
                    ->delay(now()->addSeconds(rand(1, 5))); // Add small random delay to spread load
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to process survey broadcast: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initialize progress tracking in cache
     */
    private function initializeProgress(int $totalCustomers): void
    {
        $cacheKey = "broadcast_progress_{$this->batchId}";
        $progress = [
            'sent' => 0,
            'failed' => 0,
            'total' => $totalCustomers,
            'started_at' => now()->toISOString(),
            'status' => 'processing'
        ];
        
        Cache::put($cacheKey, $progress, 3600); // Cache for 1 hour
    }
}
