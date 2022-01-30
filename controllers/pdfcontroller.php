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

    /* list of allowed uri routes */
    private $_allow = [
        /* base url */
        ''
    ];
    /* generate pdf from given query */
    public function path( $query )
    {
        /* get URI */
        $uri = $query ? implode( '/', array_slice( $query, 0, -1 ) ) : '';
        /* determine path for caching */
        $path = implode( DS , preg_replace( '/[\W]/', '', array_slice( $query, 0, -1 ) ) );
        /* abort if uri is not whitelisted */
        if ( !in_array( $uri, $this->_allow ) ) { header( 'Location: /' ); exit; }
        /* send pdf created from uri and use path for caching */
        $this->_model->getPdf( $uri, $path );
    }

}
