<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Entity;

use App\Auth\Domain\AccountIdentifier;
use App\Auth\Domain\AccountRoles;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LocalUserRepository::class)]
class LocalUser
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 10)]
    private string $identifierType;

    #[ORM\Column(length: 255)]
    private string $identifierValue;

    #[ORM\Column(type: Types::TEXT)]
    private string $jwt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    public function __construct(AccountIdentifier $identifier, Jwt $jwt)
    {
        $this->id = Uuid::v4();
        $this->identifierType = $identifier->type;
        $this->identifierValue = $identifier->value;
        $this->jwt = $jwt->toString();
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
        return new Jwt($this->jwt);
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function updateRoles(AccountRoles $roles): void
    {
        $this->roles = $roles->toArray();
    }

    public function getRoles(): AccountRoles
    {
        return new AccountRoles(...$this->roles);
    }
}
