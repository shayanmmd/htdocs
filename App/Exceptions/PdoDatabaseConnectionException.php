<?php


namespace App\Exceptions;

use PDOException;

class PdoDatabaseConnectionException extends PDOException
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}
