<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Domain;

use App\Auth\Domain\Jwt;
use PHPUnit\Framework\TestCase;

final class JwtTest extends TestCase
{
    public function testDecodeValidJwt(): void
    {
        /** @var string $headerJson */
        $headerJson = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($headerJson);

        /** @var string $payloadJson */
        $payloadJson = json_encode(['uit' => 'email', 'uiv' => 'test@example.com']);
        $payload = base64_encode($payloadJson);
        $token = "{$header}.{$payload}.signature";

        $jwt = new Jwt($token);
        $decoded = $jwt->decode();

        self::assertSame('email', $decoded['uit']);
        self::assertSame('test@example.com', $decoded['uiv']);
    }

    public function testDecodeInvalidJwtThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JWT');

        $jwt = new Jwt('invalid-token');
        $jwt->decode();
    }

    public function testDecodeInvalidPayloadThrowsException(): void
    {
        /** @var string $headerJson */
        $headerJson = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($headerJson);

        $invalidPayload = 'invalid-payload';
        $token = "{$header}.{$invalidPayload}.signature";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JWT payload');

        $jwt = new Jwt($token);
        $jwt->decode();
    }

    public function testGetUserReturnsAccount(): void
    {
        /** @var string $headerJson */
        $headerJson = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($headerJson);

        /** @var string $payloadJson */
        $payloadJson = json_encode(['uit' => 'email', 'uiv' => 'test@example.com']);
        $payload = base64_encode($payloadJson);

        $token = "{$header}.{$payload}.signature";

        $jwt = new Jwt($token);
        $account = $jwt->getUser();

        self::assertSame('email', $account->identifier->type);
        self::assertSame('test@example.com', $account->identifier->value);
    }

    public function testToStringReturnsJwtValue(): void
    {
        $token = 'sample.jwt.token';
        $jwt = new Jwt($token);

        self::assertSame($token, $jwt->toString());
    }
}
