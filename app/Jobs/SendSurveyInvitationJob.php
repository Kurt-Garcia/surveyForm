<?php

namespace App\Jobs;

use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendSurveyInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    protected $survey;
    protected $customer;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(Survey $survey, $customer, $batchId)
    {
        $this->survey = $survey;
        $this->customer = $customer;
        $this->batchId = $batchId;
        $this->onQueue('high'); // Use high priority queue for email sending
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Create personalized survey URL with customer name and account type pre-filled
            $surveyUrl = route('customer.survey', $this->survey->id) . '?account_name=' . urlencode($this->customer->CUSTNAME) . '&account_type=' . urlencode($this->customer->CUSTTYPE ?? 'Customer');
            
            // Prepare email data
            $emailData = [
                'customer_name' => $this->customer->CUSTNAME,
                'survey_title' => $this->survey->title,
                'survey_url' => $surveyUrl
            ];
            
            // Send email to customer
            Mail::send('emails.survey_invitation', $emailData, function($message) {
                $message->to($this->customer->EMAIL, $this->customer->CUSTNAME)
                        ->subject('You\'re invited to complete a survey: ' . $this->survey->title)
                        ->from('testsurvey_1@w-itsolutions.com', 'Fast Distribution Corporation');
            });
            
            // Update progress in cache
            $this->updateProgress('success');
            
            Log::info("Survey invitation sent successfully to {$this->customer->EMAIL} for survey {$this->survey->title}");
            
        } catch (\Exception $e) {
            Log::error("Failed to send survey email to {$this->customer->EMAIL}: " . $e->getMessage());
            
            // Update progress in cache with failure
            $this->updateProgress('failed');
            
            // Re-throw exception to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Survey invitation job failed permanently for {$this->customer->EMAIL}: " . $exception->getMessage());
        
        // Update progress in cache
        $this->updateProgress('failed');
    }

    /**
     * Update progress tracking in cache
     */
    private function updateProgress($status): void
    {
        $cacheKey = "broadcast_progress_{$this->batchId}";
        $progress = Cache::get($cacheKey, ['sent' => 0, 'failed' => 0, 'total' => 0]);
        
        if ($status === 'success') {
            $progress['sent']++;
        } elseif ($status === 'failed') {
            $progress['failed']++;
        }
        
        Cache::put($cacheKey, $progress, 3600); // Cache for 1 hour
    }
}
