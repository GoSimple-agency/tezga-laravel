<?php

declare(strict_types=1);

namespace GoSimple\TezgaLaravel\Facades;

use GoSimple\Tezga\TezgaClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array issueReceipt(array $body)
 * @method static array refund(array $body)
 * @method static array copyReceipt(array $body, ?string $idempotencyKey = null)
 * @method static array openAdvance(array $body)
 * @method static array closeAdvance(array $body)
 * @method static array createCosting(array $body)
 * @method static array createPriceChange(array $body)
 * @method static array upsertProducts(array $stavke)
 * @method static array upsertCustomers(array $kupci)
 * @method static array getDocumentByExternalId(string $externalId)
 * @method static array getDocument(string $id)
 * @method static array getTaxLabels()
 * @method static array getTransactions(array $query = [])
 * @method static string getEnvironment()
 *
 * @see \GoSimple\Tezga\TezgaClient
 */
final class Tezga extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TezgaClient::class;
    }
}
