<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store;
use App\Models\Analysis;
use App\Services\Analysis\LighthouseScanner;
use App\Services\AI\GeminiService;

class AnalysisController extends Controller
{
    /**
     * Display the GTmetrix-style batch report.
     */
    public function show(Store $store)
    {
        // Get the most recent analysis to find the latest batch_id
        $latestRecord = $store->analyses()->latest()->first();

        if (!$latestRecord) {
            return view('analysis.empty', compact('store'));
        }

        $batchId = $latestRecord->batch_id;
        $batchAnalyses = $store->analyses()->where('batch_id', $batchId)->get();

        // Check if jobs are still running (we expect 4 pages scanned based on the controller logic)
        // If there are less than 4, or if ai_insights is null on the First record but jobs are done, we process.
        $isProcessing = $batchAnalyses->count() < 4;

        $masterInsights = null;

        if (!$isProcessing && count($batchAnalyses) > 0) {
            // Check if we've already generated the Master AI insights for this batch
            // We usually store the master insight on the 'first' record of the batch
            $firstRecord = $batchAnalyses->first();
            
            if (empty($firstRecord->ai_insights)) {
                // All jobs done, but no master AI synthesis yet. Run it now synchronously.
                $aiService = new GeminiService();
                $masterInsightsText = $aiService->analyzeBatch($batchAnalyses->toArray());
                
                // Save it down to the first record so we don't re-run it
                $firstRecord->update([
                    'ai_insights' => ['insight_text' => $masterInsightsText]
                ]);
                
                $masterInsights = ['insight_text' => $masterInsightsText];
            } else {
                $masterInsights = $firstRecord->ai_insights;
            }
        }

        return view('analysis.show', compact('store', 'batchAnalyses', 'isProcessing', 'masterInsights'));
    }

    /**
     * Trigger a bulk Lighthouse deep scan.
     */
    public function store(Request $request, Store $store)
    {
        $baseUrl = $store->domain ?? $request->input('url');

        if (!$baseUrl) {
            return back()->with('error', 'No domain configured for this store.');
        }

        // Add scheme if missing
        if (!str_starts_with($baseUrl, 'http://') && !str_starts_with($baseUrl, 'https://')) {
            $baseUrl = 'https://' . $baseUrl;
        }

        $baseUrl = rtrim($baseUrl, '/');

        // For V1 of bulk scanning, we will scan the Homepage and append a few common sub-paths
        // In V2, we would parse the sitemap.xml to find the highest-priority URLs
        $urlsToScan = [
            $baseUrl,
            $baseUrl . '/shop',
            $baseUrl . '/about',
            $baseUrl . '/contact',
        ];

        // Ensure we only scan unique URLs (in case of weird trailing slashes)
        $urlsToScan = array_unique($urlsToScan);

        $batchId = (string) \Illuminate\Support\Str::uuid();

        foreach ($urlsToScan as $url) {
            \App\Jobs\RunLighthouseScanJob::dispatch($store->id, $url, $batchId);
        }

        // We redirect immediately while jobs run in the background
        return redirect()->route('analysis.show', $store->id)
            ->with('success', 'Deep Scan Initiated! We are analyzing ' . count($urlsToScan) . ' pages in the background. Check back in a few minutes for the master report.');
    }
}
