<?php

declare(strict_types=1);

namespace LamodaTacticianQueue\Tests\Middleware\QueueProducerStrategy;

use Lamoda\QueueBundle\QueueInterface;
use Lamoda\TacticianQueue\Middleware\QueueProducerStrategy\ChainedStrategy;
use Lamoda\TacticianQueue\Middleware\QueueProducerStrategy\QueueProducerStrategyInterface;
use PHPUnit\Framework\TestCase;

final class ChainedStrategyTest extends TestCase
{
    public function testChainedStrategyCreation(): void
    {
        $command = new \stdClass();

        $strategy1 = $this->createMock(QueueProducerStrategyInterface::class);
        $strategy2 = $this->createMock(QueueProducerStrategyInterface::class);

        $job1 = $this->createMock(QueueInterface::class);
        $job2 = $this->createMock(QueueInterface::class);

        $strategy1->expects($this->once())
            ->method('produceQueues')
            ->with($command)
            ->willReturn([$job1]);

        $strategy2->expects($this->once())
            ->method('produceQueues')
            ->with($command)
            ->willReturn([$job2]);

        $strategy = new ChainedStrategy([
            $strategy1,
            $strategy2,
        ]);

        $result = $strategy->produceQueues($command);

        $this->assertEquals([
            $job1,
            $job2,
        ], $result);
    }

    public function testDynamicAddOfStrategies(): void
    {
        $command = new \stdClass();

        $strategy1 = $this->createMock(QueueProducerStrategyInterface::class);
        $strategy2 = $this->createMock(QueueProducerStrategyInterface::class);

        $job1 = $this->createMock(QueueInterface::class);
        $job2 = $this->createMock(QueueInterface::class);

        $strategy1->expects($this->once())
            ->method('produceQueues')
            ->with($command)
            ->willReturn([$job1]);

        $strategy2->expects($this->once())
            ->method('produceQueues')
            ->with($command)
            ->willReturn([$job2]);

        $strategy = new ChainedStrategy([
            $strategy1,
        ]);

        $strategy->addStrategy($strategy2);

        $result = $strategy->produceQueues($command);

        $this->assertEquals([
            $job1,
            $job2,
        ], $result);
    }

    public function testNoStrategies(): void
    {
        $command = new \stdClass();

        $strategy = new ChainedStrategy([]);

        $result = $strategy->produceQueues($command);

        $this->assertEquals([], $result);
    }
}
