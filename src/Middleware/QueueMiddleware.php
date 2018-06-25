<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Middleware;

use Lamoda\QueueBundle\Factory\PublisherFactory;
use Lamoda\TacticianQueue\Middleware\Command\ReceivedCommand;
use Lamoda\TacticianQueue\Middleware\QueueProducerStrategy\QueueProducerStrategyInterface;
use League\Tactician\Middleware;

final class QueueMiddleware implements Middleware
{
    /**
     * @var PublisherFactory
     */
    private $publisherFactory;
    /**
     * @var QueueProducerStrategyInterface
     */
    private $queueProducerStrategy;

    public function __construct(
        PublisherFactory $publisherFactory,
        QueueProducerStrategyInterface $queueProducerStrategy
    ) {
        $this->publisherFactory = $publisherFactory;
        $this->queueProducerStrategy = $queueProducerStrategy;
    }

    public function execute($command, callable $next)
    {
        if ($command instanceof ReceivedCommand) {
            $command = $command->getCommand();

            return $next($command);
        }

        if (!empty($queues = $this->queueProducerStrategy->produceQueues($command))) {
            foreach ($queues as $queue) {
                $this->publisherFactory->publish($queue);
            }

            return null;
        }

        return $next($command);
    }
}
