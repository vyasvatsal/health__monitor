<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIHealthController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'store_data' => 'required|array',
        ]);

        $data = $request->input('store_data');

        // Build a concise prompt context
        $promptContext = "Analyze the following Store Health Metrics:\n";
        $promptContext .= "- Score: " . ($data['score'] ?? 'N/A') . "/100\n";
        $promptContext .= "- Performance: " . ($data['performance_score'] ?? 'N/A') . "%\n";
        $promptContext .= "- UX: " . ($data['ux_score'] ?? 'N/A') . "%\n";
        $promptContext .= "- Trust: " . ($data['trust_score'] ?? 'N/A') . "%\n";
        $promptContext .= "- SEO: " . ($data['seo_score'] ?? 'N/A') . "%\n";

        if (!empty($data['issues'])) {
            $promptContext .= "- Key Issues: " . implode(', ', $data['issues']) . "\n";
        }

        if (!empty($data['recent_alerts'])) {
            $promptContext .= "- Recent Alerts: " . implode(', ', $data['recent_alerts']) . "\n";
        }

        $systemPrompt = "You are an elite E-commerce Health Consultant. 
        Analyze the provided metrics and return a **concise, executive summary** (max 3-4 sentences) + 3 bullet points of actionable advice in Markdown format.
        Focus on REVENUE IMPACT.";

        try {
            // Use the new AI Infrastructure
            $response = app('ai')->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $promptContext],
            ]);



            $responseContent = $response['content'];

            return response()->json([
                'status' => 'success',
                'analysis' => $responseContent
            ]);

        } catch (\Exception $e) {
            Log::error("Dashboard AI Analysis Failed: " . $e->getMessage());
            return response()->json([
                'error' => 'AI Service Unavailable: ' . $e->getMessage()
            ], 500);
        }
    }
}
