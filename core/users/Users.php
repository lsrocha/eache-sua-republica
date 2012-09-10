<?php
namespace core\users;

use core\Database;

class Users
{
    private $database;

    public function __construct()
    {
        $this->database = new Database();
    }

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

    public function deleteUser($id)
    {
        return $this->database->basicQuery("DELETE FROM users WHERE id='{$id}'");
    }

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

    public function generateSalt()
    {
        $salt = '';

        for ($i=0; $i < 24; $i++) {
            $decimal = rand(33, 125);

            $salt .= chr($decimal);
        }

        return $salt;
    }

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
			
        $this->database->query("UPDATE users SET password='{$password}' WHERE email='{$email}'");

        $this->database->query("DELETE FROM recovery_token WHERE email='{$email}'");        

        $this->database->disconnect();
    }
}

