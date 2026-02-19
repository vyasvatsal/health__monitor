<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
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

            $response = app('ai')->chat([
                ['role' => 'user', 'content' => $userMessage],
            ]);

            return response()->json([
                'status' => 'success',
                'reply' => $response['content']
            ]);

        } catch (\Exception $e) {
            Log::error("AI Chat Failed: " . $e->getMessage());
            return response()->json([
                'error' => 'AI Service Unavailable: ' . $e->getMessage()
            ], 500);
        }
    }
}
