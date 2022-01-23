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
            /* step through each record in array and convert to regex pattern using generatePattern */
            function generatePattern( &$str ) { $str = '/(:[^:;\n]*)(var\(' . preg_quote( $str ) . '\))/'; }; array_walk( $matches[ 1 ], 'generatePattern' );
            /* ensure first match group remains where it was by prepending ${1} to replace text */
            function generateReplace( &$str ) { $str = '${1}' . $str; }; array_walk( $matches[ 2 ], 'generateReplace' );

            /* replace each var() with the css variable's true value */
            $newbuffer = preg_replace( $matches[1], $matches[2], $buffer );
            /* return buffer through text replacement so that css variables are replaced with their values */
            return preg_replace( $matches[1], $matches[2], $buffer );
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
