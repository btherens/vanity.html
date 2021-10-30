<?php

class PdfModel extends Model
{

    /* constructor */
    public function __construct() {
        parent::__construct();
    }
    /* renders a $url to pdf and return path */
    private function renderPdf( $url ): string
    {
        $path = uniqid() . '.pdf';
        $agent = 'internal-pdf-render';
        try
        {
            /* run shell command */
            shell_exec( "wkhtmltopdf -T 5 -L 5 -R 5 -B 5 --custom-header 'User-Agent' '" . $agent . "' --custom-header-propagation --zoom 0.9 localhost '" . $this->basepath() . $path . "'" );
            //$path = shell_exec( 'utilities/vanityPrint.sh ' . $url . ' ' . $this->basepath() . 'pdf' );
        }
        /* if the query fails, pass to createTable method and then bail */
        catch ( Exception $e ) { http_response_code(400); echo 'errors encountered while rendering pdf'; header( 'Location: ./' ); exit; }
        return $path;
    }

    /* render pdf and return */
    public function getPdf( $url )
    {   
        /* render pdf to file */
        $path = $this->renderPdf( $url );
        /* set proper document header */
        header( 'Content-type: application/pdf' );
        /* read pdf to output buffer */
        try     { $this->getFile( $path ); }
        /* throw exception and exit */
        catch   ( Exception $e ) { http_response_code(400); echo 'errors encountered while retrieving pdf'; header( 'Location: ./' ); exit; }
        /* clean up temporary files */
        // finally { $this->dropFile( $path ); }
    }

}
