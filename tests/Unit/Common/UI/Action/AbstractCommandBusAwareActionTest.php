<?php

declare(strict_types=1);

namespace App\Tests\Unit\Common\UI\Action;

use App\Common\UI\Action\AbstractCommandBusAwareAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class AbstractCommandBusAwareActionTest extends TestCase
{
    /** @var MessageBusInterface&MockObject */
    private MessageBusInterface $messageBus;
    private AbstractCommandBusAwareAction $action;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->action = new class extends AbstractCommandBusAwareAction {};
    }

    public function testSetMessageBus(): void
    {
        $enrichedAction = $this->action->setMessageBus($this->messageBus);
        self::assertNotSame($this->action, $enrichedAction);
    }

    public function testDoDispatchesCommand(): void
    {
        $command = new \stdClass();
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command));

        $this->action->setMessageBus($this->messageBus)->do($command);
    }
}
