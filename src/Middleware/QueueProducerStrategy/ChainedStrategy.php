<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Middleware\QueueProducerStrategy;

final class ChainedStrategy implements QueueProducerStrategyInterface
{
    /**
     * @var QueueProducerStrategyInterface[]
     */
    private $strategies;

    /**
     * @param QueueProducerStrategyInterface[] $strategies
     */
    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    public function addStrategy(QueueProducerStrategyInterface $strategy): self
    {
        $this->strategies[] = $strategy;

        return $this;
    }

    public function produceQueues($command): array
    {
        $jobs = [];

        foreach ($this->strategies as $strategy) {
            $jobs[] = $strategy->produceQueues($command);
        }

        if (!$jobs) {
            return [];
        }

        return array_merge(...$jobs);
    }
}
