<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Tests\Middleware;

use Lamoda\QueueBundle\Factory\PublisherFactory;
use Lamoda\TacticianQueue\Job\CommandJob;
use Lamoda\TacticianQueue\Middleware\Command\ReceivedCommand;
use Lamoda\TacticianQueue\Middleware\QueueMiddleware;
use Lamoda\TacticianQueue\Middleware\QueueProducerStrategy\QueueProducerStrategyInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class QueueMiddlewareTest extends TestCase
{
    /**
     * @var string
     */
    private $publishedCommandClass;

    private $publishedCommand;

    /**
     * @var PublisherFactory | PHPUnit_Framework_MockObject_MockObject
     */
    private $publisherFactory;

    /**
     * @var QueueProducerStrategyInterface | PHPUnit_Framework_MockObject_MockObject
     */
    private $queueProducerStrategy;

    /**
     * @var QueueMiddleware
     */
    private $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publisherFactory = $this->createMock(PublisherFactory::class);
        $this->queueProducerStrategy = $this->createMock(QueueProducerStrategyInterface::class);

        $this->middleware = new QueueMiddleware(
            $this->publisherFactory,
            $this->queueProducerStrategy
        );

        $this->publishedCommand = new class() {
        };

        $this->publishedCommandClass = get_class($this->publishedCommand);
    }

    /**
     * @param $command
     * @param $expectNextCalled
     * @param $expectedCommandPassed
     * @param $publishedQueues
     *
     * @dataProvider dataHandling
     */
    public function testHandling($command, $expectNextCalled, $expectedCommandPassed, $publishedQueues)
    {
        $nextCalled = false;

        $next = function ($passedCommand) use (&$nextCalled, $expectedCommandPassed) {
            $nextCalled = true;
            $this->assertEquals($expectedCommandPassed, $passedCommand);
        };

        $this->queueProducerStrategy->expects($this->any())
            ->method('produceQueues')
            ->willReturn($publishedQueues);

        $this->publisherFactory->expects($this->any())
            ->method('publish')
            ->withConsecutive($publishedQueues);

        $this->middleware->execute($command, $next);

        $this->assertEquals($expectNextCalled, $nextCalled);
    }

    public function dataHandling()
    {
        $simpleCommand = new \stdClass();
        $commandToJob = $this->publishedCommand;
        $receivedCommand = new \stdClass();
        $wrappedCommand = new ReceivedCommand($receivedCommand);

        return [
            [
                $simpleCommand,
                true,
                $simpleCommand,
                [],
            ],
            [
                $commandToJob,
                false,
                null,
                [
                    new CommandJob($commandToJob, 'test', 'test'),
                ],
            ],
            [
                $wrappedCommand,
                true,
                $receivedCommand,
                [],
            ],
        ];
    }
}
