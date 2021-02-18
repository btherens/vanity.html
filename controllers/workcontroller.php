<?php

class WorkController extends Controller
{

    public function __construct( $action = 'index' )
    {
        $model = 'Work';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel( $model );
    }

    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'works', $this->_model->getWorks() );
        /* render view and return */
        return $this->_view->output();
    }

}
