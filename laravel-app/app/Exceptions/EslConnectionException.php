<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when ESL connection fails
 */
class EslConnectionException extends Exception
{
    public function __construct(string $message = "Failed to connect to FreeSwitch ESL", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
