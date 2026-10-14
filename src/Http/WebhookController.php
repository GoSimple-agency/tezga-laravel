<?php

declare(strict_types=1);

namespace GoSimple\TezgaLaravel\Http;

use GoSimple\Tezga\TezgaException;
use GoSimple\Tezga\Webhooks;
use GoSimple\TezgaLaravel\Events\TezgaWebhookReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Receives Tezga webhook deliveries, verifies the X-Tezga-Signature against the RAW body,
 * and fires TezgaWebhookReceived. An invalid signature is rejected with 401 and nothing is
 * dispatched, so an unverified payload never reaches application code.
 */
final class WebhookController
{
    public function __invoke(Request $request): Response
    {
        $secret = (string) config('tezga.webhook.secret', '');

        try {
            // Verify against the raw request body, never a re-encoded array.
            $payload = Webhooks::parse(
                $request->getContent(),
                $request->header('X-Tezga-Signature'),
                $secret,
            );
        } catch (TezgaException $e) {
            return new Response('Invalid signature', 401);
        }

        event(new TezgaWebhookReceived($payload, $request->header('X-Tezga-Delivery')));

        return new Response('', 200);
    }
}
