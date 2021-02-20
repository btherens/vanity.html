<?php

class MethodController extends Controller
{

    public function __construct( $action = 'index' )
    {
        $model = 'Method';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel( $model );
    }

    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'methods', $this->_model->getMethods() );
        /* render view and return */
        return $this->_view->output();
    }

}
