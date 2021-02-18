<?php

class SkillController extends Controller
{

    public function __construct( $action = 'index' )
    {
        $model = 'Skill';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel( $model );
    }

    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'skills', $this->_model->getSkills() );
        /* render view and return */
        return $this->_view->output();
    }

}
