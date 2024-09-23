<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\AccountManager\Client\Response;

use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerIdentifierDto;
use PHPUnit\Framework\TestCase;

final class AccountManagerIdentifierDtoTest extends TestCase
{
    public function testConstructorAssignsValues(): void
    {
        $type = 'email';
        $value = 'test@example.com';

        $identifierDto = new AccountManagerIdentifierDto($type, $value);

        self::assertSame($type, $identifierDto->type);
        self::assertSame($value, $identifierDto->value);
    }
}
