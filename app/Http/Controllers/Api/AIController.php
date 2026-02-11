<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AI\GeminiService;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'store_data' => 'required|array',
        ]);

        try {
            $analysis = $this->gemini->analyzeHealth($request->store_data);
            return response()->json(['analysis' => $analysis]);
        } catch (\Exception $e) {
            Log::error('AI Analysis Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate analysis.'], 500);
        }
    }
}
