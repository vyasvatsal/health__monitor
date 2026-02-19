<?php

namespace App\Services\Benchmarking;

use App\Models\Competitor;
use App\Models\Store;
use App\Models\BenchmarkResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BenchmarkRunner
{
    public function run(Store $store, Competitor $competitor): BenchmarkResult
    {
        // 1. Audit MY Store (if domain is set, otherwise skip or error)
        // For MVP we assume store->domain is the project URL, or we use a testing URL
        $myUrl = $store->domain;
        if (!$myUrl) {
            // If no domain set, we can't really benchmark "me". 
            // For now, let's just return a partial result or error.
            // But let's assume the user entered a domain.
            $myUrl = 'http://localhost:8000'; // Default for local testing
        }

        // Ensure protocol
        if (!Str::startsWith($myUrl, ['http://', 'https://'])) {
            $myUrl = 'https://' . $myUrl;
        }

        $myStats = $this->ping($myUrl);
        $compStats = $this->ping($competitor->url);

        // Determine winner
        // Simple logic: Winner is who has lower TTFB
        $winner = 'tie';
        if ($myStats['ttfb'] < $compStats['ttfb']) {
            $winner = 'me';
        } elseif ($compStats['ttfb'] < $myStats['ttfb']) {
            $winner = 'them';
        }

        // Save Result
        $result = BenchmarkResult::create([
            'competitor_id' => $competitor->id,
            'my_ttfb_ms' => $myStats['ttfb'],
            'competitor_ttfb_ms' => $compStats['ttfb'],
            'my_size_kb' => $myStats['size'],
            'competitor_size_kb' => $compStats['size'],
            'winner' => $winner,
            'details' => [
                'my_assets' => $myStats['assets'],
                'comp_assets' => $compStats['assets']
            ],
        ]);

        $competitor->update(['last_audit_at' => now()]);

        return $result;
    }

    private function ping(string $url): array
    {
        try {
            $start = microtime(true);
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])
                ->timeout(10)
                ->get($url);

            $duration = (microtime(true) - $start) * 1000; // ms

            $body = $response->body();
            $sizeKb = round(strlen($body) / 1024);

            // Asset Analysis (Simple Regex Count)
            $scriptCount = preg_match_all('/<script/i', $body);
            $imgCount = preg_match_all('/<img/i', $body);

            // SEO Checks
            $hasTitle = preg_match('/<title[^>]*>(.*?)<\/title>/is', $body);
            $hasDesc = preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\'][^>]*>/i', $body);
            $h1Count = preg_match_all('/<h1/i', $body);

            // Accessibility Checks
            $imagesWithAlt = preg_match_all('/<img[^>]+alt=["\'][^"\']+["\'][^>]*>/i', $body);
            $allyScore = $imgCount > 0 ? round(($imagesWithAlt / $imgCount) * 100) : 100;

            return [
                'ttfb' => round($duration),
                'size' => $sizeKb,
                'assets' => [
                    'scripts' => $scriptCount,
                    'images' => $imgCount,
                    'seo' => [
                        'title' => (bool) $hasTitle,
                        'description' => (bool) $hasDesc,
                        'h1' => $h1Count
                    ],
                    'ally' => [
                        'score' => $allyScore,
                        'alt_tags' => $imagesWithAlt
                    ]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'ttfb' => 9999, // penalty
                'size' => 0,
                'assets' => [
                    'scripts' => 0,
                    'images' => 0,
                    'seo' => ['title' => false, 'description' => false, 'h1' => 0],
                    'ally' => ['score' => 0, 'alt_tags' => 0]
                ]
            ];
        }
    }
}
