<?php

declare(strict_types=1);

namespace App\Auth\UI\Action\Gui;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class SignOutAction extends AbstractController
{
    #[Route(
        path: '/{_locale}/auth/sign-out',
        name: 'app.auth.sign-out',
        requirements: ['_locale' => '%app.supported_locale%']
    )]
    public function __invoke(): never
    {
        throw new \RuntimeException('This method will never be called.');
    }
}
