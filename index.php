<?php

use Faker\Factory;


include "vendor/autoload.php";
use App\DatabaseConnections\PdoDatabaseConnection;
use App\Helpers\Config;
use App\QueryBuilders\PdoQueryBuilder;

// include "autoLoad.php";
$pdoConfig = Config::get("Database", "pdo");
$queryBuilderConfig = Config::get("QueryBuilder", "pdoQueryBuilder");

var_dump($pdoConfig);

// $connection = new PdoDatabaseConnection($pdoConfig);
// $pdo = new PdoQueryBuilder($connection, 'test', $queryBuilderConfig);

// $newUser = array(
//     // 'Name' => 'AliReza',
//     'Sex' => '1',
//     'PhoneNumber' => '09012508847',
//     'Email' => 'fsdkgfdsg',
// );

// $condition = [
//     'Id' => '123',
//     'Name' => 'shayan'
// ];

// $r = $pdo->table('User')->where($condition);
// var_dump($r);

// $faker = Factory::create();
// echo $faker->name();