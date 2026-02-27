<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RouteSyncController extends Controller
{
    /**
     * Receives a manifest of URLs from the aihealth-laravel-sdk via `php artisan aihealth:sync-routes`
     */
    public function sync(Request $request)
    {
        $apiKey = $request->header('X-Monitor-Key');
        $projectId = $request->header('X-Project-Id');

        if (!$apiKey || !$projectId) {
            return response()->json(['error' => 'Unauthorized. Missing Key or Project ID.'], 401);
        }

        $store = Store::where('id', $projectId)->where('api_key', $apiKey)->first();

        if (!$store) {
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        $routes = $request->input('routes', []);

        if (empty($routes)) {
            return response()->json(['message' => 'No routes provided.'], 200);
        }

        try {
            DB::beginTransaction();

            $insertedCount = 0;
            $updatedCount = 0;

            foreach ($routes as $route) {
                if (!isset($route['uri']))
                    continue;

                // Upsert logic (Update if exists, Insert if new)
                $existing = DB::table('project_routes')
                    ->where('store_id', $store->id)
                    ->where('uri', $route['uri'])
                    ->first();

                if ($existing) {
                    DB::table('project_routes')->where('id', $existing->id)->update([
                        'name' => $route['name'] ?? null,
                        'action' => $route['action'] ?? null,
                        'updated_at' => now()
                    ]);
                    $updatedCount++;
                } else {
                    DB::table('project_routes')->insert([
                        'store_id' => $store->id,
                        'uri' => $route['uri'],
                        'name' => $route['name'] ?? null,
                        'action' => $route['action'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $insertedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced routes.",
                'inserted' => $insertedCount,
                'updated' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RouteSync Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error during sync.'], 500);
        }
    }
}
