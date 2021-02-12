<?php

class CanvasController extends Controller
{

    public $title = 'test title';

    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        $this->_setView($action);
        // $this->_setModel($model);
    }

    /* route for single app system */
    public function index()
    {
        /* set variables to view */
        $this->_view->set( 'title', $this->title );
        /* send view output to response */
        echo $this->_view->output();
    }

}
