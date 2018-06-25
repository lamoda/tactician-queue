<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Job;

use JMS\Serializer\Annotation as Serializer;
use Lamoda\QueueBundle\Job\AbstractJob;

final class CommandJob extends AbstractJob
{
    /**
     * @var object
     *
     * @Serializer\Type("tactician_command")
     */
    private $command;

    public function __construct($command, string $queue, string $exchange)
    {
        $this->command = $command;
        $this->queue = $queue;
        $this->exchange = $exchange;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @deprecated Only for compatibility with current implementation in queue
     */
    public function getDefaultQueue(): string
    {
        return $this->queue;
    }

    /**
     * @deprecated Only for compatibility with current implementation in queue
     */
    public function getDefaultExchange(): string
    {
        return $this->exchange;
    }
}
