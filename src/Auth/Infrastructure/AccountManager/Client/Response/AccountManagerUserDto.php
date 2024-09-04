<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager\Client\Response;

use App\Auth\Domain\AccountIdentifier;
use App\Auth\Domain\AccountRoles;

final readonly class AccountManagerUserDto
{
    public function __construct(
        public string $name,
        public AccountManagerIdentifierDto $identifier,
        public AccountRoles $roles
    ) {}

    public function getAccountManagerIdentifier(): AccountIdentifier
    {
        return new AccountIdentifier($this->identifier->type, $this->identifier->value);
    }
}
