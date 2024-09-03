<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final readonly class AuthenticatedUser implements UserInterface
{
    public function __construct(private Uuid $id, private string $identifierType, private string $identifierValue) {}

    public static function createFromLocalUser(LocalUser $localUser): self
    {
        return new self(
            $localUser->getId(),
            $localUser->getIdentifierType(),
            $localUser->getIdentifierValue()
        );
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
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
}
