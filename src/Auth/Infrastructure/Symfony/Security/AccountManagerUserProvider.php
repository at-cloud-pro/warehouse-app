<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Symfony\Security;

use App\Auth\Infrastructure\AccountManager\Client\AccountManagerClientInterface;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @template-implements UserProviderInterface<AuthenticatedUser> */
final readonly class AccountManagerUserProvider implements UserProviderInterface
{
    public function __construct(
        private AccountManagerClientInterface $accountManagerClient,
        private LocalUserRepository $localUserRepository
    ) {}

    public function refreshUser(UserInterface $user): AuthenticatedUser
    {
        if (!$user instanceof AuthenticatedUser) {
            throw new \RuntimeException('Wrong user class provided.');
        }

        $accountManagerUser = $this->accountManagerClient->getUserByToken($user->getJwt());
        $accountManagerIdentifier = $accountManagerUser->getAccountManagerIdentifier();

        /** @var LocalUser $localUser user cannot be null on this point */
        $localUser = $this->localUserRepository->findByIdentifier($accountManagerIdentifier);

        $localUser->updateName($accountManagerUser->name);
        $localUser->updateRoles($accountManagerUser->roles);

        $this->localUserRepository->store($localUser);

        return AuthenticatedUser::createFromLocalUser($localUser);
    }

    public function supportsClass(string $class): bool
    {
        return AuthenticatedUser::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): AuthenticatedUser
    {
        throw new \RuntimeException('this method');
    }
}
