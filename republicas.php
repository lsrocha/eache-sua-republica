<?php
session_start();

require 'includes/autoloader.php';

use core\Republicas; 
use core\Authentication;

if (isset($_GET['list'], $_GET['lat'], $_GET['lng'], $_GET['r'], $_GET['n'])) {
    $latitude = filter_var($_GET['lat'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_var($_GET['lng'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $radius = filter_var($_GET['r'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $numberRepublicas = filter_var($_GET['n'], FILTER_SANITIZE_NUMBER_INT);
    
    $valid = (bool) filter_var($latitude, FILTER_VALIDATE_FLOAT);
    $valid &= (bool) filter_var($longitude, FILTER_VALIDATE_FLOAT);
    $valid &= (bool) filter_var($radius, FILTER_VALIDATE_FLOAT);
    $valid &= (bool) filter_var($numberRepublicas, FILTER_VALIDATE_INT);
    
    if ($valid) {
        $republicas = new Republicas();
        echo $republicas->getRepublicas($latitude, $longitude, $radius, $numberRepublicas);
    }
} elseif (isset($_GET['insert']) && $_GET['insert']) {
    if (!Authentication::isLoggedIn()) {
        header('Location: index.php?goback='.$_SERVER['PHP_SELF']);
    }

    $republicas = new Republicas();
    $republicas->addRepublica($_POST, $_SESSION['user_id']);
}

