<?php

include "vendor/autoLoad.php";

use App\DatabaseConnections\PdoDatabaseConnection;
use App\Exceptions\PdoDatabaseConnectionException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class PdoDatabaseConnectionTest extends TestCase
{
    private PdoDatabaseConnection $connection;

    public function setUp(): void
    {
        $pdoConfig = Config::get('Database', 'pdo');

        $this->connection = new PdoDatabaseConnection($pdoConfig);

        parent::setUp();
    }

    private function getConfig()
    {
        return Config::getFileContents('Database');
    }

    public function testThatItCanConnect()
    {
        $config = $this->getConfig();

        $cnn = $this->connection->connect($config['pdo']);

        $this->assertInstanceOf(PdoDatabaseConnection::class, $cnn);
    }

    public function testThatItThrowsExceptionIfNotConnected()
    {
        $this->expectException(PdoDatabaseConnectionException::class);

        $pdoConfig = Config::get('Database', 'pdo');
        $pdoConfig['driver'] = 'dasdsa$#%#@$sdfgds';

        $connection = new PdoDatabaseConnection($pdoConfig);
        $connection->connect();
    }

    public function testGetConnectionMethodWorks()
    {
        $pdoConfig = Config::get('Database', 'pdo');

        $sd = new PdoDatabaseConnection($pdoConfig);

        $sdn = $sd->getConnection();

        $this->assertInstanceOf(PDO::class, $sdn);
        $this->assertNotNull($sdn);
    }
}
