<?php

namespace App\Services;

use Exception;

class AiService
{
    protected $provider;
    protected $model;
    protected $apiKey;

    public function __construct(string $provider = 'openai', string $model = 'gpt-4')
    {
        $this->provider = $provider;
        $this->model = $model;
        $this->apiKey = $this->getApiKey($provider);
    }

    /**
     * Get API key for the specified provider
     */
    protected function getApiKey(string $provider): ?string
    {
        return match($provider) {
            'openai' => env('OPENAI_API_KEY'),
            'anthropic' => env('ANTHROPIC_API_KEY'),
            'google' => env('GOOGLE_AI_API_KEY'),
            default => null,
        };
    }

    /**
     * Generate AI response based on provider
     */
    public function generateResponse(string $prompt, array $context = []): array
    {
        if (empty($this->apiKey)) {
            throw new Exception("API key not configured for provider: {$this->provider}");
        }

        return match($this->provider) {
            'openai' => $this->callOpenAI($prompt, $context),
            'anthropic' => $this->callAnthropic($prompt, $context),
            'google' => $this->callGoogle($prompt, $context),
            default => throw new Exception("Unsupported AI provider: {$this->provider}"),
        };
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI(string $prompt, array $context = []): array
    {
        $messages = [
            ['role' => 'system', 'content' => $prompt],
        ];

        if (!empty($context)) {
            foreach ($context as $msg) {
                $messages[] = $msg;
            }
        }

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
        ]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("OpenAI API error: HTTP {$httpCode}");
        }

        $data = json_decode($response, true);

        return [
            'success' => true,
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'usage' => $data['usage'] ?? [],
            'model' => $this->model,
        ];
    }

    /**
     * Call Anthropic (Claude) API
     */
    protected function callAnthropic(string $prompt, array $context = []): array
    {
        $messages = [];

        if (!empty($context)) {
            $messages = $context;
        } else {
            $messages[] = ['role' => 'user', 'content' => $prompt];
        }

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => $this->model,
            'max_tokens' => 1024,
            'messages' => $messages,
            'system' => $prompt,
        ]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Anthropic API error: HTTP {$httpCode}");
        }

        $data = json_decode($response, true);

        return [
            'success' => true,
            'content' => $data['content'][0]['text'] ?? '',
            'usage' => $data['usage'] ?? [],
            'model' => $this->model,
        ];
    }

    /**
     * Call Google AI API
     */
    protected function callGoogle(string $prompt, array $context = []): array
    {
        $contents = [];
        
        if (!empty($context)) {
            foreach ($context as $msg) {
                $contents[] = [
                    'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [['text' => $msg['content']]],
                ];
            }
        } else {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]],
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
            ],
        ]));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Google AI API error: HTTP {$httpCode}");
        }

        $data = json_decode($response, true);

        return [
            'success' => true,
            'content' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'usage' => [],
            'model' => $this->model,
        ];
    }

    /**
     * Extract structured data from conversation
     */
    public function extractData(string $conversation, array $fields): array
    {
        $prompt = "Extract the following information from the conversation:\n";
        foreach ($fields as $field) {
            $prompt .= "- {$field}\n";
        }
        $prompt .= "\nConversation:\n{$conversation}\n\nReturn the data as JSON.";

        $response = $this->generateResponse($prompt);
        
        if ($response['success']) {
            try {
                return json_decode($response['content'], true) ?? [];
            } catch (Exception $e) {
                return [];
            }
        }

        return [];
    }
}
