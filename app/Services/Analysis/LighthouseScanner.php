<?php

namespace App\Services\Analysis;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LighthouseScanner
{
    protected $apiUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    /**
     * Run a Lighthouse scan using Google PageSpeed Insights API.
     * 
     * @param string $url The URL to scan
     * @param string $strategy 'desktop' or 'mobile'
     * @return array|null Null if failed, array of processed data if successful.
     */
    public function scan(string $url, string $strategy = 'desktop'): ?array
    {
        $apiKey = config('services.google.pagespeed_key');

        try {
            // Manually build query string because Google expects repeated category= arguments without array indices
            $queryString = http_build_query([
                'url' => $url,
                'strategy' => $strategy,
                'key' => $apiKey,
            ]);

            $categories = ['performance', 'accessibility', 'best-practices', 'seo'];
            foreach ($categories as $category) {
                $queryString .= '&category=' . $category;
            }

            $response = Http::timeout(120)->get($this->apiUrl . '?' . $queryString);

            if ($response->failed()) {
                Log::error("Lighthouse Scan Failed for {$url}: " . $response->body());
                return null;
            }

            $data = $response->json();
            return $this->processResponse($data);

        } catch (\Exception $e) {
            Log::error("Lighthouse Scanner Exception for {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Process the massive raw Lighthouse JSON into our required format.
     */
    protected function processResponse(array $data): array
    {
        $categories = $data['lighthouseResult']['categories'] ?? [];
        $audits = $data['lighthouseResult']['audits'] ?? [];

        // Scores (0-1 scale to 0-100)
        $scores = [
            'performance' => isset($categories['performance']['score']) ? round($categories['performance']['score'] * 100) : 0,
            'accessibility' => isset($categories['accessibility']['score']) ? round($categories['accessibility']['score'] * 100) : 0,
            'best_practices' => isset($categories['best-practices']['score']) ? round($categories['best-practices']['score'] * 100) : 0,
            'seo' => isset($categories['seo']['score']) ? round($categories['seo']['score'] * 100) : 0,
        ];

        // Core Web Vitals & Metrics
        $webVitals = [
            'fcp' => $this->getAuditMetric($audits, 'first-contentful-paint'),
            'lcp' => $this->getAuditMetric($audits, 'largest-contentful-paint'),
            'tbt' => $this->getAuditMetric($audits, 'total-blocking-time'),
            'cls' => $this->getAuditMetric($audits, 'cumulative-layout-shift'),
            'si' => $this->getAuditMetric($audits, 'speed-index'),
        ];

        // Screenshot (Base64)
        $screenshot = $audits['final-screenshot']['details']['data'] ?? null;

        // Extract raw text content / structure for AI later (if applicable/available in full-page config)
        // Lighthouse doesn't natively send the stripped HTML, but it sends huge DOM node lists in accessibility audits
        // We will extract a summarized list of issues to feed the AI instead of raw HTML
        $aiFeedData = $this->extractOpportunities($audits);

        return [
            'scores' => $scores,
            'web_vitals' => $webVitals,
            'screenshot' => $screenshot,
            'ai_feed' => $aiFeedData,
        ];
    }

    protected function getAuditMetric(array $audits, string $key): array
    {
        if (!isset($audits[$key])) {
            return ['score' => 0, 'displayValue' => 'N/A'];
        }

        return [
            'score' => isset($audits[$key]['score']) ? round($audits[$key]['score'] * 100) : 0,
            'displayValue' => $audits[$key]['displayValue'] ?? 'N/A',
            'numericValue' => $audits[$key]['numericValue'] ?? 0,
        ];
    }

    /**
     * Extract key opportunities/issues to feed to our generic Groq LLM.
     */
    protected function extractOpportunities(array $audits): array
    {
        $opportunities = [];

        foreach ($audits as $audit) {
            // If it's a failed opportunity (e.g. "Eliminate render-blocking resources")
            if (isset($audit['details']['type']) && $audit['details']['type'] === 'opportunity' && ($audit['score'] ?? 1) < 1) {
                // Keep it very concise to save tokens
                $opportunities[] = [
                    'id' => $audit['id'],
                    'title' => $audit['title'],
                    'savings_ms' => $audit['details']['overallSavingsMs'] ?? null,
                ];
            }
        }

        // Also capture SEO / Accessibility failures for UX advice
        foreach (['color-contrast', 'link-name', 'button-name', 'meta-description'] as $key) {
            if (isset($audits[$key]) && ($audits[$key]['score'] ?? 1) === 0) {
                $opportunities[] = [
                    'id' => $audits[$key]['id'],
                    'title' => $audits[$key]['title'],
                    'description' => $audits[$key]['description'] ?? '',
                ];
            }
        }

        return $opportunities;
    }
}
