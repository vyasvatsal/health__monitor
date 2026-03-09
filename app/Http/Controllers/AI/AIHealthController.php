<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Services\AI\Context\HealthContextBuilder;

class AIHealthController extends Controller
{
    protected $contextBuilder;

    public function __construct(HealthContextBuilder $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'store_data' => 'required|array',
        ]);

        $data = $request->input('store_data');

        // 1. Build System Context (Real-time Errors/Incidents)
        $systemContext = $this->contextBuilder->build();

        $storeName = $data['store_name'] ?? 'Target Store';
        $storeContext = "Metrics for {$storeName}:\n";
        $storeContext .= "- Score: " . ($data['score'] ?? 'N/A') . "/100\n";
        $storeContext .= "- Performance: " . ($data['performance_score'] ?? 'N/A') . "%\n";
        $storeContext .= "- UX: " . ($data['ux_score'] ?? 'N/A') . "%\n";
        $storeContext .= "- Trust: " . ($data['trust_score'] ?? 'N/A') . "%\n";
        $storeContext .= "- SEO: " . ($data['seo_score'] ?? 'N/A') . "%\n";

        if (isset($data['server_health'])) {
            $sh = $data['server_health'];
            $storeContext .= "- Server CPU Load: " . ($sh['cpu_load'] ?? 'N/A') . "\n";
            $storeContext .= "- Server Memory: " . ($sh['memory_usage_mb'] ?? 'N/A') . " MB\n";
            $storeContext .= "- DB Connection: " . (($sh['db_connected'] ?? false) === 'true' || $sh['db_connected'] === true ? 'Connected' : 'Disconnected') . "\n";
        }

        if (!empty($data['issues'])) {
            $storeContext .= "- Key Issues: " . implode(', ', $data['issues']) . "\n";
        }

        if (!empty($data['recent_alerts'])) {
            $storeContext .= "- Recent Alerts: " . implode(', ', $data['recent_alerts']) . "\n";
        }

        $userPrompt = "SYSTEM STATUS:\n{$systemContext}\n\nSTORE STATUS:\n{$storeContext}";

        $systemPrompt = "You are an elite E-commerce Health Consultant. 
        Analyze the provided metrics (System Status + Store Status) and return a **concise, executive summary** (max 3-4 sentences) + 3 bullet points of actionable advice in Markdown format.
        
        CRITICAL: If 'System Status' shows critical errors/incidents, YOU MUST MENTION THEM as they likely impact the store's performance.
        Focus on REVENUE IMPACT.";

        try {
            // Use the new AI Infrastructure
            $response = app('ai')->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ]);

            $responseContent = $response['content'];

            return response()->json([
                'status' => 'success',
                'analysis' => $responseContent
            ]);

        } catch (\Throwable $e) {
            Log::error("Dashboard AI Analysis Failed: " . $e->getMessage());
            return response()->json([
                'error' => 'AI Service Unavailable: ' . $e->getMessage()
            ], 500);
        }
    }

    public function analyzeTrend(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $storeId = $request->input('store_id');
        $store = \App\Models\Store::find($storeId);

        // Fetch last 2 daily snapshots
        $snapshots = \App\Models\HealthScore::where('store_id', $storeId)
            ->where('is_daily_snapshot', true)
            ->orderBy('recorded_at', 'desc')
            ->limit(2)
            ->get();

        if ($snapshots->count() < 2) {
            return response()->json([
                'status' => 'info',
                'analysis' => 'Not enough historical data to analyze trends yet. Come back tomorrow!'
            ]);
        }

        $current = $snapshots[0];
        $previous = $snapshots[1];
        $diff = $current->score - $previous->score;

        $prompt = "Analyze the change in E-commerce Store Health Score for '{$store->name}':\n";
        $prompt .= "- Previous Score: {$previous->score} ({$previous->recorded_at->format('M d')})\n";
        $prompt .= "- Current Score: {$current->score} ({$current->recorded_at->format('M d')})\n";
        $prompt .= "- Change: " . ($diff > 0 ? '+' : '') . "{$diff} points\n\n";
        $prompt .= "Previous Metrics: " . json_encode($previous->metrics_json) . "\n";
        $prompt .= "Current Metrics: " . json_encode($current->metrics_json) . "\n\n";
        $prompt .= "Explain WHY the score changed and what the owner should do. Be concise.";

        try {
            $response = app('ai')->chat([
                ['role' => 'system', 'content' => 'You are a Senior Site Reliability Engineer and Business Analyst.'],
                ['role' => 'user', 'content' => $prompt],
            ]);

            return response()->json([
                'status' => 'success',
                'analysis' => $response['content']
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
