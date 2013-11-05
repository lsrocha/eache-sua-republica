<?php
require realpath('../config/core.php');
require ROOT.DS.'config'.DS.'database.php';
require ROOT.DS.'vendor'.DS.'autoload.php';

use EACHeRepublica\Controller\Users;

$test = new Users();

header('Content-Type: text/html; charset=utf-8;');
header('Content-Encoding: gzip;');

echo $test->index();

// var_dump($test->add($data));

// var_dump($test->delete(18));
// print_r($test->listAll());
