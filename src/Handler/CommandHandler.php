<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Handler;

use Lamoda\QueueBundle\Handler\HandlerInterface;
use Lamoda\QueueBundle\QueueInterface;
use Lamoda\TacticianQueue\Exception\CanNotHandleJobException;
use Lamoda\TacticianQueue\Job\CommandJob;
use Lamoda\TacticianQueue\Middleware\Command\ReceivedCommand;
use League\Tactician\CommandBus;

final class CommandHandler implements HandlerInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handle(QueueInterface $job)
    {
        /* @var CommandJob $job */
        $this->assertCommandJob($job);

        $receivedCommand = new ReceivedCommand($job->getCommand());

        $this->commandBus->handle($receivedCommand);
    }

    private function assertCommandJob(QueueInterface $job): void
    {
        if (!$job instanceof CommandJob) {
            throw CanNotHandleJobException::becauseJobShouldInstanceOf(CommandJob::class);
        }
    }
}
