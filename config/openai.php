<?php

return [
    'api_key' => env('OPENAI_SECRET_KEY', ''),
    'model' => env('OPENAI_MODEL', 'gpt-4o'),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 4000),
    'temperature' => env('OPENAI_TEMPERATURE', 1.0),
];