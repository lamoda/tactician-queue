<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Middleware\QueueProducerStrategy;

use Lamoda\TacticianQueue\Job\CommandJob;

final class CommandsListToCommandJobStrategy implements QueueProducerStrategyInterface
{
    /**
     * @var string
     */
    private $queue;
    /**
     * @var string
     */
    private $exchange;
    /**
     * @var array
     */
    private $commandsClasses;
    /**
     * @var int
     */
    private $delaySeconds;

    public function __construct(string $queue, string $exchange, array $commandsClasses = [], int $delaySeconds = 0)
    {
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->commandsClasses = $commandsClasses;
        $this->delaySeconds = $delaySeconds;
    }

    public function produceQueues($command): array
    {
        foreach ($this->commandsClasses as $commandClass) {
            if ($command instanceof $commandClass) {
                $queue = new CommandJob($command, $this->queue, $this->exchange);

                if ($this->delaySeconds > 0) {
                    $queue->setScheduleAt((new \DateTime())->modify('+' . $this->delaySeconds . ' second'));
                }

                return [$queue];
            }
        }

        return [];
    }
}
