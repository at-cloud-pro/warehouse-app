<?php

declare(strict_types=1);

namespace App\Auth\UI\Action\Gui;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignedOutAction extends AbstractController
{
    #[Route(
        path: '/{_locale}/auth/signed-out',
        name: 'app.auth.signed-out',
        requirements: ['_locale' => '%app.supported_locale%']
    )]
    public function __invoke(): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('app.auth.sign-out');
        }

        return $this->render('pages/landing/signed_out.html.twig');
    }
}
