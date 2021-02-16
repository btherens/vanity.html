<?php

class CanvasController extends Controller
{

    public $title = 'test title';

    public function __construct( $action )
    {
        $model = 'Canvas';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        // $this->_setModel($model);
    }

    /* route for single app system */
    public function index()
    {
        /* set variables to view */
        $this->_view->set( 'title', $this->title );
        $profile = new ProfileController('index');
        $this->_view->set( 'profile', $profile->index() );
        /* send view output to response */
        echo $this->_view->output();
    }

}
