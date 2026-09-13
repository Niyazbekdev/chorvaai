<?php

return [
    'gemini_key' => env('GEMINI_API_KEY', ''),
    'model'      => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    'max_tokens' => (int) env('GEMINI_MAX_TOKENS', 2048),
];
