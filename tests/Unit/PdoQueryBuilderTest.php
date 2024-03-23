<?php

include "vendor/autoload.php";

use App\Contracts\QueryBuilderInterface;
use App\DatabaseConnections\PdoDatabaseConnection;
use App\Exceptions\TableEmptyException;
use App\Helpers\Config;
use App\QueryBuilders\PdoQueryBuilder;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class PdoQueryBuilderTest extends TestCase
{
    private PdoDatabaseConnection $dbConnection;
    private PdoQueryBuilder $pdoQueryBuilder;
    private Generator $faker;

    public function setUp(): void
    {
        $this->faker = Factory::create();
        $pdoConfig = Config::get('Database', 'pdo');
        $queryBuilderConfig = Config::get('QueryBuilder', 'pdoQueryBuilder');

        $this->dbConnection = new PdoDatabaseConnection($pdoConfig);
        $this->pdoQueryBuilder = new PdoQueryBuilder($this->dbConnection, 'test', $queryBuilderConfig);

        parent::setUp();
    }

    public function testThatPdoQueryBuilderImplementsValidInterface()
    {
        $this->assertInstanceOf(QueryBuilderInterface::class, $this->pdoQueryBuilder);
    }

    public function testThatTableMethodIsValidInstance()
    {
        $result =  $this->pdoQueryBuilder->table('User');

        $this->assertInstanceOf(PdoQueryBuilder::class, $result);
    }

    public function testThatTableMethodIsNotEmpty()
    {
        $this->expectException(TableEmptyException::class);

        $this->pdoQueryBuilder->table('');
    }

    public function testCreateMethod()
    {
        $rowCounts = $this->insertIntoDb();

        $this->assertIsInt($rowCounts);
        $this->assertNotEquals(0, $rowCounts);
    }

    public function testCreateMethodWithInvalidData()
    {
        $this->expectException(Exception::class);

        $newUser = [
            'sada' => 'waf',
        ];

        $this->pdoQueryBuilder
            ->table('User')
            ->create($newUser);
    }

    public function testMultipleCreateMethod()
    {
        for ($i = 0; $i < 100; $i++) {

            $rowCount = $this->insertIntoDb();

            $this->assertIsInt($rowCount);
            $this->assertNotEquals(0, $rowCount);
        }
    }

    public function testThatDeleteMethodWorksWithoutWhere()
    {
        $this->insertMultipleIntoDb();

        $rowCounts = $this->pdoQueryBuilder
            ->table('User')
            ->delete();

        $this->assertNotEquals(0, $rowCounts);
    }

    public function testThatUpdateMethodWorks()
    {
        $this->insertIntoDb();

        $updateUser = [
            "Name" => "safdsa@#efesgrdsghrd",
            "Email"=>"sad"
        ];

        $rowCounts = $this->pdoQueryBuilder
            ->table('User')
            ->update($updateUser);

        $this->assertIsInt($rowCounts);
        $this->assertNotEquals(0, $rowCounts);
    }

    public function testThatUpdateMethodThrowsExceptionIfNotValidData()
    {
        $this->expectException(Exception::class);

        $this->insertIntoDb();

        $updateUser = [
            "Ngrfame" => "safdsaaaaaaaaaa"
        ];

        $rowCounts = $this->pdoQueryBuilder
            ->table('User')
            ->update($updateUser);

        $this->assertIsInt($rowCounts);
        $this->assertNotEquals(0, $rowCounts);
    }

    public function testThatSelectMethodWorks()
    {
        $user = $this->pdoQueryBuilder
            ->table('User')
            ->select();

        $this->assertNotInstanceOf(stdClass::class,$user);
        
    }

    private function insertIntoDb()
    {

        $newUser = $this->generateNewUser();

        $rowCount = $this->pdoQueryBuilder
            ->table('User')
            ->create($newUser);

        return $rowCount;
    }

    private function insertMultipleIntoDb()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->insertIntoDb();
        }
    }

    private function generateNewUser()
    {
        $newUser = array(
            'Name' => $this->faker->name(),
            'Sex' => random_int(1, 2),
            'PhoneNumber' => $this->faker->phoneNumber(),
            'Email' => $this->faker->email(),
        );

        return $newUser;
    }

    public function tearDown(): void
    {
        $this->pdoQueryBuilder->rollBackTransaction();
        parent::tearDown();
    }
}
