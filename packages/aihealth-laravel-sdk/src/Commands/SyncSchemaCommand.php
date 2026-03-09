<?php

namespace AIHealth\Laravel\Commands;

use Illuminate\Console\Command;
use AIHealth\Laravel\Facades\AIHealth;

class SyncSchemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aihealth:sync-schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture and sync the database schema to the Health Monitor';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Capturing database schema...');

        try {
            app('aihealth')->captureDatabaseSchema();
            $this->info('Database schema sync payload dispatched.');
            return 0;
        } catch (\Throwable $e) {
            $this->error('Failed to capture schema: ' . $e->getMessage());
            return 1;
        }
    }
}
