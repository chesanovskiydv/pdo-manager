<?php
namespace rezord\pdo;

class DBQuery implements DBQueryInterface
{
    /** @var  DB */
    protected $dbConnection;

    /** @var  float */
    protected $lastQueryTime;

    /**
     * Create new instance DBQuery.
     *
     * @param DBConnectionInterface $DBConnection
     */
    public function __construct(DBConnectionInterface $DBConnection)
    {
        $this->setDBConnection($DBConnection);
    }

    /**
     * Returns the DBConnection instance.
     *
     * @return DBConnectionInterface
     */
    public function getDBConnection()
    {
        return $this->dbConnection;
    }

    /**
     * Change DBConnection.
     *
     * @param DBConnectionInterface $DBConnection
     *
     * @return void
     */
    public function setDBConnection(DBConnectionInterface $DBConnection)
    {
        $this->dbConnection = $DBConnection;
    }

    /**
     * Returns the PDO instance
     *
     * @return PDO
     */
    protected function getPdoInstance()
    {
        return $this->getDBConnection()->getPdoInstance();
    }

    /**
     * Executes the SQL statement and returns query result.
     *
     * @param string $query  sql query
     * @param array  $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed if successful, returns a PDOStatement on error false
     */
    public function query($query, array $params = null)
    {
        try {
            $sth = $this->getPdoInstance()->prepare($query);
            $start = microtime(true);
            $sth->execute($params);
            $this->lastQueryTime  = microtime(true) - $start;

            return $sth;
        } catch (\PDOException $e) {
            echo 'Error: ' . $e->getMessage();

            return false;
        }
    }

    /**
     * Executes the SQL statement and returns all rows of a result set as an associative array
     *
     * @param string $query  sql query
     * @param array  $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryAll($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return ($sth === false) ? false : $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Executes the SQL statement returns the first row of the query result
     *
     * @param string $query  sql query
     * @param array  $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryRow($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return ($sth === false) ? false : $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Executes the SQL statement and returns the first column of the query result.
     *
     * @param string $query  sql query
     * @param array  $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryColumn($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return ($sth === false) ? false : $sth->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Executes the SQL statement and returns the first field of the first row of the result.
     *
     * @param string $query  sql query
     * @param array  $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed  column value
     */
    public function queryScalar($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return ($sth === false) ? false : $sth->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * Executes the SQL statement.
     * This method is meant only for executing non-query SQL statement.
     * No result set will be returned.
     *
     * @param string $query  sql query
     * @param array  $params input parameters (name=>value) for the SQL execution
     *
     * @return integer number of rows affected by the execution.
     */
    public function execute($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return ($sth === false) ? false : $sth->rowCount();
    }

    /**
     * Returns the last query execution time in seconds
     *
     * @return float query time in seconds
     */
    public function getLastQueryTime()
    {
        return $this->lastQueryTime;
    }
}