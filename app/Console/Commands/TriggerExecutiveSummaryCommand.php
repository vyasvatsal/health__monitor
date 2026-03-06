<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Services\AI\DailyExecutiveSummaryService;

class TriggerExecutiveSummaryCommand extends Command
{
    protected $signature = 'ai:generate-summary {store_id?}';
    protected $description = 'Trigger the AI Executive Summary for a store';

    public function handle()
    {
        $storeId = $this->argument('store_id');
        $stores = $storeId ? Store::where('id', $storeId)->get() : Store::all();

        $service = new DailyExecutiveSummaryService();

        foreach ($stores as $store) {
            /** @var Store $store */
            $this->info("Generating summary for {$store->name}...");
            $summary = $service->generate($store);

            if ($summary) {
                $this->success("Summary generated for {$store->name} for date {$summary->summary_date}");
            } else {
                $this->error("Failed to generate summary for {$store->name}");
            }
        }
    }
}
