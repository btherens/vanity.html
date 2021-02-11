<?php

class HomeController extends Controller
{

    public function __construct($model, $action)
    {
        parent::__construct('home', $action);
        /* only use index view */
        $this->_setView('index');
        /* set base class */
        $this->_setModel('');
        $this->_requireAccount();
    }

    /* redirect if page is empty */
    public function index()
    {
        header('Location: /feeds');
        exit;
    }

    /* route for single app system */
    public function main($pagecontent, $active)
    {
        $this->_view->set('title', 'rss');
        $this->_view->set('pagecontent', $pagecontent);
        $this->_view->set('active', $active);

        if (Modal::exists())
        {
            $this->setModal();
        }

        return $this->_view->output();
    }

    private function setModal()
    {
        $this->_view->set('modalcontent', Modal::setString());
    }

}
