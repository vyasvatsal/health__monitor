<?php

namespace App\Services\Alerts;

use App\Models\Store;
use App\Models\StoreAlert;
use Illuminate\Support\Facades\Log;

class AlertHubService
{
    /**
     * Dispatch an alert for a specific store.
     */
    public function trigger(Store $store, string $severity, string $title, ?string $message = null, array $data = [])
    {
        // 1. Prevent duplicate alerts in a short time (Deduplication)
        $exists = StoreAlert::where('store_id', $store->id)
            ->where('title', $title)
            ->where('severity', $severity)
            ->where('created_at', '>', now()->subMinutes(30))
            ->exists();

        if ($exists) {
            return null;
        }

        // 2. Persist to Database
        $alert = StoreAlert::create([
            'store_id' => $store->id,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        // 3. Log it for system visibility
        Log::channel('alerts')->info("Alert Triggered [{$severity}]: {$title}", [
            'store_id' => $store->id,
            'message' => $message
        ]);

        // 4. (Extension Point) Trigger Email/Slack Notifications
        // $store->user->notify(new StoreAlertNotification($alert));

        return $alert;
    }

    /**
     * Handy helper for critical errors
     */
    public function critical(Store $store, string $title, ?string $message = null, array $data = [])
    {
        return $this->trigger($store, 'critical', $title, $message, $data);
    }

    /**
     * Handy helper for warnings
     */
    public function warning(Store $store, string $title, ?string $message = null, array $data = [])
    {
        return $this->trigger($store, 'warning', $title, $message, $data);
    }
}
