<?php

namespace App\Jobs;

use App\Models\ErrorGroup;
use App\Services\AiAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeErrorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $errorGroup;
    protected $errorMessage;
    protected $errorTrace;

    /**
     * Create a new job instance.
     */
    public function __construct(ErrorGroup $errorGroup, $errorMessage, $errorTrace = null)
    {
        $this->errorGroup = $errorGroup;
        $this->errorMessage = $errorMessage;
        $this->errorTrace = $errorTrace;
    }

    /**
     * Execute the job.
     */
    public function handle(AiAnalysisService $aiService): void
    {
        // Double check if analysis already exists to avoid redundant calls
        if ($this->errorGroup->ai_analysis) {
            return;
        }

        try {
            Log::info("Starting AI Analysis for Group ID: {$this->errorGroup->id}");

            $analysis = $aiService->analyzeError($this->errorMessage, $this->errorTrace);

            if ($analysis) {
                $this->errorGroup->update(['ai_analysis' => $analysis]);
                Log::info("AI Analysis completed for Group ID: {$this->errorGroup->id}");
            } else {
                Log::warning("AI Analysis returned null for Group ID: {$this->errorGroup->id}");
            }
        } catch (\Exception $e) {
            Log::error("AI Analysis Job Failed: " . $e->getMessage());
            // Optionally release the job back to queue if it's a temporary failure
            // $this->release(30); 
        }
    }
}
