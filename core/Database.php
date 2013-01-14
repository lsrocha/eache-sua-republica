<?php
namespace core;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package core
 */
class Database extends \PDO
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

    public function __construct()
    {
        parent::__construct(
            'mysql:host='.self::$host.';dbname='.self::$database,
            self::$user,
            self::$password
        );
    }
}

