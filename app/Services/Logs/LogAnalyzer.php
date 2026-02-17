<?php

namespace App\Services\Logs;

use Illuminate\Support\Facades\File;

class LogAnalyzer
{
    /**
     * Get recent log entries.
     * @param int $lines
     * @return array
     */
    public function getRecentErrors($lines = 50): array
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return [];
        }

        // Read last N lines efficiently? For MVP, reading whole file if small, or using tail logic.
        // PHP approach:
        $content = File::get($logPath);
        $linesArr = explode("\n", $content);
        $recent = array_slice($linesArr, -$lines); // Last 50 lines

        // Filter for "ERROR" or "CRITICAL"
        $errors = array_filter($recent, function ($line) {
            return strpos($line, '.ERROR:') !== false || strpos($line, '.CRITICAL:') !== false;
        });

        // Clean up: extract meaningful message
        $cleaned = array_map(function ($line) {
            // Remove timestamp/env info for brevity in AI prompt
            // Example: [2024-01-01 12:00:00] local.ERROR: Message {"context"}
            $parts = explode(']: ', $line, 2);
            return $parts[1] ?? substr($line, 0, 100);
        }, $errors);

        return array_values($cleaned);
    }
}
