<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Tests\Middleware\QueueProducerStrategy;

use Lamoda\TacticianQueue\Job\CommandJob;
use Lamoda\TacticianQueue\Middleware\QueueProducerStrategy\CommandsListToCommandJobStrategy;
use PHPUnit\Framework\TestCase;

final class CommandListToCommandJobStrategyTest extends TestCase
{
    private $stubCommand;
    private $otherCommand;

    /** @var string */
    private $queue;
    /** @var string */
    private $exchange;
    /** @var array */
    private $commandClasses;
    /** @var int */
    private $delaySeconds;

    /** @var CommandsListToCommandJobStrategy */
    private $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stubCommand = new class() {
        };

        $this->otherCommand = new class() {
        };

        $this->queue = 'test_queue';
        $this->exchange = 'test_exchange';

        $this->commandClasses = [
            get_class($this->stubCommand),
        ];

        $this->delaySeconds = 8;

        $this->strategy = new CommandsListToCommandJobStrategy(
            $this->queue,
            $this->exchange,
            $this->commandClasses,
            $this->delaySeconds
        );
    }

    public function testCommandShouldBeConvertedToJob()
    {
        $jobs = $this->strategy->produceQueues($this->stubCommand);

        $this->assertCount(1, $jobs);

        /** @var CommandJob $job */
        $job = $jobs[0];

        $this->assertInstanceOf(CommandJob::class, $job);
        $this->assertEquals($this->queue, $job->getQueue());
        $this->assertEquals($this->exchange, $job->getExchange());
        $this->assertNotNull($job->getScheduleAt());
    }

    public function testCommandShouldNotBeConvertedToJob()
    {
        $jobs = $this->strategy->produceQueues($this->otherCommand);

        $this->assertCount(0, $jobs);
    }
}
