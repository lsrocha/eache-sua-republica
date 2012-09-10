<?php
namespace core;

class Database
{
    private static $host = '';
    private static $user = '';
    private static $password = '';
    private static $database = '';
    private $mysql;
	
    public function connect()
    {
        $this->mysql = new \mysqli(self::$host, self::$user, self::$password, self::$database); 
    }

    public function query($sql)
    {
        return $this->mysql->query($sql);
    }

    public function basicQuery($sql)
    {
        $this->connect();
        $result = $this->query($sql);
        $this->disconnect();

        return $result;
    }

    public function disconnect()
    {
        $this->mysql->close();
    }
}

