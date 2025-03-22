<?php

namespace RingleSoft\LaravelSelectable\Exceptions;

use Throwable;

class InvalidCallableException extends LaravelSelectableException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
