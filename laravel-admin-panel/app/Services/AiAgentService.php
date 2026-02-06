<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\XmlCdr;
use Illuminate\Support\Facades\Log;

class AiAgentService
{
    /**
     * Analyze call with AI
     */
    public function analyzeCall($callData)
    {
        try {
            $prompt = $this->buildCallAnalysisPrompt($callData);
            
            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model', 'gpt-4'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an AI assistant helping analyze call center data and provide insights.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => config('services.openai.max_tokens', 2000),
            ]);

            return [
                'success' => true,
                'analysis' => $response->choices[0]->message->content,
                'usage' => $response->usage->totalTokens ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('AI Analysis Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate call summary
     */
    public function generateCallSummary($cdrRecords)
    {
        $totalCalls = count($cdrRecords);
        $answeredCalls = collect($cdrRecords)->where('hangup_cause', 'NORMAL_CLEARING')->count();
        $missedCalls = $totalCalls - $answeredCalls;
        $totalDuration = collect($cdrRecords)->sum('duration');
        $avgDuration = $totalCalls > 0 ? $totalDuration / $totalCalls : 0;

        $prompt = "Analyze the following call center statistics and provide insights:\n\n" .
                  "Total Calls: {$totalCalls}\n" .
                  "Answered Calls: {$answeredCalls}\n" .
                  "Missed Calls: {$missedCalls}\n" .
                  "Average Call Duration: " . gmdate("H:i:s", $avgDuration) . "\n\n" .
                  "Provide a brief summary with recommendations for improvement.";

        try {
            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model', 'gpt-4'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a call center analytics expert providing actionable insights.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 500,
            ]);

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            Log::error('AI Summary Error: ' . $e->getMessage());
            return 'Unable to generate AI summary at this time.';
        }
    }

    /**
     * AI-powered call routing decision
     */
    public function routingDecision($callContext)
    {
        $mode = config('services.ai_agent.mode', 'hybrid');
        
        if ($mode === 'ai_only') {
            return ['route' => 'ai', 'confidence' => 1.0];
        }
        
        if ($mode === 'human_only') {
            return ['route' => 'human', 'confidence' => 1.0];
        }

        // Hybrid mode - use AI to decide
        try {
            $prompt = "Based on the following call context, should this call be routed to AI (automated) or Human agent?\n\n" .
                      "Caller: {$callContext['caller_id']}\n" .
                      "Destination: {$callContext['destination']}\n" .
                      "Time: {$callContext['time']}\n" .
                      "Previous interactions: {$callContext['history']}\n\n" .
                      "Respond with JSON: {\"route\": \"ai\" or \"human\", \"confidence\": 0-1, \"reason\": \"explanation\"}";

            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model', 'gpt-4'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a call routing expert. Respond only with valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 200,
            ]);

            $result = json_decode($response->choices[0]->message->content, true);
            
            if ($result && isset($result['route'])) {
                return $result;
            }
        } catch (\Exception $e) {
            Log::error('AI Routing Error: ' . $e->getMessage());
        }

        // Default to human if AI fails
        return ['route' => 'human', 'confidence' => 0.5, 'reason' => 'AI routing failed'];
    }

    /**
     * Build call analysis prompt
     */
    private function buildCallAnalysisPrompt($callData)
    {
        return "Analyze this call record:\n\n" .
               "Caller: {$callData['caller_id_name']} ({$callData['caller_id_number']})\n" .
               "Destination: {$callData['destination_number']}\n" .
               "Duration: {$callData['duration']} seconds\n" .
               "Status: {$callData['hangup_cause']}\n" .
               "Direction: {$callData['direction']}\n\n" .
               "Provide insights about this call including call quality, outcome, and any recommendations.";
    }

    /**
     * Get AI conversation response
     */
    public function getConversationResponse($message, $conversationHistory = [])
    {
        try {
            $messages = [
                ['role' => 'system', 'content' => 'You are a helpful AI assistant for a call center. Help users with inquiries about calls, statistics, and routing.'],
            ];

            // Add conversation history
            foreach ($conversationHistory as $msg) {
                $messages[] = $msg;
            }

            // Add current message
            $messages[] = ['role' => 'user', 'content' => $message];

            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model', 'gpt-4'),
                'messages' => $messages,
                'max_tokens' => config('services.openai.max_tokens', 2000),
            ]);

            return [
                'success' => true,
                'message' => $response->choices[0]->message->content,
            ];
        } catch (\Exception $e) {
            Log::error('AI Conversation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
