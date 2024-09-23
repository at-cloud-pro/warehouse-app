<?php

declare(strict_types=1);

namespace App\Tests\Unit\Auth\Application\Event\Listener;

use App\Auth\Application\Event\Listener\OnAuthenticationSuccessListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class OnAuthenticationSuccessListenerTest extends TestCase
{
    /** @var MockObject&RouterInterface */
    private RouterInterface $router;

    private OnAuthenticationSuccessListener $listener;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->listener = new OnAuthenticationSuccessListener($this->router);
    }

    public function testOnAuthenticationSuccessRedirectsToDashboard(): void
    {
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);

        $dashboardUrl = '/dashboard';
        $this->router->expects($this->once())
            ->method('generate')
            ->with('app.dashboard')
            ->willReturn($dashboardUrl);

        $response = $this->listener->onAuthenticationSuccess($request, $token);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame($dashboardUrl, $response->getTargetUrl());
    }
}
