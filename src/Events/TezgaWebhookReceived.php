<?php

declare(strict_types=1);

namespace GoSimple\TezgaLaravel\Events;

/**
 * Fired after a Tezga webhook delivery has been signature-verified.
 *
 * Listen for this to react to racun.fiskalizovan, racun.greska, refundacija.izvrsena
 * and storno.izvrsen. Deliveries are retried, so make your listener idempotent — key it
 * on `$payload['podaci']['externalId']` or the X-Tezga-Delivery header.
 */
final class TezgaWebhookReceived
{
    /** @param array{dogadjaj?: string, vreme?: string, tenantId?: string, podaci?: array} $payload */
    public function __construct(
        public readonly array $payload,
        public readonly ?string $deliveryId = null,
    ) {
    }

    public function event(): ?string
    {
        return $this->payload['dogadjaj'] ?? null;
    }
}
