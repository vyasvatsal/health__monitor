<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store;
use App\Models\PageMetrics;
use App\Models\CrawledPage;
use App\Jobs\CrawlStoreJob;
use Illuminate\Support\Facades\DB;

class RumDashboardController extends Controller
{
    public function index()
    {
        $store = Store::first(); // Currently single-tenant setup

        if (!$store) {
            return redirect()->route('settings.connection')->with('error', 'Please configure your project first.');
        }

        // Aggregate Metrics per URL
        $metrics = PageMetrics::where('store_id', $store->id)
            ->select(
                'url_path',
                DB::raw('COUNT(*) as total_visits'),
                DB::raw('ROUND(AVG(load_time_ms)) as avg_load_time'),
                DB::raw('ROUND(AVG(js_time_ms)) as avg_js_time'),
                // For Grade, we simply grab the latest one for the view in this basic query
                DB::raw('MAX(created_at) as last_visit')
            )
            ->groupBy('url_path')
            ->orderByDesc('total_visits')
            ->get();

        // Get the most recent grades for each URL
        $latestGrades = PageMetrics::where('store_id', $store->id)
            ->whereNotNull('grade')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('url_path')
            ->pluck('grade', 'url_path');

        // Extract and aggregate all CTAs per URL
        // In a very large app this needs a background job, but we'll parse it here for real-time viewing
        $rawCtas = PageMetrics::where('store_id', $store->id)
            ->whereNotNull('cta_clicks')
            ->select('url_path', 'cta_clicks')
            ->get();

        $ctaBreakdown = [];
        $totalCtaClicks = 0;

        foreach ($rawCtas as $log) {
            if (!$log->cta_clicks || !is_array($log->cta_clicks))
                continue;

            $url = $log->url_path;
            if (!isset($ctaBreakdown[$url])) {
                $ctaBreakdown[$url] = [];
            }

            foreach ($log->cta_clicks as $click) {
                $text = $click['text'] ?? 'Unknown';
                // Use the text as the key to group same buttons
                $key = md5($text . ($click['classes'] ?? ''));

                if (!isset($ctaBreakdown[$url][$key])) {
                    $ctaBreakdown[$url][$key] = [
                        'text' => $text,
                        'tag' => $click['tag'] ?? 'button',
                        'classes' => $click['classes'] ?? '',
                        'clicks' => 0
                    ];
                }
                $ctaBreakdown[$url][$key]['clicks']++;
                $totalCtaClicks++;
            }
        }

        // Get Crawled Pages from our new crawler
        $crawledPages = CrawledPage::with('ctas')->where('store_id', $store->id)->get();

        return view('rum.index', compact('store', 'metrics', 'latestGrades', 'ctaBreakdown', 'totalCtaClicks', 'crawledPages'));
    }

    public function crawl()
    {
        $store = Store::first();

        if (!$store || empty($store->domain)) {
            return redirect()->back()->with('error', 'Store missing or URL not set. Please configure connection settings.');
        }

        CrawlStoreJob::dispatch($store);

        return redirect()->back()->with('success', 'Web crawler job dispatched successfully. It will map out pages and discover CTAs in the background.');
    }
}
