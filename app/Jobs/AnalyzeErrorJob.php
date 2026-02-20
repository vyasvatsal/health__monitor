<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\ErrorGroup;
use App\Services\AI\Providers\GroqProvider;
use Illuminate\Support\Facades\Log;

class AnalyzeErrorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ErrorGroup $errorGroup;

    /**
     * Create a new job instance.
     */
    public function __construct(ErrorGroup $errorGroup)
    {
        $this->errorGroup = $errorGroup;
    }

    /**
     * Execute the job.
     */
    public function handle(GroqProvider $ai): void
    {
        try {
            // Fetch the latest event for context
            $latestEvent = $this->errorGroup->events()->latest('occurred_at')->first();

            if (!$latestEvent) {
                return;
            }

            $systemPrompt = "You are an expert Laravel and modern JavaScript developer debugging an application error.
Analyze the following error report. Return a JSON object with exactly these 3 keys:
- 'title': A short, human-readable title for the error (under 60 chars).
- 'cause_explanation': A clear, concise explanation of why this error happens (under 300 chars).
- 'suggested_fix': A specific, actionable fix or code snippet to resolve it.

Be very precise and focus on practical solutions.";

            $userPrompt = "Error Message: {$this->errorGroup->title}\n";
            $userPayload = is_string($latestEvent->payload) ? json_decode($latestEvent->payload, true) : $latestEvent->payload;

            $userPrompt .= "File: " . ($userPayload['file'] ?? 'unknown') . "\n";
            $userPrompt .= "Line: " . ($userPayload['line'] ?? '0') . "\n";
            $userPrompt .= "Type: " . ($userPayload['type'] ?? 'unknown') . "\n\n";

            if ($latestEvent->stack_trace) {
                $trace = is_string($latestEvent->stack_trace) ? json_decode($latestEvent->stack_trace, true) ?? $latestEvent->stack_trace : $latestEvent->stack_trace;
                $userPrompt .= "Stack Trace:\n" . (is_array($trace) ? implode("\n", array_slice($trace, 0, 5)) : substr($trace, 0, 1000));
            }

            $analysis = $ai->analyze($systemPrompt, $userPrompt);

            // Save the AI insights
            $this->errorGroup->update([
                'ai_solution' => [
                    'title' => $analysis['title'] ?? 'Unknown Error',
                    'explanation' => $analysis['cause_explanation'] ?? 'Could not determine cause.',
                    'fix' => $analysis['suggested_fix'] ?? 'No fix available.',
                    'analyzed_at' => now()->toIso8601String()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("AnalyzeErrorJob Failed: " . $e->getMessage(), [
                'error_group_id' => $this->errorGroup->id
            ]);

            // Allow retry if it's a network issue with Groq
            if ($this->attempts() < 3) {
                $this->release(60); // retry after 60s
            }
        }
    }
}
