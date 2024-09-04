<?php

declare(strict_types=1);

namespace App\Auth\Domain;

final readonly class Account
{
    public function __construct(public AccountIdentifier $identifier, public Jwt $jwt) {}
}
