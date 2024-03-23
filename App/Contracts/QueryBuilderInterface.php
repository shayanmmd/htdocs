<?php

namespace App\Contracts;

interface QueryBuilderInterface
{
    function table(string $name);
    function where(array $conditions);
    function select(...$columns);
    function update(array $columns);
    function delete();
    function create(array $columns);
}