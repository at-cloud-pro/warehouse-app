<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain;

use App\Auth\Domain\AccountManagerConfiguration;
use PHPUnit\Framework\TestCase;

final class AccountManagerConfigurationTest extends TestCase
{
    public function testConstructorAssignsValues(): void
    {
        $host = 'localhost';
        $serviceId = '12345';
        $serviceName = 'Test Service';

        $config = new AccountManagerConfiguration($host, $serviceId, $serviceName);

        self::assertSame($host, $config->host);
        self::assertSame($serviceId, $config->serviceId);
        self::assertSame($serviceName, $config->serviceName);
    }
}
