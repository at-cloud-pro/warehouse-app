<?php

declare(strict_types=1);

namespace App\Dashboard\UI\Action;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app.dashboard')]
final class ShowDashboardAction extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('pages/dashboard/dashboard.html.twig');
    }
}
