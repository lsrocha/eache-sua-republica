<?php
function autoload($class)
{
    $class = ltrim($class, '\\');
    $file = '';
    $namespace = '';

    if ($lastSeparator = strripos($class, '\\')) {
        $namespace = substr($class, 0, $lastSeparator);
        $class = substr($class, $lastSeparator + 1);
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
    }

    $file .= $class.'.php';

    require $file; 
}

spl_autoload_register('autoload');

