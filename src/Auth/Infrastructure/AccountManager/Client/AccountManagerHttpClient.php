<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager\Client;

use App\Auth\Domain\AccountManagerConfiguration;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerUserDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationExpiredException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class AccountManagerHttpClient implements AccountManagerClientInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private AccountManagerConfiguration $configuration
    ) {}

    public function getUserByToken(Jwt $token): AccountManagerUserDto
    {
        $response = $this->httpClient->request(Request::METHOD_GET, "{$this->configuration->host}/api/security/me", [
            'headers' => [
                'Authorization' => 'Bearer '.$token->toString(),
                'X-Sso-External-Service-Id' => $this->configuration->serviceId,
            ],
        ]);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new AuthenticationExpiredException('You must log in one more time.');
        }

        $json = $response->getContent();

        return $this->serializer->deserialize($json, AccountManagerUserDto::class, 'json');
    }
}
