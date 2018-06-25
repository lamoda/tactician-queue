<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Middleware\QueueProducerStrategy;

use Lamoda\QueueBundle\QueueInterface;

interface QueueProducerStrategyInterface
{
    /**
     * @param $command
     *
     * @return QueueInterface[]
     */
    public function produceQueues($command): array;
}
