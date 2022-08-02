<?php

namespace App\Exceptions;


const ERROR_DUPLICATE_PROGRAM = 800;
const ERROR_SQL_EXCEPTION = 1000;

class ApiException extends \Exception
{
    public function __construct($code)
    {
        switch ($code) {
            case ERROR_DUPLICATE_PROGRAM:
                $message = "Duplicate program";
                break;
            default:
                $message = "Unknown server error";
        }

        parent::__construct($message, $code);
    }
}
