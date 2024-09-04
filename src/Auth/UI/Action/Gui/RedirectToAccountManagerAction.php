<?php

declare(strict_types=1);

namespace App\Auth\UI\Action\Gui;

use App\Auth\Domain\Service\AccountManagerUrlProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RedirectToAccountManagerAction extends AbstractController
{
    public function __construct(private readonly AccountManagerUrlProviderInterface $accountManagerUrlProvider) {}

    #[Route(path: '/auth/redirect', name: 'app.auth.redirect-to-iam')]
    public function __invoke(): Response
    {
        $url = $this->accountManagerUrlProvider->getAccountManagerUrl();

        return $this->redirect($url);
    }
}
