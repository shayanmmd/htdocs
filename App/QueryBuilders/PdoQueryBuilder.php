<?php

namespace App\QueryBuilders;

include "vendor/autoLoad.php";

use App\Contracts\QueryBuilderInterface;
use App\DatabaseConnections\PdoDatabaseConnection;
use App\Exceptions\ArgumentNullException;
use App\Exceptions\ArrayIsNullOrEmptyException;
use App\Exceptions\TableEmptyException;
use Exception;
use PDO;

class PdoQueryBuilder implements QueryBuilderInterface
{
    private $config;
    private PDO $pdoDbConnection;
    private $tableName;
    private $databaseName;
    private $where;

    public function __construct(PdoDatabaseConnection $pdoDbConnection, string $databaseName, $config)
    {
        $this->pdoDbConnection = $pdoDbConnection->getConnection();
        $this->databaseName = $databaseName;
        $this->config = $config;

        if ($this->config["transaction"])
            $this->pdoDbConnection->beginTransaction();
    }

    public function table(string $name)
    {
        if (empty($name))
            throw new TableEmptyException();
        $this->tableName = $name;

        return $this;
    }

    public function where(array $conditions)
    {
        if (is_null($conditions))
            throw new ArgumentNullException();
        $whereSections = [];

        foreach ($conditions as $key => $value) {
            $whereSections[]  = $key . "=" . $value;
        }

        $whereSections = implode(' and ', $whereSections);

        $this->where = "where " . $whereSections;

        return $this;
    }

    public function select(...$columns)
    {
        try {
            if (is_null($columns) || empty($columns))
                $columns = "*";
        
            $query = "SELECT $columns FROM `{$this->databaseName}`.`{$this->tableName}` {$this->where}";
            $stmt =  $this->pdoDbConnection->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();

        } catch (\Throwable $th) {
            throw new Exception($th);
        }
    }

    public function update(array $columns)
    {
        try {
            if (is_null($columns) || empty($columns))
                throw new ArrayIsNullOrEmptyException();

            $placeHolders = [];
            foreach ($columns as $key => $value) {
                $placeHolders[] = $key.'=?';
            }
            
            $placeHolders = implode(',', $placeHolders);          
            
            $query = "UPDATE `{$this->databaseName}`.`{$this->tableName}` SET $placeHolders {$this->where};";
           

            $stmt =  $this->pdoDbConnection->prepare($query);
            $stmt->execute(array_values($columns));

            return $stmt->rowCount();
        } catch (Exception $th) {
            throw new Exception($th);
        }
    }

    public function delete()
    {
        $query = "delete from `{$this->databaseName}`.{$this->tableName} {$this->where}";
        $stmt = $this->pdoDbConnection->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function create(array $columns)
    {
        try {
            if (is_null($columns) || empty($columns))
                throw new ArrayIsNullOrEmptyException();

            $placeHolders = [];
            foreach ($columns as $key => $value) {
                $placeHolders[] = '?';
            }
            $placeHolders = implode(',', $placeHolders);

            $columnName = implode(",", array_keys($columns));

            $query = "INSERT INTO `{$this->databaseName}`.`{$this->tableName}` ({$columnName}) VALUES ({$placeHolders});";

            $stmt =  $this->pdoDbConnection->prepare($query);
            $stmt->execute(array_values($columns));

            return $stmt->rowCount();
        } catch (Exception $th) {
            throw new Exception($th);
        }
    }

    public function commitTransaction()
    {
        $this->pdoDbConnection->commit();
    }

    public function rollBackTransaction()
    {
        $this->pdoDbConnection->rollBack();
    }
}
