<?php

declare(strict_types=1);

use GoSimple\Tezga\TezgaClient;

return [
    // Your Tezga API key (Settings → Integrations), shape tzg_ + 48 hex.
    'api_key' => env('TEZGA_API_KEY', ''),

    // Base URL. Change only for internal testing.
    'base_url' => env('TEZGA_BASE_URL', TezgaClient::DEFAULT_BASE_URL),

    // Integration tag sent as `provider`.
    'provider' => env('TEZGA_PROVIDER', 'custom'),

    // Request timeout in seconds.
    'timeout' => (int) env('TEZGA_TIMEOUT', 30),

    'webhook' => [
        // Webhook secret from Settings → Integrations. Leave empty to disable the route.
        'secret' => env('TEZGA_WEBHOOK_SECRET', ''),

        // Path the webhook route is registered at (no leading slash).
        'path' => env('TEZGA_WEBHOOK_PATH', 'tezga/webhook'),
    ],
];
