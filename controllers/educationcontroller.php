<?php

class EducationController extends Controller
{

    public function __construct( $action = 'index' )
    {
        $model = 'Education';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel( $model );
    }

    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'educations', $this->_model->getEducations() );
        /* render view and return */
        return $this->_view->output();
    }

}
