<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\AccountManager\Provider;

use App\Auth\Domain\AuthenticatedUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @template-implements UserProviderInterface<AuthenticatedUser> */
final readonly class AccountManagerUserProvider implements UserProviderInterface
{
    public function refreshUser(UserInterface $user): AuthenticatedUser
    {
        if (!$user instanceof AuthenticatedUser) {
            throw new \InvalidArgumentException(sprintf('User must be an instance of "%s".', AuthenticatedUser::class));
        }

        return $user;
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
