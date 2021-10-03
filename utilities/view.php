<?php

class View
{
    /* filepath to the view, assigned with an argument passed to constructor */
    protected $_file;
    /* data collection for variables required by view */
    protected $_data = [];

    /* constructor - set reference to supplied view */
    public function __construct( $file ) { $this->_file = $file; }

    /* getter for data properties */
    public function get( $key ) { return $this->_data[ $key ]; }

    /* setter for data properties */
    public function set( $key, $value ) { $this->_data[ $key ] = $value; }

    /* render view and return output */
    public function output()
    {
        /* throw exception if the view doesn't exist at path */
        if ( !file_exists( $this->_file ) ) { throw new Exception( "Template " . $this->_file . " doesn't exist." ); }
        /* extract variables stored in data collection */
        extract( $this->_data );
        /* store output in buffer */
        ob_start();
        /* interpret and load the view */
        include $this->_file;
        /* save the output buffer to output */
        $output = ob_get_contents();
        /* close the output buffer */
        ob_end_clean();
        /* return result from buffer */
        return $output;
    }
}
