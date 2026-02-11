<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when ESL command execution fails
 */
class EslCommandException extends Exception
{
    public function __construct(string $message = "ESL command execution failed", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
