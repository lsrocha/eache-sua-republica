<?php
namespace core;

use core\Database;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 * @package core
 */
class Users
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     *
     * @return boolean
     */
    public static function addUser($name, $email, $password, Database &$database)
    {
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $password = addslashes($passowrd);

        $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validEmail) {
            return false;
        }

        $salt = self::generateSalt();
        $password = hash('sha512', $salt.$password);

        $query = $database->prepare('
            INSERT INTO users (name, email, password, salt) 
            VALUES ( :name, :email, :password, :salt)
        ');

        $query->bindParam(':name', $name, Database::PARAM_STR);
        $query->bindParam(':email', $email, Database::PARAM_STR);
        $query->bindParam(':password', $password, Database::PARAM_STR);
        $query->bindParam(':salt', $salt, Database::PARAM_STR);

        return $query->execute();
    }

    /**
     * @param int $id User ID
     * @return boolean
     */
    public static function deleteUser($id, Database &$database)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        
        $query = $database->prepare('DELETE FROM users WHERE id = :id}');
        $query->bindParam(':id', $id, Database::PARAM_INT);    

        return $query->execute();
    }

    /**
     * @return string 
     */
    public static function listUsers(Database &$database)
    {
        $query = $database->query('SELECT name, email FROM users');

        $table = '<table><tr><th>Name</th><th>E-mail</th></tr>';

        while ($value = $query->fetch(Database::FETCH_BOTH)) {
            $table .= '<tr><td>'.$value[0].'</td><td>'.$value[1].'</td></tr>';
        }

        $table .= '</table>';

        return $table;
    }

    /**
     * @param string $email
     * @return boolean
     */
    public static function isEmailRegistered($email, Database &$database)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validEmail) {
            return false;
        }

        $query = $database->prepare('SELECT * FROM users WHERE email = :email');
        $query->bindParam(':email', $email, Database::PARAM_STR);
        $query->execute();

        return ($query->rowCount() != 0);
    }

    /**
     * @return string
     */
    public static function generateSalt()
    {
        $salt = '';

        for ($i=0; $i < 24; $i++) {
            $decimal = rand(33, 125);

            $salt .= chr($decimal);
        }

        return $salt;
    }

    /**
     * @param string $email
     * @return string
     */
    public static function generateRecoveryToken($email, Database &$database)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $token = '';

        for ($i=0; $i < 12; $i++) {
            $decimal = rand(48, 122);

            if ($decimal > 57 && $decimal < 65) {
                $decimal += 65 - $decimal;
            } elseif ($decimal > 90 && $decimal < 97) {
                $decimal += 97 - $decimal;
            } 

            $token .= chr($decimal); 
        }

        $query = $database->prepare('
            INSERT INTO recovery_token(email, token) VALUES ( :email, :token)
        ');

        $query->bindParam(':email', $email, Database::PARAM_STR);
        $query->bindParam(':token', $token, Database::PARAM_STR);
        $query->execute();

        return $token;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $token
     *
     * @return boolean
     */
    public static function createNewPassword($email, $password, $token, Database &$database)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $validEmail = (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

        if (!$validEmail) {
            return false;
        }

        $query = $database->prepare('
            SELECT salt FROM users
            INNER JOIN recovery_token
            ON users.email = recovery_token.email
            WHERE users.email = :email
            AND recovery_token.token = :token
        ');

        $query->bindParam(':email', $email, Database::PARAM_STR);
        $query->bindParam(':token', $token, Database::PARAM_STR);
        $query->execute();

        $row = $query->fetch(Database::FETCH_ASSOC);

        $password = hash('sha512', $row['salt'].$password);

        $query = $database->prepare('
            UPDATE users SET password = :password WHERE email = :email
        ');
        $query->bindParam(':password', $password, Database::PARAM_STR);
        $query->bindParam(':email', $email, Database::PARAM_STR);
        $updated = $query->execute();

        $query = $database->prepare('
            DELETE FROM recovery_token WHERE email = :email
        ');
        $query->bindParam(':email', $email, Database::PARAM_STR);
        $query->execute();

        return $updated;
    }
}

