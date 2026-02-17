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
            // Run standard Laravel optimizations
            Artisan::call('optimize');
            Artisan::call('view:cache');

            // Log the action
            Log::info('Autonomous optimization triggered by user ' . auth()->id());

            if (request()->expectsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Optimizations executed successfully.']);
            }

            return back()->with('success', 'Autonomous optimizations (CACHE, VIEW, CONFIG) executed successfully.');
        } catch (\Exception $e) {
            Log::error('Optimization failed: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json(['error' => 'Optimization failed', 'details' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to run optimizations. Check logs.');
        }
    }
}
