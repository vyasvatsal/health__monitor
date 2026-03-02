<?php

namespace App\Services;

use App\Models\Store;
use App\Models\CrawledPage;
use App\Models\DiscoveredCta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use Exception;

class StoreCrawlerService
{
    protected string $userAgent = 'AIHealthMonitor/1.0 (+https://aihealth.monitor)';
    protected int $maxUrls = 500; // Limit crawling to avoid overloading small servers

    public function crawlStore(Store $store)
    {
        $baseUrl = $this->normalizeUrl($store->domain);
        if (!$baseUrl) {
            Log::error("StoreCrawler: Cannot crawl missing or invalid URL for store ID {$store->id}");
            return;
        }

        $robotsUrl = rtrim($baseUrl, '/') . '/robots.txt';
        $rules = $this->parseRobots($robotsUrl);

        if ($rules['disallowAll']) {
            Log::info("StoreCrawler: robots.txt disallows all crawling for store ID {$store->id}");
            return;
        }

        $visited = [];
        $queue = [$baseUrl];
        $baseNormalized = preg_replace('/^(https?:\/\/)(www\.)?/', '$1', strtolower($baseUrl));

        while (!empty($queue) && count($visited) < $this->maxUrls) {
            $url = array_shift($queue);
            $urlNorm = $this->normalizeUrl($url);

            if (!$urlNorm || isset($visited[$urlNorm])) {
                continue;
            }

            if (!$this->allowedByRobots($urlNorm, $rules)) {
                $visited[$urlNorm] = false;
                continue;
            }

            // Mark as visiting to prevent recursive enqueue
            $visited[$urlNorm] = true;

            try {
                $response = Http::withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ])->timeout(10)->withOptions(['verify' => false])->get($url);

                $statusCode = $response->status();
                $html = $response->body();

                // Store or update the page
                $pageRecord = CrawledPage::updateOrCreate(
                    ['store_id' => $store->id, 'url' => $urlNorm],
                    [
                        'last_crawled_at' => now(),
                        'status_code' => $statusCode,
                        'title' => $this->extractTitle($html)
                    ]
                );

                if ($statusCode === 200 && mb_strlen($html) > 50) {
                    // Extract new URLs to crawl
                    $links = $this->extractLinks($html, $url);
                    foreach ($links as $link) {
                        $linkNorm = rtrim($this->normalizeUrl($link), '/');
                        $linkCompare = preg_replace('/^(https?:\/\/)(www\.)?/', '$1', strtolower($linkNorm));

                        if (str_starts_with($linkCompare, str_replace('www.', '', $baseNormalized))) {
                            if (!isset($visited[$linkNorm]) && !in_array($link, $queue)) {
                                $queue[] = $link;
                            }
                        }
                    }

                    // Extract CTAs
                    $this->extractCtas($html, $pageRecord);
                }

            } catch (Exception $e) {
                Log::warning("StoreCrawler: Failed to fetch URL $url - " . $e->getMessage());
                CrawledPage::updateOrCreate(
                    ['store_id' => $store->id, 'url' => $urlNorm],
                    [
                        'last_crawled_at' => now(),
                        'status_code' => 0, // Indicate fetch failure
                    ]
                );
            }
        }
    }

    protected function normalizeUrl(string $url): string|false
    {
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return false;
        }

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'];
        $path = $parsed['path'] ?? '/';

        $normalized = strtolower($scheme . '://' . $host . $path);
        if (isset($parsed['query'])) {
            $normalized .= '?' . $parsed['query'];
        }

        return $normalized;
    }

    protected function parseRobots(string $robotsUrl): array
    {
        $rules = ['disallow' => [], 'disallowAll' => false];
        try {
            $response = Http::timeout(5)->withOptions(['verify' => false])->get($robotsUrl);
            if ($response->successful()) {
                $content = $response->body();
                $lines = explode("\n", $content);
                $userAgentMatch = false;

                foreach ($lines as $line) {
                    $line = trim(preg_replace('/#.*/', '', $line));
                    if (stripos($line, 'User-agent:') === 0) {
                        $agent = trim(substr($line, 11));
                        $userAgentMatch = ($agent === '*' || stripos($this->userAgent, $agent) !== false);
                    } elseif ($userAgentMatch && stripos($line, 'Disallow:') === 0) {
                        $path = trim(substr($line, 9));
                        if ($path === '/') {
                            $rules['disallowAll'] = true;
                        } elseif ($path !== '') {
                            $rules['disallow'][] = $path;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore robots fetch failure
        }
        return $rules;
    }

    protected function allowedByRobots(string $url, array $rules): bool
    {
        if ($rules['disallowAll']) {
            return false;
        }
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        foreach ($rules['disallow'] as $pattern) {
            $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '#i';
            if (preg_match($regex, $path)) {
                return false;
            }
        }
        return true;
    }

    protected function urlToAbsolute(string $base, string $rel): string|false
    {
        if (parse_url($rel, PHP_URL_SCHEME) != '')
            return $rel;

        if (str_starts_with($rel, '//')) {
            $scheme = parse_url($base, PHP_URL_SCHEME) ?: 'https';
            return $scheme . ':' . $rel;
        }

        $baseParts = parse_url($base);
        if (!isset($baseParts['scheme'], $baseParts['host']))
            return false;

        $basePath = $baseParts['path'] ?? '/';
        $basePath = preg_replace('#/[^/]*$#', '/', $basePath);
        $path = ($rel[0] === '/') ? $rel : ($basePath . $rel);

        $segments = explode('/', $path);
        $resolved = [];
        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($resolved);
            } elseif ($segment !== '.' && $segment !== '') {
                $resolved[] = $segment;
            }
        }

        return $baseParts['scheme'] . '://' . $baseParts['host'] . '/' . implode('/', $resolved);
    }

    protected function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return trim(strip_tags($matches[1]));
        }
        return null;
    }

    protected function extractLinks(string $html, string $baseUrl): array
    {
        $links = [];
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = trim($a->getAttribute('href'));
            // Skip non-HTTP links and fragments
            if (!$href || str_starts_with($href, 'mailto:') || str_starts_with($href, 'javascript:') || str_starts_with($href, 'tel:') || str_starts_with($href, '#')) {
                continue;
            }

            if (str_contains(strtolower($a->getAttribute('rel')), 'nofollow')) {
                continue;
            }

            $abs = $this->urlToAbsolute($baseUrl, $href);
            if ($abs) {
                $links[] = strtok($abs, '#'); // Remove hash fragments
            }
        }

        return array_unique($links);
    }

    protected function extractCtas(string $html, CrawledPage $page)
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        libxml_clear_errors();

        $ctas = [];

        // Find buttons
        foreach ($dom->getElementsByTagName('button') as $btn) {
            $ctas[] = [
                'tag' => 'button',
                'text' => trim(strip_tags($btn->textContent)) ?: ($btn->getAttribute('aria-label') ?: 'Button'),
                'href' => null,
                'css_classes' => $btn->getAttribute('class') ?: null
            ];
        }

        // Find links
        foreach ($dom->getElementsByTagName('a') as $a) {
            $ctas[] = [
                'tag' => 'a',
                'text' => trim(strip_tags($a->textContent)) ?: ($a->getAttribute('title') ?: ($a->getAttribute('aria-label') ?: 'Link')),
                'href' => $a->getAttribute('href') ?: null,
                'css_classes' => $a->getAttribute('class') ?: null
            ];
        }

        // Find inputs (submit buttons)
        foreach ($dom->getElementsByTagName('input') as $input) {
            $type = strtolower($input->getAttribute('type'));
            if ($type === 'submit' || $type === 'button') {
                $ctas[] = [
                    'tag' => 'input',
                    'text' => $input->getAttribute('value') ?: 'Submit',
                    'href' => null,
                    'css_classes' => $input->getAttribute('class') ?: null
                ];
            }
        }

        // Save valid CTAs
        $page->ctas()->delete(); // Clear old CTAs for this URL

        foreach ($ctas as $ctaData) {
            // Only care about things that have text or reasonable classes, skip invisible structural links
            if (empty($ctaData['text']) || strlen($ctaData['text']) > 100) {
                continue;
            }

            DiscoveredCta::create(array_merge($ctaData, [
                'crawled_page_id' => $page->id
            ]));
        }
    }
}
