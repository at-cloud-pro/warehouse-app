<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

interface AccountManagerUrlProviderInterface
{
    public function getAccountManagerUrl(): string;
}
