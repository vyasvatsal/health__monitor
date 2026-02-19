<?php

namespace App\Services\AI\Context;

use App\Models\ErrorLog;
use App\Models\Incident;
use Illuminate\Support\Facades\App;

class HealthContextBuilder
{
    public function build(): string
    {
        $environment = App::environment();
        $laravelVersion = App::version();
        $phpVersion = PHP_VERSION;

        $context = "System Context:\n";
        $context .= "- Environment: {$environment}\n";
        $context .= "- Laravel Version: {$laravelVersion}\n";
        $context .= "- PHP Version: {$phpVersion}\n";

        $context .= $this->getRecentErrors();
        $context .= $this->getRecentIncidents();

        return $context;
    }

    protected function getRecentErrors(): string
    {
        // Fetch last 5 distinct error types to avoid flooding context with duplicates
        $errors = ErrorLog::latest('last_seen_at')
            ->take(5)
            ->get();

        if ($errors->isEmpty()) {
            return "\nRecent Errors: None (System Healthy)\n";
        }

        $output = "\nRecent Critical Logs (Last 5):\n";
        foreach ($errors as $error) {
            $output .= "- [{$error->severity}] {$error->type}: {$error->message} (at {$error->file}:{$error->line})\n";
        }

        return $output;
    }

    protected function getRecentIncidents(): string
    {
        $incidents = Incident::where('status', '!=', 'resolved')
            ->latest()
            ->take(3)
            ->get();

        if ($incidents->isEmpty()) {
            return "\nActive Incidents: None\n";
        }

        $output = "\nActive Incidents:\n";
        foreach ($incidents as $incident) {
            $output .= "- [{$incident->severity}] {$incident->title} ({$incident->status})\n";
        }

        return $output;
    }
}
