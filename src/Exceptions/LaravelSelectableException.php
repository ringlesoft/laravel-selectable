<?php

namespace RingleSoft\LaravelSelectable\Exceptions;

use Exception;
use Throwable;

class LaravelSelectableException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = "Laravel Selectable: " . $message;
        parent::__construct($message, $code, $previous);
    }

}
