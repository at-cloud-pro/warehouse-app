<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\AccountManager\Provider;

use App\Auth\Domain\AccountManagerConfiguration;
use App\Auth\Infrastructure\AccountManager\Provider\AccountManagerUrlProvider;
use PHPUnit\Framework\TestCase;

final class AccountManagerUrlProviderTest extends TestCase
{
    public function testCorrectUrlCreation(): void
    {
        $configurationFake = new AccountManagerConfiguration(
            'https://example.com',
            'service-id',
            'service-name'
        );

        $provider = new AccountManagerUrlProvider($configurationFake);
        $url = $provider->getAccountManagerUrl();
        self::assertSame('https://example.com/en/authorize?service=service-name', $url);
    }
}
