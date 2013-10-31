<?php
namespace EACHeRepublica\Lib;

require '/srv/www/EACHe-sua-republica/config/database.php';

use \PDO;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package EACHeRepublica
 */
abstract class Database
{
    /**
     * @static
     */
    private static $PDOInstance = null;

    /**
     * @static
     */
    public static function getInstance()
    {
        if (is_null(self::$PDOInstance)) {
            try {
                self::$PDOInstance = new PDO(
                    'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
                    DB_USER,
                    DB_PASSWORD
                );
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return self::$PDOInstance;
    }
}
