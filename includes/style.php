<style>
    <?php
        /* detect internal rendering requests to serve alternate styles */
        $printmode = (bool) ISSET( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? ( $_SERVER[ 'HTTP_USER_AGENT' ] == 'internal-pdf-render' ) : false;

        /* evaluate CSS variables for print server to support older browsing engine */
        function evalCssVars( string $buffer ): string
        {
            $matches = [];
            /* get variable definitions */
            $cssVars = preg_match_all( '/^\s*(--\S+):\s*(.+);\s*$/m', $buffer, $matches );
            /* convert key into regex match pattern */
            function evalPregPat( &$str ) { $str = '/(?<=:\s)var\(' . preg_quote( $str ) . '\)(?=\s*;)/'; }
            /* step through each record in array and convert to regex pattern using evalPregPat */
            array_walk( $matches[ 1 ], 'evalPregPat' );

            /* replace each var() with the css variable's true value */
            $newbuffer = preg_replace( $matches[1], $matches[2], $buffer );
            /* return buffer after alteration is complete */
            return $newbuffer;
        }
        /* catch included css files in buffer and postprocess with evalCssVars */
        if ( $printmode ) { ob_start( 'evalCssVars' ); }
        /* embed style files */
        include 'css/variable.css';
        include 'css/responsive.css';
        include 'css/font.css';
        include 'css/element.css';
        include 'css/layout.css';
        include 'css/icon.css';
        /* run included css through evalCssVars for print styles */
        if ( $printmode ) { ob_end_flush(); }
    ?>
</style>
