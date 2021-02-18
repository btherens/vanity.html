<?php
/* default values */
$controller = 'canvas';
$action = 'index';
$query = array( null );

/* determine if the client request was ajax */
$isAjax = isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false ) : false;

/* override defaults if uri provided */
if ( isset( $_GET['load'] ) )
{
    /* collect parameters from GET object */
    $params = array(); $params = explode( '/', $_GET['load'] );
    /* the first parameter from web request is the controller to use */
    $controller = ucwords( $params[0] );
    /* set the second argument from GET to $action (the method being called) */
    if ( isset( $params[1] ) && !empty( $params[1] ) ) { $action = $params[1]; }
    /* collect any remaining arguments from params in query object */
    if ( isset( $params[2] ) && !empty( $params[2] ) ) { $query = array_slice( $params, 2 ); }
}
/* normalize request value to controller naming convention */
$controller .= 'Controller';

/* load the class, if it exists */
if   ( class_exists( $controller ) ) { $load = new $controller( $action ); }
/* throw an error if it doesn't */
else { http_response_code(404); die('please check your url'); }

/* execute method and send to output buffer */
if   ( method_exists( $load, $action ) ) { echo $load->$action( $query ); }
/* throw an error if we couldn't find a method that matched request */
else { http_response_code(404); die('Invalid method. Please check the URL.'); }
