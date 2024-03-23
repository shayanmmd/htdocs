<?php


namespace App\DatabaseConnections;

include "vendor/autoload.php";

use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\PdoDatabaseConnectionException;
use PDO;
use PDOException;


class PdoDatabaseConnection implements DatabaseConnectionInterface
{
    private $config;
    private $connection;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        $dsnArray = $this->generateDsn();
        try {
            $connection = new PDO($dsnArray['dsn'], $dsnArray['userName'], $dsnArray['password']);
            $this->connection = $connection;
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            return $this;
        } catch (PDOException $e) {
            throw new PdoDatabaseConnectionException($e->getMessage());
        }
    }

    public function getConnection()
    {
        if (is_null($this->connection))
            $this->connect();
        return $this->connection;
    }

    private function generateDsn()
    {
        $config = $this->config;
        $dsn = "{$config['driver']}:host={$config['host']};dbName={$config['dbName']};";

        $dsnArray = array(
            'dsn' => $dsn,
            'userName' => $config['userName'],
            'password' =>  $config['password']
        );
        return $dsnArray;
    }
}
