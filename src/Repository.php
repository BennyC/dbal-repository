<?php

namespace Fudge\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Base repository class for extending and creating your own repositories
 * Comes with standard CRUD operations and methods to create more complex
 * queries
 */
class Repository
{
    /**
     * @var    Doctrine\DBAL\Connection
     * @access protected
     */
    protected $db;

    /**
     * @var    string
     * @access protected
     */
    protected $table;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Return the DBAL connection
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Return the database table for this repository
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the database table for this repository
     * @param  string
     * @return void
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Return the Data Access Object's fully qualified domain name for this
     * repository
     * Extend this method to use your own DAO
     * @return string
     */
    public function getDao()
    {
        return \stdClass::class;
    }

    /**
     * Use the QueryBuilder instance to retrieve the requested Object of
     * ::getDao() type
     * @param  QueryBuilder
     * @return mixed
     */
    protected function fetch(QueryBuilder $query)
    {
        $stmt = $query->execute();
        return $stmt->fetchObject($this->getDao());
    }

    /**
     * Use the QueryBuilder instance to retrieve all the requested Objects of
     * ::getDao() type
     * @param  QueryBuilder
     * @return mixed
     */
    protected function fetchAll(QueryBuilder $query)
    {
        $stmt = $query->execute();
        return new Collection($stmt->fetchAll(\PDO::FETCH_CLASS, $this->getDao()));
    }

    /**
     * Create a new DBAL query builder
     * @return \Doctrine\Query\QueryBuilder
     */
    public function newQueryBuilder()
    {
        return $this->db->createQueryBuilder();
    }

    /**
     * Insert data into the current repository table
     * @param  array
     * @return integer
     */
    public function insert(array $insertData)
    {
        $this->db->insert($this->getTable(), $insertData);
        return $this->db->lastInsertId();
    }

    /**
     * Simple delete for rows found by the set of constraints
     * @param  array
     * @return void
     */
    public function delete(array $constraints)
    {
        $this->db->delete($this->getTable(), $constraints);
    }

    /**
     * Simple update for rows found by the set of constraints
     * @param  array
     * @param  array
     * @return integer
     */
    public function update(array $update, array $constraints)
    {
        $this->db->update($this->getTable(), $update, $constraints);
    }

    /**
     * Find single instance of a data row by the search parameter against the
     * column
     * @param  mixed
     * @param  string
     * @return mixed
     */
    public function findBy($search, $column = "id")
    {
        $qbl = $this->newQueryBuilder();
        $qbl->select("*")
            ->from($this->getTable())
            ->where("{$column} = :search")
            ->setMaxResults(1)
            ->setParameters([
                ":search" => $search,
            ]);

        return $this->fetch($qbl);
    }

    /**
     * Find multiple instances of data rows by the search parameter against the
     * column
     * @param  mixed
     * @param  string
     * @return mixed
     */
    public function findAllBy($search, $column = "id")
    {
        $qbl = $this->newQueryBuilder();
        $qbl->select("*")
            ->from($this->getTable())
            ->where("{$column} = :search")
            ->setParameters([
                ":search" => $search,
            ]);

        return $this->fetchAll($qbl);
    }

    /**
     * Find all instances
     * @return array
     */
    public function findAll()
    {
        $qbl = $this->newQueryBuilder();
        $qbl->select("*")
            ->from($this->getTable());

        return $this->fetchAll($qbl);
    }
}
