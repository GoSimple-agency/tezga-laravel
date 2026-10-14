<?php

declare(strict_types=1);

namespace GoSimple\TezgaLaravel;

use GoSimple\Tezga\TezgaClient;
use GoSimple\TezgaLaravel\Http\WebhookController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class TezgaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tezga.php', 'tezga');

        // One shared client, built from config. Resolved by the Tezga facade.
        $this->app->singleton(TezgaClient::class, function ($app): TezgaClient {
            $c = $app['config']['tezga'];

            return new TezgaClient(
                (string) ($c['api_key'] ?? ''),
                (string) ($c['base_url'] ?? TezgaClient::DEFAULT_BASE_URL),
                (string) ($c['provider'] ?? 'custom'),
                (int) ($c['timeout'] ?? 30),
            );
        });
        $this->app->alias(TezgaClient::class, 'tezga');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/tezga.php' => $this->app->configPath('tezga.php'),
        ], 'tezga-config');

        // Register the webhook route only when a secret is configured, so a project that
        // does not use webhooks never exposes an unprotected endpoint.
        $secret = (string) ($this->app['config']['tezga']['webhook']['secret'] ?? '');
        $path = (string) ($this->app['config']['tezga']['webhook']['path'] ?? 'tezga/webhook');

        if ($secret !== '') {
            // Plain route (no `web` group), so Laravel's CSRF does not apply to this POST.
            Route::post($path, WebhookController::class)->name('tezga.webhook');
        }
    }
}
