<?php

include_once "vendor/autoLoad.php";

use App\Exceptions\ConfigFileNotFound;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function testGetFileContents()
    {
        $contents =  Config::getFileContents('Database');
        $this->assertIsArray($contents);
    }

    public function testGetFilecontentsNotFound()
    {
        $this->expectException(ConfigFileNotFound::class);
        Config::getFileContents('safd@@#$gvrfgdrsdfsefs');
    }

    public function testGetMethodReturnsValidData()
    {
        $expected = [

            'driver' => 'mysql',
            'host' => 'localhost',
            'dbName' => 'test',
            'userName' => 'root',
            'password' => ''

        ];

        $actual = Config::get('Database', 'pdo');

        $this->assertEquals($expected, $actual);
    }
}
