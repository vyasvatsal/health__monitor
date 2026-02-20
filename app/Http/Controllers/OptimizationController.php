<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class OptimizationController extends Controller
{
    public function run()
    {
        try {
            // 1. Run standard Laravel optimizations
            Artisan::call('optimize');
            Artisan::call('view:cache');

            // 2. Run Database Analysis
            $analyzer = new \App\Services\Optimization\DatabaseAnalyzer();
            $dbIssues = $analyzer->analyze();

            $message = 'System optimized (Cache, Views, Config).';
            if (!empty($dbIssues)) {
                $message .= ' Found ' . count($dbIssues) . ' database optimization opportunities.';
            }

            // Log the action
            Log::info('Autonomous optimization triggered by user ' . auth()->id());

            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'db_recommendations' => $dbIssues
                ]);
            }

            return back()->with('success', $message)->with('db_optimizations', $dbIssues);

        } catch (\Exception $e) {
            Log::error('Optimization failed: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json(['error' => 'Optimization failed', 'details' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to run optimizations. Check logs.');
        }
    }
}
