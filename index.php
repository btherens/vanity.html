<?php
define('DS', DIRECTORY_SEPARATOR);
define('HOME', dirname(__FILE__));
session_start();

function basic_autoload($class)
{
    if (file_exists(HOME . DS . 'utilities' . DS . strtolower($class) . '.php'))
    {
        require_once HOME . DS . 'utilities' . DS . strtolower($class) . '.php';
    }
    else if (file_exists(HOME . DS . 'models' . DS . strtolower($class) . '.php'))
    {
        require_once HOME . DS . 'models' . DS . strtolower($class) . '.php';
    }
    else if (file_exists(HOME . DS . 'controllers' . DS . strtolower($class) . '.php'))
    {
        require_once HOME . DS . 'controllers' . DS . strtolower($class) . '.php';
    }
}
spl_autoload_register("basic_autoload");

require_once HOME . DS . 'config.php';
require_once HOME . DS . 'utilities' . DS . 'startup.php';
require_once HOME . DS . 'utilities' . DS . 'bootstrap.php';
