<?php

namespace Fudge\DBAL;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @coversDefaultClass \Fudge\DBAL\Repository
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    const TABLE_NAME = "foo";

    // set up doctrine connection and a repository
    public function setUp()
    {
        $this->db = DriverManager::getConnection([
            "path" => ":memory:",
            "driver" => "pdo_sqlite",
        ], new Configuration);

        $schema = new Schema;
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn("column_1", "string");
        $table->addColumn("column_2", "string");

        $this->db->getSchemaManager()->createTable($table);

        $this->repository = new Repository($this->db);
        $this->repository->setTable(self::TABLE_NAME);
    }

    /**
     * @coversNothing
     */
    public function testExists()
    {
        $this->assertTrue(class_exists("Fudge\DBAL\Repository"));
    }

    /**
     * @covers ::__construct
     * @covers ::getConnection
     */
    public function testConnection()
    {
        $this->assertEquals($this->db, $this->repository->getConnection());
    }

    /**
     * @covers ::setTable
     * @covers ::getTable
     */
    public function testSettingOfTable()
    {
        $expected = "test_table";
        $this->repository->setTable($expected);
        $this->assertEquals($expected, $this->repository->getTable());
    }

    /**
     * @covers ::getDao
     */
    public function testDefaultDao()
    {
        $this->assertEquals("stdClass", $this->repository->getDao());
    }

    /**
     * @covers ::newQueryBuilder
     */
    public function testNewQueryBuilder()
    {
        $this->assertInstanceOf(QueryBuilder::class, $this->repository->newQueryBuilder());
    }

    /**
     * @covers ::newCollection
     */
    public function testNewCollection()
    {
        $collection = $this->repository->newCollection([1, 2, 3]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(3, $collection);
    }

    /**
     * @covers ::insert
     */
    public function testInserting()
    {
        $expected = 1;
        $result = $this->repository->insert([
            "column_1" => "foo",
            "column_2" => "bar",
        ]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $this->generateRows();

        $this->repository->delete(["column_1" => 1]);
        $remaining = $this->repository->findAll();
        $this->assertCount(9, $remaining);
    }

    /**
     * @covers ::update
     */
    public function testUpdate()
    {
        $this->generateRows();

        $this->repository->update(["column_1" => "ben"], ["column_1" => 1]);
        $row = $this->repository->findBy(2, "column_2");
        $this->assertEquals("ben", $row->column_1);
    }

    /**
     * @covers ::findBy
     * @covers ::fetch
     */
    public function testFindBy()
    {
        $this->generateRows();

        $result = $this->repository->findBy(1, "column_1");
        $this->assertInstanceOf("stdClass", $result);
        $this->assertObjectHasAttribute("column_1", $result);
        $this->assertEquals(1, $result->column_1);
    }

    /**
     * @covers ::findAllBy
     * @covers ::fetchAll
     */
    public function testFindAllBy()
    {
        $this->generateRows(10, true);

        $results = $this->repository->findAllBy(1, "column_1");
        $this->assertInstanceOf("Fudge\DBAL\Collection", $results);
        $this->assertCount(10, $results);
    }

    /**
     * @covers ::findAll
     * @covers ::fetchAll
     */
    public function testFindAll()
    {
        $this->generateRows(10, true);

        $results = $this->repository->findAll();
        $this->assertInstanceOf("Fudge\DBAL\Collection", $results);
        $this->assertCount(10, $results);
    }

    /**
     * @coversNothing
     */
    private function generateRows($rows = 10, $static = false)
    {
        for ($i = 0; $i < $rows; $i += 1) {
            $this->repository->insert([
                "column_1" => $static ? 1 : $i,
                "column_2" => $i * 2,
            ]);
        }
    }
}
