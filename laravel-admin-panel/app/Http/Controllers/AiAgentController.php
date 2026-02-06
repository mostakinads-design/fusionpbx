<?php

namespace App\Http\Controllers;

use App\Services\AiAgentService;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    protected $aiService;

    public function __construct(AiAgentService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI Chat interface
     */
    public function index()
    {
        return view('ai.index');
    }

    /**
     * Handle chat message
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'array',
        ]);

        $response = $this->aiService->getConversationResponse(
            $request->message,
            $request->get('history', [])
        );

        return response()->json($response);
    }

    /**
     * Get routing decision
     */
    public function routingDecision(Request $request)
    {
        $request->validate([
            'caller_id' => 'required|string',
            'destination' => 'required|string',
        ]);

        $callContext = [
            'caller_id' => $request->caller_id,
            'destination' => $request->destination,
            'time' => now()->toDateTimeString(),
            'history' => $request->get('history', 'No previous interactions'),
        ];

        $decision = $this->aiService->routingDecision($callContext);

        return response()->json($decision);
    }

    /**
     * Update AI agent mode
     */
    public function updateMode(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:ai_only,human_only,hybrid',
        ]);

        // This would typically update a database setting or config
        // For now, we'll return success
        return response()->json([
            'success' => true,
            'message' => "AI agent mode set to: {$request->mode}",
            'mode' => $request->mode,
        ]);
    }
}

