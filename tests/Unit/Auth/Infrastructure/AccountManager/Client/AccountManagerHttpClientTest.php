<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\AccountManager\Client;

use App\Auth\Domain\AccountManagerConfiguration;
use App\Auth\Domain\AccountRoles;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\AccountManager\Client\AccountManagerHttpClient;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerIdentifierDto;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerUserDto;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AuthenticationExpiredException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class AccountManagerHttpClientTest extends TestCase
{
    /** @var HttpClientInterface&MockObject */
    private HttpClientInterface $httpClient;

    /** @var MockObject&SerializerInterface */
    private SerializerInterface $serializer;

    private AccountManagerHttpClient $accountManagerHttpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $configuration = new AccountManagerConfiguration('http://localhost', 'service-id', 'service-name');

        $this->accountManagerHttpClient = new AccountManagerHttpClient(
            $this->httpClient,
            $this->serializer,
            $configuration
        );
    }

    public function testGetUserByTokenReturnsAccountManagerUserDto(): void
    {
        $jwt = new Jwt('valid.jwt.token');
        $response = $this->createMock(ResponseInterface::class);
        $jsonResponse = '{"identifier": "user-123", "name": "Test User", "roles": ["ROLE_USER"]}';

        $response->method('getStatusCode')->willReturn(200);
        $response->method('getContent')->willReturn($jsonResponse);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://localhost/api/security/me', [
                'headers' => [
                    'Authorization' => 'Bearer valid.jwt.token',
                    'X-Sso-External-Service-Id' => 'service-id',
                ],
            ])
            ->willReturn($response);

        $accountManagerUserDto = new AccountManagerUserDto(
            'user-123',
            new AccountManagerIdentifierDto('email', 'test@example.com'),
            new AccountRoles('ROLE_USER')
        );

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($jsonResponse, AccountManagerUserDto::class, 'json')
            ->willReturn($accountManagerUserDto);

        $result = $this->accountManagerHttpClient->getUserByToken($jwt);

        self::assertEquals(new AccountManagerIdentifierDto('email', 'test@example.com'), $result->identifier);
        self::assertSame('user-123', $result->name);
        self::assertEquals(new AccountRoles('ROLE_USER'), $result->roles);
    }

    public function testGetUserByTokenThrowsAuthenticationExpiredExceptionOnNon200Response(): void
    {
        $jwt = new Jwt('valid.jwt.token');
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(401);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $this->expectException(AuthenticationExpiredException::class);
        $this->expectExceptionMessage('You must log in one more time.');

        $this->accountManagerHttpClient->getUserByToken($jwt);
    }
}
