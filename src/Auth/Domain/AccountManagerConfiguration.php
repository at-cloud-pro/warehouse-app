<?php

declare(strict_types=1);

namespace App\Auth\Domain;

final readonly class AccountManagerConfiguration
{
    public function __construct(
        public string $host,
        public string $serviceId,
        public string $serviceName
    ) {}
}
