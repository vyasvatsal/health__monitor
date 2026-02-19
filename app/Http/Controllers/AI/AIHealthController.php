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

        // 2. Build Store Context
        $storeContext = "Target Store Metrics:\n";
        $storeContext .= "- Score: " . ($data['score'] ?? 'N/A') . "/100\n";
        $storeContext .= "- Performance: " . ($data['performance_score'] ?? 'N/A') . "%\n";
        $storeContext .= "- UX: " . ($data['ux_score'] ?? 'N/A') . "%\n";
        $storeContext .= "- Trust: " . ($data['trust_score'] ?? 'N/A') . "%\n";
        $storeContext .= "- SEO: " . ($data['seo_score'] ?? 'N/A') . "%\n";

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
}
