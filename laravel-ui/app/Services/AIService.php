<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', env('OPENAI_API_KEY'));
        $this->model = config('services.openai.model', 'gpt-4');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }

    public function generateSMSResponse($inboundMessage, $context = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(30)->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $context['system_prompt'] ?? 'You are a helpful SMS assistant.'],
                    ['role' => 'user', 'content' => $inboundMessage],
                ],
                'max_tokens' => 150,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return ['success' => true, 'response' => $data['choices'][0]['message']['content'] ?? ''];
            }
            return ['success' => false, 'error' => 'API request failed'];
        } catch (\Exception $e) {
            Log::error('AI SMS Response Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function analyzeSentiment($text)
    {
        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->post($this->baseUrl . '/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Analyze sentiment. Reply with one word: positive, negative, or neutral.'],
                        ['role' => 'user', 'content' => $text],
                    ],
                    'max_tokens' => 10,
                ]);

            if ($response->successful()) {
                $sentiment = strtolower(trim($response->json()['choices'][0]['message']['content'] ?? 'neutral'));
                return ['success' => true, 'sentiment' => $sentiment];
            }
            return ['success' => false, 'sentiment' => 'neutral'];
        } catch (\Exception $e) {
            return ['success' => false, 'sentiment' => 'neutral'];
        }
    }

    public function textToSpeech($text, $voice = 'alloy')
    {
        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->post($this->baseUrl . '/audio/speech', ['model' => 'tts-1', 'input' => $text, 'voice' => $voice]);
            if ($response->successful()) {
                return ['success' => true, 'audio_content' => $response->body()];
            }
            return ['success' => false, 'error' => 'TTS failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
