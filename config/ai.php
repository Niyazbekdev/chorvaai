<?php

return [
    'anthropic_key' => env('ANTHROPIC_API_KEY', ''),
    'model'         => env('ANTHROPIC_MODEL', 'claude-haiku-4-5-20251001'),
    'max_tokens'    => (int) env('ANTHROPIC_MAX_TOKENS', 2048),
];
