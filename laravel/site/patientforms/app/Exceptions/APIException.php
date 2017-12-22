<?php

namespace App\Exceptions;

class APIException extends \Exception
{
    
    public function __construct($message)
    {
        parent::__construct($message);
    }

}