<?php

class PdfController extends Controller
{

    /* constructor */
    public function __construct( $action )
    {
        $model = 'Pdf';
        parent::__construct( $model, $action );
        #$this->_setView( $action );
        $this->_setModel( $model );
    }

    /* generate pdf */
    public function index()
    {
        $this->_model->getPdf( BASEDNS );
    }

}
