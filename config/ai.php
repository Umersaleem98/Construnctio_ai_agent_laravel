<?php

return [
    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    // 'openai_api_key' => env('OPENAI_API_KEY',''),
    'openai_model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
    'fallback_order' => env('AI_FALLBACK_ORDER', 'gemini_first'),
];