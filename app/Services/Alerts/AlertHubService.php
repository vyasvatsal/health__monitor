<?php

namespace App\Services\Alerts;

use App\Models\Store;
use App\Models\StoreAlert;
use Illuminate\Support\Facades\Log;

class AlertHubService
{
    /**
     * Trigger a new alert for a store with deduplication.
     *
     * @param Store $store
     * @param string $severity
     * @param string $title
     * @param string $message
     * @param array $data
     * @return StoreAlert|null
     */
    public function trigger(Store $store, string $severity, string $title, string $message, array $data = [])
    {
        // 1. Generate a hash for deduplication (fingerprint)
        // Based on title and store_id to prevent "spamming" the same alert type
        $hash = md5($store->id . $severity . $title);

        // 2. Check for recent similar alerts (within the last 30 minutes)
        $existing = $store->alerts()
            ->where('hash', $hash)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        if ($existing) {
            // Update the data/message potentially, but don't create a new one
            $existing->update(['message' => $message, 'data' => array_merge($existing->data ?? [], $data)]);
            return $existing;
        }

        // 3. Create new alert
        $alert = $store->alerts()->create([
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'hash' => $hash,
        ]);

        Log::info("New Alert Triggered [{$severity}]: {$title} for Store #{$store->id}");

        // 4. (Future) Trigger Notifications (Email, Slack, etc.)
        // $store->user->notify(new StoreAlertNotification($alert));

        return $alert;
    }

    /**
     * Mark all alerts as read for a store.
     *
     * @param Store $store
     */
    public function markAllAsRead(Store $store)
    {
        $store->alerts()->unread()->update(['read_at' => now()]);
    }
}
