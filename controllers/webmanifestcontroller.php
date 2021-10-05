<?php

class WebmanifestController extends Controller
{

    /* constructor */
    public function __construct( $action = 'index' ) { parent::__construct( '', $action ); }

    /* return .webmanifest compatible document */
    public function index()
    {
        /* use profile controller to fill web manifest metadata */
        $profile = new ProfileController();
        /* set manifest object */
        $manifest = [
            'lang' => 'en',
            'dir' => 'ltr',
            'name' => $profile->name(),
            'description' => $profile->occupation(),
            'icons' => [ [
                'src' => '/asset/icon_solid.png',
                'sizes' => '96x96 128x128 144x144',
                'type' => 'image/png'
            ], ],
            'scope' => '/',
            'start_url' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
        ];
        /* manifest type headers */
        header( 'Content-Type: application/manifest+json' );
        /* encode and return response */
        return json_encode( $manifest );
    }

}
