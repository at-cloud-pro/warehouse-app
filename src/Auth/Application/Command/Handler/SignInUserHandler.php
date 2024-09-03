<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\Handler;

use App\Auth\Application\Command\SignInUserCommand;
use App\Auth\Domain\AuthenticatedUser;
use App\Auth\Infrastructure\Doctrine\Entity\LocalUser;
use App\Auth\Infrastructure\Doctrine\Repository\LocalUserRepository;
use App\Auth\Infrastructure\JsonWebToken\JwtDecoderFacade;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SignInUserHandler
{
    public function __construct(
        private JwtDecoderFacade $jwtDecoderFacade,
        private LocalUserRepository $localUserRepository,
        private Security $security
    ) {}

    public function __invoke(SignInUserCommand $command): void
    {
        $identifier = $this->jwtDecoderFacade->getIdentifierFromToken($command->token);

        $localUser = $this->localUserRepository->findByAccountManagerUser($identifier);

        if (null === $localUser) {
            $this->localUserRepository->addUserByAccountManagerUser($identifier);

            /** @var LocalUser $localUser */
            $localUser = $this->localUserRepository->findByAccountManagerUser($identifier);
        }

        $account = AuthenticatedUser::createFromLocalUser($localUser);

        $this->security->login($account);
    }
}
