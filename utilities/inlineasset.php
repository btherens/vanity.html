<?php

class InlineAsset
{
    /* root path for assets */
    private static $_path = 'asset/';

    /* get svg from resource and read to output buffer */
    public static function svg(string $name): void
    { readfile( self::$_path . $name . '.svg' ); }

}
