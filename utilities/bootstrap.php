<?php
/* default values */
$controller = 'Home';
$action = 'index';
$query = array(null);

/* determine if the client request was ajax */
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false) : false;

/* override defaults if uri provided */
if (isset($_GET['load']))
{
    $params = array();
    $params = explode("/", $_GET['load']);

    $controller = ucwords($params[0]);

    if (isset($params[1]) && !empty($params[1]))
    {
        $action = $params[1];
    }

    if (isset($params[2]) && !empty($params[2]))
    {
        $query = array_slice($params, 2);
    }
}
$modelName = $controller;
$controller .= 'Controller';

/* routing pre-processes */
if (strtolower($controller) == 'rsscontroller')
{
    if (strtolower($action) != 'index')
    {
        $query = array($action);
        $action = 'detail';
    }
}
/* routing pre-processes */

/* load the class, if it exists */
if (class_exists($controller))
{
    $load = new $controller($modelName, $action);
}
else
{
    http_response_code(404);
    die('please check your url');
}

if (method_exists($load, $action))
{
    /*
     * single page system
     * array is a allowlist of routes that should use main app window
     * send direct request to controller if ajax request
     */
    if
    (
        (
            /* FeedsController is run through single page app */
            in_array($controller, array('FeedsController')) ||
            /* case for the account controller and any applicable methods */
            ( $controller == 'AccountController' && in_array( $action, array( 'index', 'new' ) ) )
        /* reject single page system if the request is ajax */
        ) && !$isAjax
    )
    {
        $output = $load->main($action, $query);

        /* single page navigation */
        $mainwindow = new HomeController('Home','main');
        $output = $mainwindow->main($output,$modelName);
    }
    /*
     * direct route (no app chrome) for:
     * ajax requests
     * routes that aren't using the app window
     */
    else
    {
        switch (count($query))
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
else
{
    http_response_code(404);
    die('Invalid method. Please check the URL.');
}

/* send response */
echo $output;
