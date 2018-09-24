<?php

/**
 * Define the way to instanciate a class.
 * 
 * @param string $sClassname The class name
 */
spl_autoload_register(function ($sClassName) {
    
    $sClassName = strtr($sClassName, '\\', DIRECTORY_SEPARATOR);
    include_once '../'.$sClassName . '.php';
});

