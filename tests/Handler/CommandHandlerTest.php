<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Tests\Handler;

use Lamoda\QueueBundle\QueueInterface;
use Lamoda\TacticianQueue\Exception\CanNotHandleJobException;
use Lamoda\TacticianQueue\Handler\CommandHandler;
use Lamoda\TacticianQueue\Job\CommandJob;
use Lamoda\TacticianQueue\Middleware\Command\ReceivedCommand;
use League\Tactician\CommandBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    /**
     * @var CommandBus | MockObject
     */
    private $commandBus;
    /**
     * @var CommandHandler
     */
    private $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = $this->createMock(CommandBus::class);
        $this->handler = new CommandHandler($this->commandBus);
    }

    /**
     * @param QueueInterface  $job
     * @param ReceivedCommand $expectedCommand
     *
     * @dataProvider dataHandlingQueue
     */
    public function testHandlingQueue(QueueInterface $job, ReceivedCommand $expectedCommand)
    {
        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with($expectedCommand);

        $this->handler->handle($job);
    }

    public function dataHandlingQueue()
    {
        $command = new \stdClass();

        return [
            [
                new CommandJob($command, 'dummy', 'dummy'),
                new ReceivedCommand($command),
            ],
        ];
    }

    public function testNotCommandJobShellNotPass()
    {
        $job = $this->createMock(QueueInterface::class);

        $this->expectException(CanNotHandleJobException::class);

        $this->handler->handle($job);
    }
}
