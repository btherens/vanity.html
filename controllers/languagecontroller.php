<?php

class LanguageController extends Controller
{

    public function __construct( $action = 'index' )
    {
        $model = 'Language';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel( $model );
    }

    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'languages', $this->_model->getLanguages() );
        /* render view and return */
        return $this->_view->output();
    }

}
