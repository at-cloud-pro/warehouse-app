<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Symfony\Security;

use App\Auth\Domain\AccountRoles;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final readonly class AuthenticatedUser implements UserInterface
{
    public function __construct(
        private Uuid $id,
        private string $identifierType,
        private string $identifierValue,
        private Jwt $jwt,
        private AccountRoles $roles = new AccountRoles(),
        private ?string $name = null,
    ) {}

    public static function createFromLocalUser(LocalUser $localUser): self
    {
        return new self(
            $localUser->getId(),
            $localUser->getIdentifierType(),
            $localUser->getIdentifierValue(),
            $localUser->getJwt(),
            $localUser->getRoles(),
            $localUser->getName(),
        );
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->identifierValue;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIdentifierType(): string
    {
        return $this->identifierType;
    }

    public function getIdentifierValue(): string
    {
        return $this->identifierValue;
    }

    public function getJwt(): Jwt
    {
        return $this->jwt;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRoles(): array
    {
        return array_merge($this->roles->toArray(), ['ROLE_USER']);
    }
}
