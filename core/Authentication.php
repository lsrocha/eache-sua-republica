<?php
namespace core;

use core\Database;

/**
 * @abstract
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package core
 */
abstract class Authentication
{
    /**
     * @static
     * @param string $email
     * @param string $password
     * @param string $location URL you want to redirect user to
     */
    public static function login($email, $password, $location)
    {
        $email = addslashes($email);

        $database = new Database();
        $database->connect();
        $result = $database->query("SELECT id, password, salt FROM users WHERE email='{$email}'");

        if ($result->num_rows == 1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);		

            $passwordHash = hash('sha512', $row['salt'].$password);	

            if ($row['password'] == $passwordHash) {
                $_SESSION['user_id'] = $row['id'];

                header('Location: '.$location);
            }
        }

        $database->disconnect();
    }

    /**
     * @static
     */
    public static function logout(){
        unset($_SESSION['user_id']);

        session_destroy();
    }

    /**
     * @static
     * @return boolean
     */
    public static function isLoggedIn(){
        return isset($_SESSION['user_id']);
    }
}

