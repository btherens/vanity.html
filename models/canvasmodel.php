<?php

class CanvasModel extends Model
{

    private ?string $_sourcecodeurl;

    /* constructor */
    public function __construct() {
        /* use parent constructor */
        parent::__construct();
        /* save sourcecode url to model if one is defined */
        $this->_sourcecodeurl = defined( 'SOURCECODEURL' ) ? SOURCECODEURL : null;
    }

    /* get sourcecodeurl */
    public function getSourceCodeUrl(): ?string { return $this->_sourcecodeurl; }

}
