<style>
    <?php
        /* detect internal rendering requests to serve alternate styles */
        $printmode = (bool) ISSET( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? ( $_SERVER[ 'HTTP_USER_AGENT' ] == 'internal-pdf-render' ) : false;
        function evalCssVars( string $buffer ): string
        {
            $matches = [];
            /* get variable definitions */
            $cssVars = preg_match_all( '/^\s*(--\S+):\s*(.+);\s*$/m', $buffer, $matches );
            // (?<=:\s)var\(--canvas-accentcolor1\)(?=\s*;)
            function evalPregPat( &$str ) { $str = '/(?<=:\s)var\(' . preg_quote( $str ) . '\)(?=\s*;)/'; }
            array_walk( $matches[ 1 ], 'evalPregPat' );

            $newbuffer = preg_replace( $matches[1], $matches[2], $buffer );
            return $newbuffer;
        }
        if ( $printmode ) { ob_start( 'evalCssVars' ); }
        /* embed style files directly */
        include 'css/variable.css';
        include 'css/responsive.css';
        include 'css/font.css';
        include 'css/element.css';
        include 'css/layout.css';
        include 'css/icon.css';
        if ( $printmode ) { ob_end_flush(); }
    ?>
</style>
