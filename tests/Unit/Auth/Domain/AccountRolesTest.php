<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain;

use App\Auth\Domain\AccountRoles;
use PHPUnit\Framework\TestCase;

final class AccountRolesTest extends TestCase
{
    public function testToArrayReturnsRoles(): void
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];

        $accountRoles = new AccountRoles(...$roles);
        $result = $accountRoles->toArray();

        self::assertSame($roles, $result);
    }

    public function testEmptyRoles(): void
    {
        $accountRoles = new AccountRoles();
        $result = $accountRoles->toArray();

        self::assertSame([], $result);
    }
}
