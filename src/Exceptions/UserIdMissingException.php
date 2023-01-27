<?php

namespace dnj\Ticket\Exceptions;

class UserIdMissingException extends \Exception
{
    public function __construct(
        string $message = 'Cannot Find Current User Id',
        int $code = 0,
        \Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
