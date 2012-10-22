<?php
namespace core;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package core
 */
class Database
{
    /**
     * Database host name.
     *
     * @var string
     * @static
     */
    private static $host = '';
    
    /**
     * Database username.
     *
     * @var string
     * @static
     */
    private static $user = '';
    
    /**
     * Database user password.
     *
     * @var string
     * @static
     */
    private static $password = '';
    
    /**
     * Database table name.
     *
     * @var string
     * @static
     */
    private static $database = '';
    
    /**
     * @var mysqli
     */
    private $mysql;
	
    /**
     * Opens MySQL connection.
     */
    public function connect()
    {
        $this->mysql = new \mysqli(self::$host, self::$user, self::$password, self::$database); 
    }

    /**
     * Performs a query on the database.
     *
     * @param string $sql SQL Query
     * @return mysqli_result object
     */
    public function query($sql)
    {
        return $this->mysql->query($sql);
    }

    /**
     * Method for INSERT, UPDATE and DELETE queries.
     *
     * It opens a MySQL connection, executes queries and closes connection.
     *
     * @param string $sql SQL Query
     * @return boolean
     */
    public function basicQuery($sql)
    {
        $this->connect();
        $result = $this->query($sql);
        $this->disconnect();

        return $result;
    }

    /**
     * Closes MySQL connection.
     */
    public function disconnect()
    {
        $this->mysql->close();
    }
}

