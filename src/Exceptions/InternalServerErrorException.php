<?php
namespace App\Semlohe\Exceptions;

class InternalServerErrorException extends HttpException
{
    public function __construct(
        $message = 'Internal server error',
        \Exception $previous = null,
        array $headers = array()
    ) {
        parent::__construct(500, $message, $previous, $headers);
    }
}
