<?php

declare(strict_types=1);

namespace App\Auth\Domain;

final readonly class AccountIdentifier
{
    public function __construct(public string $type, public string $value) {}
}
