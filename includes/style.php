<style>
    <?php
        /* detect internal rendering requests to serve alternate styles */
        $printmode = (bool) ISSET( $_SERVER['HTTP_USER_AGENT'] ) ? ( $_SERVER['HTTP_USER_AGENT'] == 'internal-pdf-render' ) : false;
        /* embed style files directly  */
        include 'css/variable.css';
        if ( $printmode ) { include 'css/responsive-print.css'; } else { include 'css/responsive.css'; }
        include 'css/font.css';
        include 'css/element.css';
        if ( $printmode ) { include 'css/layout-print.css'; } else { include 'css/layout.css'; }
        include 'css/icon.css';
    ?>
</style>
