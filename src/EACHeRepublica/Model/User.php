<?php
namespace EACHeRepublica\Model;

use \PDO;
use EACHeRepublica\Lib\Model;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 */
class User extends Model
{
    public function add()
    {
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
