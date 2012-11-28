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
     * @var Database
     */
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     *
     * @return boolean
     */
    public function addUser($name, $email, $password)
    {
        $name = addslashes($name);
        $email = addslashes($email);

        $salt = self::generateSalt();
        $password = hash('sha512', $salt.$password);

        $sql = <<<EOD
INSERT INTO users(name, email, password, salt) 
VALUES ('{$name}', '{$email}', '{$password}', '{$salt}')
EOD;

        return $this->database->basicQuery($sql);
    }

    /**
     * @param int $id User ID
     * @return boolean
     */
    public function deleteUser($id)
    {
        return $this->database->basicQuery("DELETE FROM users WHERE id='{$id}'");
    }

    /**
     * @return string 
     */
    public function listUsers()
    {
        $this->database->connect();
        $result = $this->database->query('SELECT name, email FROM users');

        $table = '<table><tr><th>Name</th><th>E-mail</th></tr>';

        while ($value = $result->fetch_row()) {
            $table .= '<tr><td>'.$value[0].'</td><td>'.$value[1].'</td></tr>';
        }

        $table .= '</table>';

        $this->database->disconnect();

        return $table;
    }

    /**
     * @param string $email
     * @return boolean
     */
    public function isEmailRegistered($email)
    {
        $email = addslashes($email);

        $this->database->connect();
        $result = $this->database->query("SELECT * FROM users WHERE email='{$email}'");

        if ($result->num_rows != 0) {
            $available = true;
        } else {
            $available = false;
        }

        $this->database->disconnect();

        return $available;
    }

    /**
     * @return string
     */
    public function generateSalt()
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
    public function generateRecoveryToken($email)
    {
        $email = addslashes($email);
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

        $this->database->basicQuery("INSERT INTO recovery_token(email, token) VALUES ('{$email}', '{$token}')");

        return $token;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $token
     *
     * @return boolean
     */
    public function createNewPassword($email, $password, $token)
    {
        $email = addslashes($email);

        $this->database->connect();

        $sql = <<<EOD
SELECT salt FROM users 
INNER JOIN recovery_token 
ON users.email=recovery_token.email 
WHERE users.email='{$email}' 
AND recovery_token.token='{$token}';
EOD;

        $result = $this->database->query($sql);

        $row = $result->fetch_array(MYSQLI_ASSOC);

        $password = hash('sha512', $row['salt'].$password);
			
        $updated = $this->database->query("UPDATE users SET password='{$password}' WHERE email='{$email}'");

        $this->database->query("DELETE FROM recovery_token WHERE email='{$email}'");        

        $this->database->disconnect();

        return $updated;
    }
}

