<p align="center">
  <a href="https://tezgafiskal.com"><img alt="Tezga" src="https://raw.githubusercontent.com/GoSimple-agency/.github/main/profile/assets/tezga-znak.svg" width="72"></a>
</p>

# tezga-laravel

[![CI](https://github.com/GoSimple-agency/tezga-laravel/actions/workflows/ci.yml/badge.svg)](https://github.com/GoSimple-agency/tezga-laravel/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/php-%E2%89%A58.2-777BB4)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/laravel-10%20|%2011%20|%2012-FF2D20)](https://laravel.com)

Laravel integration for the **[Tezga Fiscal API](https://tezgafiskal.com/en/fiscalization-api)**:
a configured client, a `Tezga` facade, and a signed webhook route — on top of the official
[`gosimple/tezga`](https://github.com/GoSimple-agency/tezga-sdk-php) SDK.

## Installation

```bash
composer require gosimple/tezga-laravel
```

Configure it from your `.env`:

```dotenv
TEZGA_API_KEY=tzg_your_key
TEZGA_PROVIDER=my-shop
TEZGA_WEBHOOK_SECRET=whsec_your_secret
```

Publish the config file if you want to tune it:

```bash
php artisan vendor:publish --tag=tezga-config
```

## Issuing receipts

The `Tezga` facade is the SDK client, resolved from your config:

```php
use GoSimple\TezgaLaravel\Facades\Tezga;

$doc = Tezga::issueReceipt([
    'externalId' => "ORDER-{$order->id}",
    'nacinPlacanja' => 'Kartica',
    'stavke' => $order->items->map(fn ($i) => [
        'naziv' => $i->name,
        'kolicina' => $i->qty,
        'cenaSaPdv' => $i->price,
    ])->all(),
    'kupac' => ['naziv' => $order->buyer_name, 'email' => $order->buyer_email],
    'posaljiMejl' => true,
]);

$order->update(['pfr' => $doc['fiskalni']['pfrBroj'] ?? null]);
```

Do this in a **queued job** so a slow fiscalization never blocks the checkout response.

## Webhooks

When `TEZGA_WEBHOOK_SECRET` is set, the package registers `POST /tezga/webhook` (path
configurable). It verifies the `X-Tezga-Signature` against the raw body and fires an event;
an invalid signature is rejected with `401` before anything runs.

```php
// app/Providers/EventServiceProvider.php
use GoSimple\TezgaLaravel\Events\TezgaWebhookReceived;

protected $listen = [
    TezgaWebhookReceived::class => [RecordFiscalReceipt::class],
];
```

```php
public function handle(TezgaWebhookReceived $e): void
{
    // Deliveries are retried (1, 5, 15, 60, 360 min) — stay idempotent.
    match ($e->event()) {
        'racun.fiskalizovan' => /* ... */ null,
        'refundacija.izvrsena', 'storno.izvrsen' => /* ... */ null,
        default => null,
    };
}
```

Point the Tezga webhook (Settings → Integrations) at `https://your-app/tezga/webhook`.

## About

A product of **GoSimple**. See [`LICENSE`](./LICENSE) and [`NOTICE`](./NOTICE).
