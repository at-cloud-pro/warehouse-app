<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\Doctrine\Entity;

use App\Auth\Domain\AccountIdentifier;
use App\Auth\Domain\AccountRoles;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use PHPUnit\Framework\TestCase;

final class LocalUserTest extends TestCase
{
    public function testConstructorInitializesCorrectly(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');

        $localUser = new LocalUser($identifier, $jwt);

        self::assertSame('email', $localUser->getIdentifierType());
        self::assertSame('test@example.com', $localUser->getIdentifierValue());
        self::assertSame('valid.jwt.token', $localUser->getJwt()->toString());
        self::assertNull($localUser->getName());
        self::assertSame([], $localUser->getRoles()->toArray());
    }

    public function testUpdateName(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');

        $localUser = new LocalUser($identifier, $jwt);
        $localUser->updateName('New Name');

        self::assertSame('New Name', $localUser->getName());
    }

    public function testUpdateRoles(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $roles = new AccountRoles('ROLE_ADMIN', 'ROLE_USER');

        $localUser = new LocalUser($identifier, $jwt);
        $localUser->updateRoles($roles);

        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $localUser->getRoles()->toArray());
    }

    public function testGetJwtReturnsValidJwt(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');

        $localUser = new LocalUser($identifier, $jwt);

        self::assertSame($jwt->toString(), $localUser->getJwt()->toString());
    }

    public function testGetIdentifierType(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');

        $localUser = new LocalUser($identifier, $jwt);

        self::assertSame('email', $localUser->getIdentifierType());
    }

    public function testGetIdentifierValue(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');

        $localUser = new LocalUser($identifier, $jwt);

        self::assertSame('test@example.com', $localUser->getIdentifierValue());
    }
}
