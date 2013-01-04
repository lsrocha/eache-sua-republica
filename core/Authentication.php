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
     *
     * @return boolean
     */
    public static function login($email, $password)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validEmail) {
            return false;
        }

        $database = new Database();
        $database->connect();
        $result = $database->query("SELECT id, password, salt FROM users WHERE email='{$email}'");

        $success = false;
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_array(MYSQLI_ASSOC);		

            $passwordHash = hash('sha512', $row['salt'].$password);	

            $success = ($row['password'] == $passwordHash);
            
            if ($success) {
                $_SESSION['user_id'] = $row['id'];
            }
        }

        $database->disconnect();

        return $success;
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

