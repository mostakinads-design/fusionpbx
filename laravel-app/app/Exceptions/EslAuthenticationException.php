<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when ESL authentication fails
 */
class EslAuthenticationException extends Exception
{
    public function __construct(string $message = "ESL authentication failed", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
