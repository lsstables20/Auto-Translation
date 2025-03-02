<?php

return [
    'default_service' => env('TRANSLATION_SERVICE', 'google'),

    'services' => [
        'google' => [
            'api_key' => env('GOOGLE_TRANSLATE_API_KEY'),
        ],
        'deepl' => [
            'api_key' => env('DEEPL_API_KEY'),
        ],
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
        ],
    ],
];
