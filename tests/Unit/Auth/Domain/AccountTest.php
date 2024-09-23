<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain;

use App\Auth\Domain\Account;
use App\Auth\Domain\AccountIdentifier;
use App\Auth\Domain\Jwt;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    public function testConstructorAssignsValues(): void
    {
        $identifier = new AccountIdentifier('email', 'test@example.com');
        $jwt = new Jwt('sample.jwt.token');

        $account = new Account($identifier, $jwt);

        self::assertSame($identifier, $account->identifier);
        self::assertSame($jwt, $account->jwt);
    }
}
