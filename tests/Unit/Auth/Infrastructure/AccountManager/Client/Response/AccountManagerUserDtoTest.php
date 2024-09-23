<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\AccountManager\Client\Response;

use App\Auth\Domain\AccountRoles;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerIdentifierDto;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerUserDto;
use PHPUnit\Framework\TestCase;

final class AccountManagerUserDtoTest extends TestCase
{
    public function testConstructorAssignsValues(): void
    {
        $name = 'Test User';
        $identifier = new AccountManagerIdentifierDto('email', 'test@example.com');
        $roles = new AccountRoles('ROLE_ADMIN', 'ROLE_USER');

        $userDto = new AccountManagerUserDto($name, $identifier, $roles);

        self::assertSame($name, $userDto->name);
        self::assertSame($identifier, $userDto->identifier);
        self::assertSame($roles, $userDto->roles);
    }

    public function testGetAccountManagerIdentifier(): void
    {
        $name = 'Test User';
        $identifier = new AccountManagerIdentifierDto('email', 'test@example.com');
        $roles = new AccountRoles('ROLE_ADMIN', 'ROLE_USER');

        $userDto = new AccountManagerUserDto($name, $identifier, $roles);
        $accountIdentifier = $userDto->getAccountManagerIdentifier();

        self::assertSame('email', $accountIdentifier->type);
        self::assertSame('test@example.com', $accountIdentifier->value);
    }
}
