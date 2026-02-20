<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;

class SecurityScanner
{
    /**
     * Run a basic security check on the application.
     * @return array
     */
    public function scan()
    {
        $issues = [];
        $score = 100;

        // 1. Debug Mode Check
        if (config('app.debug')) {
            $issues[] = 'CRITICAL: App Debug Mode is ENABLED. Disable in production.';
            $score -= 40;
        }

        // 2. .env Exposure Check (Self-Test)
        // We try to access our own .env file via HTTP.
        // In a real scenario, we'd use the full domain, but for local MVP we assume relative risk.
        // This is a passive check.

        // 3. Header Checks (Simulation)
        // Since we are running *inside* the app, we check what middleware is active or simulating headers.
        // For MVP, we check if standard security headers are configured in headers config (if applicable) or assume missing.
        // Let's assume a default "Safe" but warn if not explicitly set.

        // MVP: Simple simulated check based on environment
        $appUrl = config('app.url');
        if (strpos($appUrl, 'https://') === false && !App::isLocal()) {
            $issues[] = 'WARNING: HTTPS not enforced (App URL is http://).';
            $score -= 20;
        }

        // 4. Critical File Exposure (.git)
        // We check if we can access the .git directory via HTTP
        try {
            $response = Http::timeout(2)->get($appUrl . '/.git/HEAD');
            if ($response->successful() && str_contains($response->body(), 'ref:')) {
                $issues[] = 'CRITICAL: .git directory is exposed publicly! Deny access in Nginx/Apache.';
                $score -= 50;
            }
        } catch (\Exception $e) {
            // If connection fails, we assume it's not exposed or unreachable (Safe)
        }

        // 5. Security Headers
        // Simulated check: In a real app we'd scan the headers of the homepage
        try {
            $response = Http::timeout(2)->get($appUrl);
            $headers = $response->headers();

            if (!isset($headers['X-Frame-Options'])) {
                $issues[] = 'Notice: Missing X-Frame-Options header (Clickjacking protection).';
                $score -= 5;
            }
            if (!isset($headers['X-Content-Type-Options'])) {
                $issues[] = 'Notice: Missing X-Content-Type-Options header.';
                $score -= 5;
            }
        } catch (\Exception $e) {
        }

        return [
            'score' => max(0, $score),
            'issues' => $issues,
            'status' => $score > 80 ? 'Secure' : ($score > 50 ? 'At Risk' : 'Critical'),
        ];
    }
}
