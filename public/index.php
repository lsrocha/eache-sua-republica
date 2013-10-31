<?php
require '../src/EACHeRepublica/Model/User.php';

use EACHeRepublica\Model\User;

$test = new User();
print_r($test->listAll());
