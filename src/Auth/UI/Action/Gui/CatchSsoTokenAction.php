<?php

declare(strict_types=1);

namespace App\Auth\UI\Action\Gui;

use App\Auth\Application\Command\SignInUserCommand;
use App\Auth\Domain\Jwt;
use App\Common\UI\Action\AbstractCommandBusAwareAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatchSsoTokenAction extends AbstractCommandBusAwareAction
{
    #[Route(
        path: '/{_locale}/auth/catch',
        requirements: ['_locale' => '%app.supported_locale%']
    )]
    public function __invoke(Request $request): Response
    {
        $token = $request->query->get('token');

        if (!is_string($token)) {
            return $this->redirectToRoute('app.auth.redirect-to-iam');
        }

        $jwt = new Jwt($token);
        $command = new SignInUserCommand($jwt);

        $this->do($command);

        return $this->redirectToRoute('app.dashboard');
    }
}
