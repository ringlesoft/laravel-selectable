<?php

namespace RingleSoft\LaravelSelectable\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class InvalidCallableException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
