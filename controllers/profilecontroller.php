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
    /* render index view with values from model and return */
    public function index()
    {
        /* map model properties to view variables */
        $this->_view->set( 'name', $this->_model->getName() );
        $this->_view->set( 'occupation', $this->_model->getOccupation() );
        $this->_view->set( 'home', $this->_model->getHome() );
        /* render view and return */
        return $this->_view->output();
    }
    /* return the name from profile */
    public function name() { return $this->_model->getName(); }
    /* return the occupation from profile */
    public function occupation() { return $this->_model->getOccupation(); }

    /*  */
    public function refresh()
    {
        $timestamp = isset($_POST['t']) ? $_POST['t'] : null;
        if ( $timestamp )
        {

        }

        $result = $this->_model->_checkKeyAvailability($type, $feedkey, $episodekey);

        $response = new stdClass();
        $response->key = ($type == 'feed') ? $feedkey : $episodekey;
        $response->available = $result;

        $responseJSON = json_encode($response);
        header("Content-type: application/json; charset=utf-8");
        return $responseJSON;
    }

}
