<?php

declare(strict_types=1);

namespace Lamoda\TacticianQueue\Exception;

final class CanNotHandleJobException extends \InvalidArgumentException
{
    public static function becauseJobShouldInstanceOf(string $className): self
    {
        return new static('Handler can handle only jobs of class ' . $className);
    }
}
