<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * NotOwnerException
 *
 * Exception thrown in case of action made on a resource that doesn't belong to the current user
 */
class NotOwnerException extends HttpException
{
    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
