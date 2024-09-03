<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager\Provider;

use App\Auth\Domain\Service\AccountManagerUrlProviderInterface;

final readonly class AccountManagerUrlProvider implements AccountManagerUrlProviderInterface
{
    private const string DEFAULT_LOCALE = 'en';

    public function __construct(private string $host, private string $service) {}

    public function getAccountManagerUrl(): string
    {
        $locale = self::DEFAULT_LOCALE;

        return "{$this->host}/{$locale}/authorize?service={$this->service}";
    }
}
