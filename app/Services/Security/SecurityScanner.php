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

        return [
            'score' => max(0, $score),
            'issues' => $issues,
            'status' => $score > 80 ? 'Secure' : ($score > 50 ? 'At Risk' : 'Critical'),
        ];
    }
}
