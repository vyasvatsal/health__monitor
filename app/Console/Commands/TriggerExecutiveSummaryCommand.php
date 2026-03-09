<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Services\AI\DailyExecutiveSummaryService;

class TriggerExecutiveSummaryCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'ai:generate-summary {store_id?}';

    /**
     * @var string
     */
    protected $description = 'Generate a daily AI executive summary for a store or all stores';

    /**
     * Execute the console command.
     */
    public function handle(DailyExecutiveSummaryService $service)
    {
        $storeId = $this->argument('store_id');

        if ($storeId) {
            $store = Store::find($storeId);
            if (!$store) {
                $this->error("Store #{$storeId} not found.");
                return 1;
            }
            $this->generateForStore($store, $service);
        } else {
            $stores = Store::all();
            $this->info("Generating summaries for " . $stores->count() . " stores...");
            foreach ($stores as $store) {
                $this->generateForStore($store, $service);
            }
        }

        $this->info('Executive summary generation complete.');
        return 0;
    }

    protected function generateForStore(Store $store, DailyExecutiveSummaryService $service)
    {
        $this->info("Generating for {$store->name}...");
        $summary = $service->generate($store);

        if ($summary) {
            $this->info("Created Summary #{$summary->id}");
        } else {
            $this->warn("Failed to generate summary for {$store->name}");
        }
    }
}
