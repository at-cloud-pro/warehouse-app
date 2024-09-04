<?php

declare(strict_types=1);

namespace App\Auth\Domain;

final readonly class AccountRoles
{
    /** @var string[] */
    private array $roles;

    public function __construct(string ...$roles)
    {
        $this->roles = $roles;
    }

    /** @return string[] */
    public function toArray(): array
    {
        return $this->roles;
    }
}
