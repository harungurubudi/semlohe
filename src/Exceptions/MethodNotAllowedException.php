<?php
namespace App\Semlohe\Exceptions;

class MethodNotAllowedException extends HttpException
{
    public function __construct($message = 'Method Not Allowed', \Exception $previous = null, array $headers = array())
    {
        parent::__construct(405, $message, $previous, $headers);
    }
}
