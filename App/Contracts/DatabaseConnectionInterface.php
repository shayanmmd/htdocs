<?php

namespace App\Contracts;

interface DatabaseConnectionInterface
{
    function connect();
    function getConnection();
}