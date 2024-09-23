<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Application\Command;

use App\Auth\Application\Command\SignInUserCommand;
use App\Auth\Domain\Jwt;
use PHPUnit\Framework\TestCase;

final class SignInUserCommandTest extends TestCase
{
    public function testConstructorSetsToken(): void
    {
        $jwtToken = new Jwt('some-token');
        $command = new SignInUserCommand($jwtToken);

        self::assertSame($jwtToken, $command->token);
    }
}
