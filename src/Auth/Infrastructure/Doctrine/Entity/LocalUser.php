<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Doctrine\Entity;

use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
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

    public function __construct(string $identifierType, string $identifierValue)
    {
        $this->id = Uuid::v4();
        $this->identifierType = $identifierType;
        $this->identifierValue = $identifierValue;
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
