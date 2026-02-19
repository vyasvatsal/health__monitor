<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Services\AI\Context\HealthContextBuilder;

class AIChatController extends Controller
{
    protected $contextBuilder;

    public function __construct(HealthContextBuilder $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
    }

    public function index()
    {
        return view('tools.ai_chat');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $userMessage = $request->input('message');

            // 1. Build Real-time Context
            $systemContext = $this->contextBuilder->build();

            // 2. Define Persona
            $systemPrompt = "You are a Senior Site Reliability Engineer (SRE) for this Laravel Health Monitor application.
            Your goal is to help the user debug issues, explain errors, and monitor system health.
            
            OFFICIAL SYSTEM CONTEXT (REAL-TIME):
            {$systemContext}
            
            GUIDELINES:
            - If the user asks about errors, refer SPECIFICALLY to the 'Recent Critical Logs' above.
            - If there are no errors, explicitly state that the system is healthy.
            - Be concise, professional, and solution-oriented.
            - Do not hallucinate errors if none are listed in the context.";

            // 3. Send to AI
            $response = app('ai')->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ]);

            return response()->json([
                'status' => 'success',
                'reply' => $response['content']
            ]);

        } catch (\Throwable $e) {
            Log::error("AI Chat Failed: " . $e->getMessage());
            return response()->json([
                'error' => 'AI Service Unavailable: ' . $e->getMessage()
            ], 500);
        }
    }
}
