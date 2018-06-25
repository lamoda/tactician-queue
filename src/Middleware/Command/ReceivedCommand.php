<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Middleware\Command;

final class ReceivedCommand
{
    /**
     * @var object
     */
    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @return object
     */
    public function getCommand()
    {
        return $this->command;
    }
}
