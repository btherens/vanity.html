<?php
/* default values */
$controller = 'canvas';
$action = 'index';
$query = [];

/* whitelist for web (non-ajax) requests */
$webroutes = [ 'canvas', 'webmanifest', 'pdf' ];

/* override defaults if uri provided */
if ( isset( $_GET[ 'load' ] ) )
{
    /* collect parameters from GET object */
    $params = []; $params = explode( '/', $_GET[ 'load' ] );
    /* redirect default controller to base url */
    if   ( $controller == strtolower( $params[ 0 ] ) ) { header( 'Location: /' ); exit; }
    /* the first parameter from web request is the controller to use (default controller redirects to base url) */
    else { $controller = ucwords( $params[ 0 ] ); }
    /* set the second argument from GET to $action (the method being called) */
    if   ( isset( $params[ 1 ] ) && !empty( $params[ 1 ] ) ) { $action = $params[ 1 ]; }
    /* collect any remaining arguments from params in query object */
    if   ( isset( $params[ 2 ] ) && !empty( $params[ 2 ] ) ) { $query = array_slice( $params, 2 ); }
}

/* determine if the request is AJAX */
$isAjax = isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ? ( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] == 'XMLHttpRequest' ? true : false ) : false;
/* redirect to homepage if the request wasn't in whitelist array */
if ( !$isAjax && !in_array( strtolower( $controller ), $webroutes ) ) { header( 'Location: /' ); exit; };

/* normalize request value to controller naming convention */
$controller .= 'Controller';

/* load the class, if it exists */
if   ( class_exists( $controller ) ) { $load = new $controller( $action ); }
/* throw an error if it doesn't */
else { http_response_code( 404 ); die( 'please check your url' ); }

/* execute method and send result to output */
if   ( method_exists( $load, $action ) ) { echo $load->$action( $query ); }
/* throw an error if we couldn't find a method that matched request */
else { http_response_code( 404 ); die( 'Invalid method. Please check the URL.' ); }
