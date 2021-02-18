<?php

class CanvasController extends Controller
{

    public function __construct( $action )
    {
        $model = 'Canvas';
        parent::__construct( $model, $action );
        $this->_setView( $action );
        $this->_setModel($model);
    }

    /* route for single app system */
    public function index()
    {
        /* create instances of other controllers here and render their view content*/
        $profile = new ProfileController();
        $this->_view->set( 'title', $profile->name() );
        $this->_view->set( 'profile', $profile->index() );

        $skill = new SkillController();
        $this->_view->set( 'skill', $skill->index() );

        $language = new LanguageController();
        $this->_view->set( 'language', $language->index() );

        $work = new WorkController();
        $this->_view->set( 'work', $work->index() );

        $education = new EducationController();
        $this->_view->set( 'education', $education->index() );

        /* set source code URL to view */
        $this->_view->set( 'sourcecodeurl', $this->_model->getSourceCodeUrl() );

        /* render view and return */
        return $this->_view->output();
    }

}
