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
     * Display the GTmetrix-style report.
     */
    public function show(Store $store)
    {
        // Get latest analysis
        $analysis = $store->analyses()->latest()->first();

        // If no analysis exists, return a view asking them to run one
        if (!$analysis) {
            return view('analysis.empty', compact('store'));
        }

        return view('analysis.show', compact('store', 'analysis'));
    }

    /**
     * Trigger a new Lighthouse deep scan.
     */
    public function store(Request $request, Store $store)
    {
        $url = $store->domain ?? $request->input('url');

        if (!$url) {
            return back()->with('error', 'No domain configured for this store.');
        }

        // Add scheme if missing
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }

        $scanner = new LighthouseScanner();
        $scanData = $scanner->scan($url);

        if (!$scanData) {
            return back()->with('error', 'Lighthouse scan failed. The URL might be unreachable.');
        }

        // Run AI Analysis
        $aiService = new GeminiService();
        $aiInsightsText = $aiService->analyzeDeepScan($scanData);

        // Save to DB
        $analysis = Analysis::create([
            'store_id' => $store->id,
            'url' => $url,
            'performance_score' => $scanData['scores']['performance'] ?? 0,
            'accessibility_score' => $scanData['scores']['accessibility'] ?? 0,
            'best_practices_score' => $scanData['scores']['best_practices'] ?? 0,
            'seo_score' => $scanData['scores']['seo'] ?? 0,
            'core_web_vitals' => $scanData['web_vitals'] ?? [],
            'ai_insights' => ['insight_text' => $aiInsightsText], // Stored as array due to JSON cast
            'desktop_screenshot' => $scanData['screenshot'], // Huge base64 string
        ]);

        return redirect()->route('analysis.show', $store->id)->with('success', 'Deep Scan Complete!');
    }
}
