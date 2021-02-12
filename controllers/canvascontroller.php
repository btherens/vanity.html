<?php

class CanvasController extends Controller
{

    public $title = 'Starship Therapise';

    public function __construct($model, $action)
    {
        parent::__construct($model, $action);
        /* only use index view */
        $this->_setView($action);
        /* set base class */
        #$this->_setModel('');
    }

    /* route for single app system */
    public function main($pagecontent,$title,$shareimage,$description,$audioprop)
    {
        $this->_view->set('pagecontent', $pagecontent);
        # override title logic for home page
        $finaltitle = $title == 'Starship Therapise | Exploring Pop Culture, Fandom and Psychology' ? $title : $title . ' | ' . $this->title;
        $this->_view->set('title', $finaltitle);
        $this->_view->set('shareimage',$shareimage);
        $this->_view->set('description',$description);
        $this->_view->set('audioprop',$audioprop);

        return $this->_view->output();
    }

}
