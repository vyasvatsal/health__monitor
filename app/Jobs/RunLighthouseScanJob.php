<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Store;
use App\Models\Analysis;
use App\Services\Analysis\LighthouseScanner;
use Illuminate\Support\Facades\Log;

class RunLighthouseScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $storeId;
    protected $url;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $storeId, string $url, string $batchId)
    {
        $this->storeId = $storeId;
        $this->url = $url;
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $scanner = new LighthouseScanner();
        $scanData = $scanner->scan($this->url);

        if (!$scanData) {
            Log::error("Background Lighthouse Scan Failed for {$this->url}");
            return;
        }

        // We do NOT run AI analysis here. We wait until ALL jobs in the batch are done
        // to run one unified AI synthesis prompt on the Master Controller.
        
        Analysis::create([
            'store_id' => $this->storeId,
            'batch_id' => $this->batchId,
            'url' => $this->url,
            'performance_score' => $scanData['scores']['performance'] ?? 0,
            'accessibility_score' => $scanData['scores']['accessibility'] ?? 0,
            'best_practices_score' => $scanData['scores']['best_practices'] ?? 0,
            'seo_score' => $scanData['scores']['seo'] ?? 0,
            'core_web_vitals' => $scanData['web_vitals'] ?? [],
            'ai_insights' => null, // Left null until batch completes
            'desktop_screenshot' => $scanData['screenshot'], 
            // We save the raw AI feed so the batch processor can use it later
            'ai_feed' => $scanData['ai_feed'] ?? [],
        ]);
    }
}
