<?php

namespace App\Exceptions;

class SendNotifyFailException extends \Exception
{
    public function __construct(string $message = 'Expectation Failed', int $code = 471, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
