<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain;

use App\Auth\Domain\AccountIdentifier;
use PHPUnit\Framework\TestCase;

final class AccountIdentifierTest extends TestCase
{
    public function testConstructorAssignsValues(): void
    {
        $type = 'email';
        $value = 'test@example.com';

        $identifier = new AccountIdentifier($type, $value);

        self::assertSame($type, $identifier->type);
        self::assertSame($value, $identifier->value);
    }
}
