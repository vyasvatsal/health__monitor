<?php

namespace App\Services\Alerts;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertService
{
    /**
     * Send a critical alert to configured channels.
     *
     * @param string $message
     * @return void
     */
    public function sendCritical(string $message)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        $settings = $user->settings ?? [];

        // 1. Email Alert
        if (!empty($settings['email_critical'])) {
            // For MVP, logging as email simulation or using Mail facade if configured
            Log::error("[CRITICAL EMAIL ALERT] To: {$user->email} - {$message}");
            // Mail::raw($message, function($msg) use ($user) { ... });
        }

        // 2. Slack Alert
        $webhookUrl = env('SLACK_WEBHOOK_URL');
        if ($webhookUrl) {
            $this->sendToSlack($webhookUrl, "🚨 *CRITICAL ALERT*\n" . $message, 'danger');
        }
    }

    /**
     * Send a daily digest.
     *
     * @param array $stats
     * @return void
     */
    public function sendDigest(array $stats)
    {
        $webhookUrl = env('SLACK_WEBHOOK_URL');
        if (!$webhookUrl)
            return;

        $message = "📊 *Daily Health Digest*\n";
        $message .= "• *Avg Latency*: {$stats['avg_latency']}ms\n";
        $message .= "• *Error Rate*: {$stats['error_rate']}%\n";
        $message .= "• *Revenue Loss*: \${$stats['revenue_loss']}\n";
        $message .= "• *Slowest Route*: " . ($stats['slowest_route'] ? $stats['slowest_route']['url'] : 'None');

        $this->sendToSlack($webhookUrl, $message, 'good');
    }

    protected function sendToSlack($url, $text, $color = 'good')
    {
        try {
            Http::post($url, [
                'text' => $text,
                'attachments' => [
                    [
                        'color' => $color,
                        'fields' => [],
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send Slack alert: " . $e->getMessage());
        }
    }
}
