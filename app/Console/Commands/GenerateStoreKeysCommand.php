<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Store;

class GenerateStoreKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-store-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing API, Public, and Private Tracking keys for existing stores.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stores = Store::all();
        $updated = 0;

        foreach ($stores as $store) {
            $changed = false;

            if (empty($store->api_key)) {
                $store->api_key = 'sk_live_' . bin2hex(random_bytes(16));
                $changed = true;
            }
            if (empty($store->public_key)) {
                $store->public_key = 'pk_live_' . bin2hex(random_bytes(16));
                $changed = true;
            }
            if (empty($store->private_tracking_key)) {
                $store->private_tracking_key = 'rum_' . bin2hex(random_bytes(16));
                $changed = true;
            }

            if ($changed) {
                $store->save();
                $updated++;
                $this->info("Updated keys for Store ID: {$store->id}");
            }
        }

        $this->info("✅ Key generation complete. Updated {$updated} store(s).");
    }
}
