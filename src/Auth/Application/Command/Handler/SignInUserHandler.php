<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\Handler;

use App\Auth\Application\Command\SignInUserCommand;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
use App\Auth\Infrastructure\Symfony\Security\AuthenticatedUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SignInUserHandler
{
    public function __construct(private LocalUserRepository $localUserRepository, private Security $security) {}

    public function __invoke(SignInUserCommand $command): void
    {
        $userFromToken = $command->token->getUser();
        $localUser = $this->localUserRepository->findByIdentifier($userFromToken->identifier);

        if (null === $localUser) {
            $localUser = new LocalUser($userFromToken->identifier, $userFromToken->jwt);
            $this->localUserRepository->store($localUser);
        }

        $account = AuthenticatedUser::createFromLocalUser($localUser);

        $this->security->login($account);
    }
}
