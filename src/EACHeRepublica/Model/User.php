<?php
namespace EACHeRepublica\Model;

require '/srv/www/EACHe-sua-republica/src/EACHeRepublica/Lib/Model.php';

use EACHeRepublica\Lib\Model;
use \PDO;

/**
 * @author Leonardo Rocha <leonardo.lsrocha@gmail.com>
 */
class User extends Model
{
    public function add()
    {
    }

    public function listAll()
    {
        $query = $this->database->query('SELECT name, email FROM users');
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
