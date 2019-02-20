<?php
namespace App\Semlohe\Exceptions;

class ForbiddenException extends HttpException
{
    public function __construct($message = 'Forbidden', \Exception $previous = null, array $headers = array())
    {
        parent::__construct(403, $message, $previous, $headers);
    }
}
