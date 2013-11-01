<?php
require realpath('../config/core.php');
require ROOT.DS.'config'.DS.'database.php';
require ROOT.DS.'vendor'.DS.'autoload.php';

use EACHeRepublica\Model\User;

$test = new User();

$data = array(
    'name' => 'Leonardo Santos Rocha',
    'email' => 'leonardo@outlook.com.br',
    'password' => 'blablabla1',
    'salt' => 'saltinbancos',
    'novo' => 'algumcoisa'
);

var_dump($test->add($data));

// var_dump($test->delete(18));
// print_r($test->listAll());
