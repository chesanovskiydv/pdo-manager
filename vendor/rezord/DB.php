<?php
require __DIR__ . '\DBConnectionInterface.php';

class DB implements DBConnectionInterface
{
    /** @var DB[] */
    protected static $_instance;
    /** @var  string */
    protected $dsn;
    /** @var  string */
    protected $username;
    /** @var  string */
    protected $password;
    /** @var PDO */
    protected $pdo;

    /**
     * @param        $dsn
     * @param string $username
     * @param string $password
     */
    private function __construct($dsn, $username = '', $password = '')
    {
        $this->setDbConfig($dsn, $username, $password);
        $this->createConnection($dsn, $username, $password);
    }

    private function __clone()
    {
    }

    public function __destruct() {
        $this->close();
    }

    /**
     * Sets the parameters of the current database connection
     *
     * @param        $dsn
     * @param string $username
     * @param string $password
     */
    protected function setDbConfig($dsn, $username = '', $password = '')
    {
        $this->dsn      = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Creates connection with database
     *
     * @param $dsn
     * @param $username
     * @param $password
     */
    protected function createConnection($dsn, $username, $password)
    {
        $pdo = null;
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo = $pdo;
    }

    /**
     * Creates new instance representing a connection to a database
     *
     * @param string $dsn      The Data Source Name, or DSN, contains the information required to connect to the database.
     *
     * @param string $username The user name for the DSN string.
     * @param string $password The password for the DSN string.
     *
     * @see http://www.php.net/manual/en/function.PDO-construct.php
     *
     * @throws  PDOException if the attempt to connect to the requested database fails.
     *
     * @return $this DB
     */
    public static function connect($dsn, $username = '', $password = '')
    {
        if (null === self::$_instance[$dsn]) {
            self::$_instance[$dsn] = new self($dsn, $username, $password);
        } else {
            $db = self::$_instance[$dsn];
            $db->setDbConfig($dsn, $username, $password);
            $db->createConnection($dsn, $username, $password);
        }

        return self::$_instance;
    }

    /**
     * Completes the current session connection, and creates a new.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->createConnection($this->dsn, $this->username, $this->password);
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO the PDO instance, null if the connection is not established yet
     */
    public function getPdoInstance()
    {
        return $this->pdo;
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close()
    {
        $this->pdo = null;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sequenceName name of the sequence object (required by some DBMS)
     *
     * @return string the row ID of the last row inserted, or the last value retrieved from the sequence object
     * @see http://www.php.net/manual/en/function.PDO-lastInsertId.php
     */
    public function getLastInsertID($sequenceName = '')
    {
        return $this->pdo->lastInsertId($sequenceName);
    }

    /**
     * Sets an attribute on the database handle.
     * Some of the available generic attributes are listed below;
     * some drivers may make use of additional driver specific attributes.
     *
     * @param int   $attribute
     * @param mixed $value
     *
     * @return bool
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function setAttribute($attribute, $value)
    {
        return $this->pdo->setAttribute($attribute, $value);
    }

    /**
     * Returns the value of a database connection attribute.
     *
     * @param int $attribute
     *
     * @return mixed
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function getAttribute($attribute)
    {
        return $this->pdo->getAttribute($attribute);
    }
}
