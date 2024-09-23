<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\Symfony\Security;

use App\Auth\Domain\AccountIdentifier;
use App\Auth\Domain\AccountRoles;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use App\Auth\Infrastructure\Symfony\Security\AuthenticatedUser;
use PHPUnit\Framework\TestCase;

final class AuthenticatedUserTest extends TestCase
{
    public function testCreateFromLocalUser(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $roles = new AccountRoles('ROLE_ADMIN');
        $name = 'Test User';

        $localUser = new LocalUser($identifier, $jwt);
        $localUser->updateName($name);
        $localUser->updateRoles($roles);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame($localUser->getId(), $authenticatedUser->getId());
        self::assertSame($localUser->getIdentifierType(), $authenticatedUser->getIdentifierType());
        self::assertSame($localUser->getIdentifierValue(), $authenticatedUser->getIdentifierValue());
        self::assertSame($localUser->getJwt()->toString(), $authenticatedUser->getJwt()->toString());
        self::assertSame($localUser->getName(), $authenticatedUser->getName());
        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $authenticatedUser->getRoles());
    }

    public function testGetUserIdentifier(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame('test@example.com', $authenticatedUser->getUserIdentifier());
    }

    public function testGetRoles(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $roles = new AccountRoles('ROLE_ADMIN');
        $localUser = new LocalUser($identifier, $jwt);
        $localUser->updateRoles($roles);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $authenticatedUser->getRoles());
    }

    public function testGetId(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame($localUser->getId(), $authenticatedUser->getId());
    }

    public function testGetIdentifierType(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame('email', $authenticatedUser->getIdentifierType());
    }

    public function testGetIdentifierValue(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame('test@example.com', $authenticatedUser->getIdentifierValue());
    }

    public function testGetJwt(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame($jwt->toString(), $authenticatedUser->getJwt()->toString());
    }

    public function testGetName(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);
        $localUser->updateName('Test User');

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertSame('Test User', $authenticatedUser->getName());
    }

    public function testGetNameReturnsNullWhenNotSet(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('valid.jwt.token');
        $localUser = new LocalUser($identifier, $jwt);

        $authenticatedUser = AuthenticatedUser::createFromLocalUser($localUser);

        self::assertNull($authenticatedUser->getName());
    }
}
