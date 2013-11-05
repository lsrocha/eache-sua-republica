<?php
namespace EACHeRepublica\Controller;

use EACHeRepublica\Lib\Controller;
use EACHeRepublica\Model\User;

class Users extends Controller
{
    private $model = null;

    public function __construct()
    {
        parent::__construct();
        $this->model = new User();
    }

    public function index()
    {
        return $this->view->render('test.php');
    }
}
