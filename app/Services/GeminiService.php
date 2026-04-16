<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API key not configured. Please set GEMINI_API_KEY in .env file');
        }
    }

    public function generateContent($prompt, $context = [], $documents = [])
    {
        try {
            $contents = [];
            
            $systemText = "You are Construction AI, a professional assistant for construction, engineering, and building projects. ";
            
            if (!empty($documents)) {
                $systemText .= "Reference these documents: ";
                foreach ($documents as $doc) {
                    $systemText .= $doc['original_name'] . ": " . substr($doc['ocr_text'], 0, 2000) . " ";
                }
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemText]]
            ];
            
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'I understand. I am Construction AI assistant.']]
            ];

            foreach ($context as $msg) {
                $contents[] = [
                    'role' => $msg['role'] === 'model' ? 'model' : 'user',
                    'parts' => [['text' => $msg['content']]]
                ];
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ];

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";

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
                
                if (isset($data['promptFeedback']['blockReason'])) {
                    return 'I cannot answer that due to safety guidelines. Please try a different question about construction.';
                }
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
                
                return 'I received an empty response. Please try again.';
            }

            $error = $response->json();
            Log::error('Gemini API Error', ['error' => $error]);
            
            return "API Error: " . ($error['error']['message'] ?? 'Unknown error');

        } catch (\Exception $e) {
            Log::error('Gemini Exception', ['error' => $e->getMessage()]);
            return 'Error: ' . $e->getMessage();
        }
    }
}