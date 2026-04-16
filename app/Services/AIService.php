<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $geminiKey;
    protected $openaiKey;
    protected $fallbackOrder;

    public function __construct()
    {
        $this->geminiKey = config('ai.gemini_api_key');
        $this->openaiKey = config('ai.openai_api_key');
        $this->fallbackOrder = config('ai.fallback_order', 'gemini_first');
    }

    /**
     * Generate content with automatic fallback between providers
     */
    public function generateContent($prompt, $context = [], $documents = [])
    {
        $providers = $this->fallbackOrder === 'gemini_first' 
            ? ['gemini', 'openai'] 
            : ['openai', 'gemini'];

        $errors = [];

        foreach ($providers as $provider) {
            try {
                Log::info("Attempting AI provider: {$provider}");
                
                if ($provider === 'gemini') {
                    $result = $this->callGemini($prompt, $context, $documents);
                } else {
                    $result = $this->callOpenAI($prompt, $context, $documents);
                }

                if ($result !== null && !empty($result)) {
                    Log::info("Success with provider: {$provider}", ['response_length' => strlen($result)]);
                    return $result;
                }

            } catch (\Exception $e) {
                $errors[$provider] = $e->getMessage();
                Log::warning("Provider {$provider} failed", ['error' => $e->getMessage()]);
                continue;
            }
        }

        // All providers failed
        Log::error('All AI providers failed', ['errors' => $errors]);
        return "I apologize, but I'm unable to process your request at the moment. Both AI services (Gemini and ChatGPT) are unavailable.\n\nErrors:\n- Gemini: " . ($errors['gemini'] ?? 'Not attempted') . "\n- ChatGPT: " . ($errors['openai'] ?? 'Not attempted') . "\n\nPlease try again later or contact support.";
    }

    /**
     * Call Google Gemini API with multiple model fallback
     */
    private function callGemini($prompt, $context, $documents)
    {
        if (empty($this->geminiKey)) {
            throw new \Exception('Gemini API key not configured');
        }

        $contents = [];
        
        // System instruction
        $systemText = $this->buildSystemPrompt($documents);
        
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemText]]
        ];
        
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'I understand. I am Construction AI assistant ready to help with construction projects.']]
        ];

        // Add conversation context
        foreach ($context as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'model' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]]
            ];
        }

        // Add current prompt
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ];

        // Try multiple Gemini models
        $models = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-1.0-pro',
            'gemini-pro',
            'gemini-1.5-pro'
        ];
        
        $lastError = null;
        
        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->geminiKey}";

                Log::info("Trying Gemini model: {$model}");

                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($url, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                        'topP' => 0.8,
                        'topK' => 40,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_ONLY_HIGH'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_ONLY_HIGH'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_ONLY_HIGH'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_ONLY_HIGH'
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Check for blocked content
                    if (isset($data['promptFeedback']['blockReason'])) {
                        Log::warning("Gemini {$model} content blocked", ['reason' => $data['promptFeedback']['blockReason']]);
                        continue;
                    }

                    // Check for finish reason
                    if (isset($data['candidates'][0]['finishReason']) && $data['candidates'][0]['finishReason'] === 'SAFETY') {
                        Log::warning("Gemini {$model} stopped for safety");
                        continue;
                    }

                    // Extract text response
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        $text = $data['candidates'][0]['content']['parts'][0]['text'];
                        Log::info("Gemini {$model} success", ['text_length' => strlen($text)]);
                        return '[Gemini] ' . $text;
                    }
                } else {
                    $error = $response->json();
                    $lastError = $error['error']['message'] ?? "HTTP {$response->status()}";
                    Log::warning("Gemini {$model} failed", ['error' => $lastError]);
                }

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini {$model} exception", ['error' => $lastError]);
                continue;
            }
        }

        throw new \Exception('All Gemini models failed. Last error: ' . $lastError);
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($prompt, $context, $documents)
    {
        if (empty($this->openaiKey)) {
            throw new \Exception('OpenAI API key not configured');
        }

        // Build messages array
        $messages = [];

        // System message
        $systemText = $this->buildSystemPrompt($documents);
        $messages[] = [
            'role' => 'system',
            'content' => $systemText
        ];

        // Add conversation context
        foreach ($context as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'model' ? 'assistant' : 'user',
                'content' => $msg['content']
            ];
        }

        // Add current prompt
        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];

        $model = config('ai.openai_model', 'gpt-3.5-turbo');

        try {
            Log::info("Calling OpenAI", ['model' => $model, 'messages_count' => count($messages)]);

            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->openaiKey,
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'top_p' => 0.9,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['choices'][0]['message']['content'])) {
                    $text = $data['choices'][0]['message']['content'];
                    Log::info("OpenAI success", ['text_length' => strlen($text)]);
                    return '[ChatGPT] ' . $text;
                }

                throw new \Exception('Empty response from OpenAI');
            }

            $error = $response->json();
            $errorMsg = $error['error']['message'] ?? 'Unknown OpenAI error';
            Log::error('OpenAI API error', ['status' => $response->status(), 'error' => $errorMsg]);
            throw new \Exception($errorMsg);

        } catch (\Exception $e) {
            Log::error('OpenAI exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Build system prompt with documents
     */
    private function buildSystemPrompt($documents)
    {
        $instruction = "You are Construction AI, a professional and knowledgeable assistant specialized in construction, engineering, architecture, and building projects. Provide detailed, accurate, and professional responses. When referencing documents, cite them clearly.";

        if (!empty($documents)) {
            $instruction .= "\n\nREFERENCE DOCUMENTS:\n";
            foreach ($documents as $index => $doc) {
                $instruction .= "\n--- DOCUMENT " . ($index + 1) . ": " . $doc['original_name'] . " ---\n";
                $instruction .= substr($doc['ocr_text'], 0, 4000);
                $instruction .= "\n";
            }
        }

        return $instruction;
    }

    /**
     * Test both providers and return status
     */
    public function testProviders()
    {
        $results = [];
        $geminiWorking = false;
        $openaiWorking = false;

        // Test Gemini
        try {
            $this->callGemini('Say exactly: Test successful', [], []);
            $results['gemini'] = ['status' => 'working', 'error' => null];
            $geminiWorking = true;
        } catch (\Exception $e) {
            $results['gemini'] = ['status' => 'failed', 'error' => $e->getMessage()];
        }

        // Test OpenAI
        try {
            $this->callOpenAI('Say exactly: Test successful', [], []);
            $results['openai'] = ['status' => 'working', 'error' => null];
            $openaiWorking = true;
        } catch (\Exception $e) {
            $results['openai'] = ['status' => 'failed', 'error' => $e->getMessage()];
        }

        $results['summary'] = [
            'gemini_configured' => !empty($this->geminiKey),
            'openai_configured' => !empty($this->openaiKey),
            'gemini_working' => $geminiWorking,
            'openai_working' => $openaiWorking,
            'fallback_order' => $this->fallbackOrder,
            'at_least_one_working' => $geminiWorking || $openaiWorking,
        ];

        return $results;
    }

    /**
     * Get current provider status
     */
    public function getProviderStatus()
    {
        return [
            'gemini' => [
                'configured' => !empty($this->geminiKey),
                'key_preview' => !empty($this->geminiKey) ? substr($this->geminiKey, 0, 10) . '...' : 'not set',
            ],
            'openai' => [
                'configured' => !empty($this->openaiKey),
                'key_preview' => !empty($this->openaiKey) ? substr($this->openaiKey, 0, 10) . '...' : 'not set',
                'model' => config('ai.openai_model', 'gpt-3.5-turbo'),
            ],
            'fallback_order' => $this->fallbackOrder,
        ];
    }
}