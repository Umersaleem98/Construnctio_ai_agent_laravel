
<?php
return [
    'api_key' => env('GEMINI_API_KEY', ''),
    'model' => 'gemini-1.5-flash',
    'max_tokens' => 2048,
    'temperature' => 0.7,
];