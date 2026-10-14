<?php

declare(strict_types=1);

namespace GoSimple\TezgaLaravel\Tests;

use GoSimple\TezgaLaravel\TezgaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [TezgaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('tezga.webhook.secret', 'whsec_test');
        $app['config']->set('tezga.webhook.path', 'tezga/webhook');
        $app['config']->set('tezga.api_key', 'tzg_test');
        $app['config']->set('tezga.base_url', 'https://pos.narbiz.com');
    }
}
