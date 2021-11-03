<?php

class PdfModel extends Model
{

    /* constructor */
    public function __construct() { parent::__construct(); }

    /* store website url here */
    private string $_url;
    /* properties loaded into memory here */
    private array $_prop;

    /* renders a $url to pdf and return path */
    private function renderPdf(): string
    {
        /* establish basic command object */
        $cmd = [ 'u' => $this->_url, 'd' => $this->basepath() . 'pdf' ];
        /* add optional parameters if we were able to detect them */
        if    ( isset( $this->_prop[ 'title' ] ) ) { $cmd[ 't' ] = $this->_prop[ 'title' ]; }
        if    ( isset( $this->_prop[ 'description' ] ) ) { $cmd[ 's' ] = $this->_prop[ 'description' ]; }
        /* call shell function */
        try   { $result = $this->_shellExec( 'utilities/vanityPrint.sh', $cmd ); }
        /* throw exceptions if pdf execution failed */
        catch ( Exception $e ) { http_response_code( 400 ); echo 'errors encountered while rendering pdf'; header( 'Location: ./' ); exit; }
        /* return last returned value from shell */
        return end( $result );
    }

    private function loadHtml( $url ): void
    {
        /* use a DomDocument class for html processing */
        $dom = new DOMDocument;
        /* ignore libxml warnings */
        libxml_use_internal_errors( true );
        /* load the html */
        $dom->loadHTMLFile( $url );
        /* declare properties object */
        $prop = [];
        /* load meta tags */
        foreach ( $dom->getElementsByTagName( 'meta' ) as $tag ) { $prop[ str_replace( 'og:', '', $tag->getAttribute( 'property' ) ) ] = $tag->getAttribute( 'content' ); }
        /* set properties */
        $this->_url = $url;
        $this->_prop = $prop;
    }

    /* render pdf and return */
    public function getPdf( $url ): void
    {
        /* load html into file */
        $this->loadHtml( $url ); 
        /* render pdf to file */
        $path = $this->renderPdf();
        /* set proper document header */
        header( 'Content-type: application/pdf' );
        /* generate filename from $url */
        header( 'Content-Disposition: inline; filename="' . trim( preg_replace( '/[^a-z0-9]+/', '-', preg_replace( '#^[^:/.]*[:/]+#i', '', strtolower( $url ) ) ), '-' ) . '"' );
        /* read pdf to output buffer */
        try     { readfile( $path ); }
        /* throw exception and exit */
        catch   ( Exception $e ) { http_response_code( 400 ); echo 'errors encountered while retrieving pdf'; header( 'Location: ./' ); exit; }
    }

}
