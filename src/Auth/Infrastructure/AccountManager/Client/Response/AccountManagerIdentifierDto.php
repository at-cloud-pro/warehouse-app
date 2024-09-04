<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager\Client\Response;

final readonly class AccountManagerIdentifierDto
{
    public function __construct(public string $type, public string $value) {}
}
