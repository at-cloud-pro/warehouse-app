<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Application\Command\Handler;

use App\Auth\Application\Command\Handler\SignInUserHandler;
use App\Auth\Application\Command\SignInUserCommand;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
use App\Auth\Infrastructure\Symfony\Security\AuthenticatedUser;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class SignInUserHandlerTest extends TestCase
{
    /** @var LocalUserRepository&MockObject */
    private LocalUserRepository $localUserRepository;

    /** @var MockObject&Security */
    private Security $security;

    private SignInUserHandler $signInUserHandler;

    /** @throws Exception */
    protected function setUp(): void
    {
        $this->localUserRepository = $this->createMock(LocalUserRepository::class);
        $this->security = $this->createMock(Security::class);

        $this->signInUserHandler = new SignInUserHandler($this->localUserRepository, $this->security);
    }

    public function testInvokeCreatesNewLocalUserAndLogsIn(): void
    {
        $jwtValue = $this->generateJwtToken(['uit' => 'email', 'uiv' => 'test@example.com']);
        $jwt = new Jwt($jwtValue);
        $account = $jwt->getUser();

        $command = new SignInUserCommand($jwt);

        $this->localUserRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($account->identifier)
            ->willReturn(null);

        $this->localUserRepository
            ->expects($this->once())
            ->method('store')
            ->with(self::isInstanceOf(LocalUser::class));

        $this->security
            ->expects($this->once())
            ->method('login')
            ->with(self::isInstanceOf(AuthenticatedUser::class));

        ($this->signInUserHandler)($command);
    }

    public function testInvokeLogsInExistingLocalUser(): void
    {
        $jwtValue = $this->generateJwtToken(['uit' => 'email', 'uiv' => 'test@example.com']);
        $jwt = new Jwt($jwtValue);
        $account = $jwt->getUser();

        $command = new SignInUserCommand($jwt);

        $localUser = new LocalUser($account->identifier, $jwt);

        $this->localUserRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with($account->identifier)
            ->willReturn($localUser);

        $this->localUserRepository
            ->expects($this->never())
            ->method('store');

        $this->security
            ->expects($this->once())
            ->method('login')
            ->with(self::isInstanceOf(AuthenticatedUser::class));

        ($this->signInUserHandler)($command);
    }

    /** @param array<string, mixed> $payload */
    private function generateJwtToken(array $payload): string
    {
        /** @var string $headersJson */
        $headersJson = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($headersJson);

        /** @var string $payloadJson */
        $payloadJson = json_encode($payload);
        $payload = base64_encode($payloadJson);
        $signature = 'signature';  // we're not verifying the signature in the test

        return "{$header}.{$payload}.{$signature}";
    }
}
