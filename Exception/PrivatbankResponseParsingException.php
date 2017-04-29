<?php

namespace Mukhin\PrivatbankBundle\Exception;

use Throwable;

class PrivatbankResponseParsingException extends PrivatbankException
{
    public function __construct($message = "Error occured during parsing response from Privatbank API endpoint", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
