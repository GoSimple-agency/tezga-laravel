<?php

declare(strict_types=1);

namespace GoSimple\TezgaLaravel\Tests;

use GoSimple\Tezga\TezgaClient;
use GoSimple\TezgaLaravel\Events\TezgaWebhookReceived;
use GoSimple\TezgaLaravel\Facades\Tezga;
use Illuminate\Support\Facades\Event;

final class IntegrationTest extends TestCase
{
    public function test_client_resolves_as_a_singleton_from_config(): void
    {
        $a = $this->app->make(TezgaClient::class);
        $b = $this->app->make('tezga');
        $this->assertInstanceOf(TezgaClient::class, $a);
        $this->assertSame($a, $b);
    }

    public function test_facade_points_at_the_bound_client(): void
    {
        $this->assertInstanceOf(TezgaClient::class, Tezga::getFacadeRoot());
    }

    public function test_valid_webhook_is_accepted_and_dispatches_the_event(): void
    {
        Event::fake([TezgaWebhookReceived::class]);

        $body = json_encode([
            'dogadjaj' => 'racun.fiskalizovan',
            'podaci' => ['externalId' => 'ORDER-1042'],
        ], JSON_UNESCAPED_UNICODE);
        $sig = hash_hmac('sha256', $body, 'whsec_test');

        $res = $this->call('POST', 'tezga/webhook', [], [], [], [
            'HTTP_X_TEZGA_SIGNATURE' => $sig,
            'HTTP_X_TEZGA_DELIVERY' => 'abc-123',
            'CONTENT_TYPE' => 'application/json',
        ], $body);

        $res->assertStatus(200);
        Event::assertDispatched(
            TezgaWebhookReceived::class,
            fn (TezgaWebhookReceived $e) => $e->event() === 'racun.fiskalizovan'
                && $e->payload['podaci']['externalId'] === 'ORDER-1042'
                && $e->deliveryId === 'abc-123',
        );
    }

    public function test_invalid_signature_is_rejected_and_nothing_is_dispatched(): void
    {
        Event::fake([TezgaWebhookReceived::class]);

        $res = $this->call('POST', 'tezga/webhook', [], [], [], [
            'HTTP_X_TEZGA_SIGNATURE' => 'deadbeef',
            'CONTENT_TYPE' => 'application/json',
        ], '{"dogadjaj":"racun.fiskalizovan"}');

        $res->assertStatus(401);
        Event::assertNotDispatched(TezgaWebhookReceived::class);
    }
}
