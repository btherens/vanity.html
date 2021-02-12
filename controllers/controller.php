<?php

class Controller
{
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_view;
    protected $_modelBaseName;

    /* collection for multiple views */
    protected $_views;

    public function __construct($model, $action)
    {
        $this->_controller = ucwords(__CLASS__);
        $this->_action = $action;
        $this->_modelBaseName = $model;
    }

    protected function _setModel($modelName)
    {
        $modelName .= 'Model';
        $this->_model = new $modelName();
    }

    protected function _setView($viewName)
    {
        if (!isset($this->_views[$viewName]))
        {
            $this->_views[$viewName] = new View(HOME . DS . 'views' . DS . strtolower($this->_modelBaseName) . DS . $viewName . '.php');
        }
        $this->_view = &$this->_views[$viewName];
    }
}
