<?php
namespace EACHeRepublica\Lib;

use EACHeRepublica\Lib\View;

class Controller
{
    protected $view = null;

    public function __construct()
    {
        $this->view = new View();
    }
}
