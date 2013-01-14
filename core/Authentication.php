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
    public static function login($email, $password, Database &$database)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validEmail) {
            return false;
        }

        $query = $database->prepare('
            SELECT id, password, salt FROM users WHERE email = :email
        ');
        $query->bindParam(':email', $email, Database::PARAM_STR);
        $query->execute();

        $success = false;

        if ($query->rowCount() == 1) {
            $result = $query->fetch(Database::FETCH_ASSOC);		

            $passwordHash = hash('sha512', $result['salt'].$password);	

            $success = ($result['password'] == $passwordHash);

            if ($success) {
                $_SESSION['user_id'] = $result['id'];
            }
        }

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

