<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager;

final readonly class AccountManagerUser
{
    public function __construct(public string $type, public string $value) {}
}
