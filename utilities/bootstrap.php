<?php
/* default values */
$controller = 'canvas';
$action = 'index';
$query = array(null);

/* determine if the client request was ajax */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false) : false;

/* override defaults if uri provided */
if (isset($_GET['load']))
{
    /* collect parameters from GET object */
    $params = array(); $params = explode("/", $_GET['load']);
    /* the first parameter from web request is the controller to use */
    $controller = ucwords($params[0]);
    /* set the second argument from GET to $action (the method being called) */
    if ( isset( $params[1] ) && !empty( $params[1] ) ) { $action = $params[1]; }
    /* collect any remaining arguments from params in query object */
    if ( isset( $params[2] ) && !empty( $params[2] ) ) { $query = array_slice($params, 2); }
}
$modelName = $controller;
$controller .= 'Controller';

/* load the class, if it exists */
if   ( class_exists( $controller ) ) { $load = new $controller($modelName, $action); }
/* throw an error if it doesn't */
else { http_response_code(404); die('please check your url'); }

/* proceed if the method exists */
if (method_exists($load, $action))
{
    /*
     * single page system
     * send direct request to controller if ajax request
     */
    if ( !$isAjax )
    {
        /* run the method called by this request */
        /* $output = $load->$action($query); */
        /* create the single page object */
        $canvas = new CanvasController('Canvas','main');
        /* execute main method (generage page response) */
        $output = $canvas->main();
    }
    /*
     * direct route (no app chrome) for:
     * ajax requests
     * routes that aren't using the app window
     */
    else
    {
        switch ( count( $query ) )
        {
            case 3:
                $output = $load->$action($query[0], $query[1], $query[2]);
                break;
            case 2:
                $output = $load->$action($query[0], $query[1]);
                break;
            case 1:
                $output = $load->$action($query[0]);
                break;
        }
    }
}
/* throw an error if we couldn't find a method that matched request */
else { http_response_code(404); die('Invalid method. Please check the URL.'); }

/* send response */
echo $output;
