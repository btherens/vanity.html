<?php

class ProfileController extends Controller
{

    public function __construct( $action = 'index' )
    {
        $model = 'Profile';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel( $model );
    }

    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'name', $this->_model->getName() );
        $this->_view->set( 'occupation', $this->_model->getOccupation() );
        $this->_view->set( 'home', $this->_model->getHome() );
        /* render view and return */
        return $this->_view->output();
    }

}
