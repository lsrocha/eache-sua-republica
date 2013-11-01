<?php
require realpath('../config/core.php');
require ROOT.DS.'config'.DS.'database.php';
require ROOT.DS.'vendor'.DS.'autoload.php';

use EACHeRepublica\Model\User;

$test = new User();
// var_dump($test->delete(18));
// print_r($test->listAll());
