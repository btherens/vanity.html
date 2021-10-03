<?php
/* define some simple properties to use when referencing files */
define( 'DS', DIRECTORY_SEPARATOR );
define( 'HOME', dirname( __FILE__ ) );
session_start();

/* includes necessary script files by object name, on demand and only if class has not already been loaded */
function basic_autoload( $class )
{
    /* step through each MVC directory and include script file once we find a matching path */
    foreach ( [ 'utilities', 'models', 'controllers' ] as $f ) { $fn = HOME . DS . $f . DS . strtolower( $class ) . '.php'; if ( file_exists( $fn ) ) { require_once $fn; break; } }
}
/* register autoload function with PHP to use when loading new modules */
spl_autoload_register( 'basic_autoload' );

/* load these modules with every request */
/* environment variables */
require_once HOME . DS . 'config.php';
/* run any commands at startup */
require_once HOME . DS . 'utilities' . DS . 'startup.php';
/* route the request through bootstrap */
require_once HOME . DS . 'utilities' . DS . 'bootstrap.php';
