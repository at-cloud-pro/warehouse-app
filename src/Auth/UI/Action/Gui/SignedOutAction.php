<?php

declare(strict_types=1);

namespace App\Auth\UI\Action\Gui;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignedOutAction extends AbstractController
{
    #[Route(path: '/auth/signed-out', name: 'app.auth.signed-out')]
    public function __invoke(): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('app.auth.sign-out');
        }

        return $this->render('pages/landing/signed_out.html.twig');
    }
}
