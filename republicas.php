<?php
session_start();

require 'includes/autoloader.php';

use core\Database;
use core\Republicas; 
use core\Authentication;

if (isset($_GET['lat'], $_GET['lng'], $_GET['r'])) {
    try {
        $database = new Database();
        $json = Republicas::getRepublicas(
            $_GET['lat'],
            $_GET['lng'],
            $_GET['r'],
            $database
        );

        echo html_entity_decode($json);
        $database = null;
    } catch (PDOException $e) {
        echo $e;
    }
}

