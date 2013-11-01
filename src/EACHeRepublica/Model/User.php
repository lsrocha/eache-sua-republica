<?php
namespace EACHeRepublica\Model;

use \PDO;
use EACHeRepublica\Lib\Model;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 */
class User extends Model
{
    public function add(array $values)
    {
        $keys = array('name', 'password', 'email', 'salt');
        $diff = array_diff($keys, array_keys($values));

        // Checking if array has the required keys
        if (!empty($diff)) {
            return false;
        }

        $query = $this->database->prepare(
            'INSERT INTO users (name, email, password, salt)'.
            'VALUES (:name, :email, :password, :salt)'
        );

        $query->bindParam(':name', $values['name'], PDO::PARAM_STR);
        $query->bindParam(':email', $values['email'], PDO::PARAM_STR);
        $query->bindParam(':password', $values['password'], PDO::PARAM_STR);
        $query->bindParam(':salt', $values['salt'], PDO::PARAM_STR);

        return $query->execute();
    }

    public function delete($id)
    {
        $query = $this->database->prepare('DELETE FROM users WHERE id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return ($query->rowCount() === 1);
    }

    public function listAll()
    {
        $query = $this->database->query('SELECT name, email FROM users');
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists($email)
    {
        $query = $this->database->prepare(
            'SELECT * FROM users WHERE email = :email'
        );
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
    }
}
