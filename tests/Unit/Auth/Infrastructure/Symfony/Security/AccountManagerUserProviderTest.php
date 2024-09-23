<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Infrastructure\Symfony\Security;

use App\Auth\Domain\AccountIdentifier;
use App\Auth\Domain\AccountRoles;
use App\Auth\Domain\Jwt;
use App\Auth\Infrastructure\AccountManager\Client\AccountManagerClientInterface;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerIdentifierDto;
use App\Auth\Infrastructure\AccountManager\Client\Response\AccountManagerUserDto;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
use App\Auth\Infrastructure\Symfony\Security\AccountManagerUserProvider;
use App\Auth\Infrastructure\Symfony\Security\AuthenticatedUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final class AccountManagerUserProviderTest extends TestCase
{
    /** @var AccountManagerClientInterface&MockObject */
    private AccountManagerClientInterface $accountManagerClient;

    /** @var LocalUserRepository&MockObject */
    private LocalUserRepository $localUserRepository;

    private AccountManagerUserProvider $userProvider;

    protected function setUp(): void
    {
        $this->accountManagerClient = $this->createMock(AccountManagerClientInterface::class);
        $this->localUserRepository = $this->createMock(LocalUserRepository::class);

        $this->userProvider = new AccountManagerUserProvider(
            $this->accountManagerClient,
            $this->localUserRepository
        );
    }

    public function testRefreshUserWithValidAuthenticatedUser(): void
    {
        $jwt = new Jwt('valid.jwt.token');
        $roles = new AccountRoles('ROLE_ADMIN');
        $uuid = Uuid::v4();
        $authenticatedUser = new AuthenticatedUser($uuid, 'email', 'test@example.com', $jwt, $roles, 'Original Name');

        $accountManagerUser = new AccountManagerUserDto(
            'New Name',
            new AccountManagerIdentifierDto('email', 'test@example.com'),
            new AccountRoles('ROLE_ADMIN')
        );

        $this->accountManagerClient
            ->expects($this->once())
            ->method('getUserByToken')
            ->with($jwt)
            ->willReturn($accountManagerUser);

        $localUser = new LocalUser(new AccountIdentifier('email', 'test@example.com'), $jwt);
        $this->localUserRepository
            ->expects($this->once())
            ->method('findByIdentifier')
            ->with(new AccountIdentifier($localUser->getIdentifierType(), $localUser->getIdentifierValue()))
            ->willReturn($localUser);

        $this->localUserRepository
            ->expects($this->once())
            ->method('store')
            ->with($localUser);

        $refreshedUser = $this->userProvider->refreshUser($authenticatedUser);

        self::assertSame('New Name', $localUser->getName());
        self::assertSame(['ROLE_ADMIN'], $localUser->getRoles()->toArray());
    }

    public function testRefreshUserWithInvalidUser(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Wrong user class provided.');

        $invalidUser = $this->createMock(UserInterface::class);
        $this->userProvider->refreshUser($invalidUser);
    }

    public function testSupportsClass(): void
    {
        self::assertTrue($this->userProvider->supportsClass(AuthenticatedUser::class));
        self::assertFalse($this->userProvider->supportsClass(\stdClass::class));
    }

    public function testLoadUserByIdentifierThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('this method');

        $this->userProvider->loadUserByIdentifier('test-identifier');
    }
}
