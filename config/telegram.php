<?php

return [
    'bots' => [
        'mybot' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'certificate_path' => env('TELEGRAM_CERTIFICATE_PATH'),
            'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
            'commands' => [
                // Commands registered in subsequent phases
            ],
        ],
    ],
    'default' => 'mybot',
    'async_requests' => env('TELEGRAM_ASYNC_REQUESTS', false),
    'http_client_handler' => null,
    'resolve_command_dependencies' => true,
];
