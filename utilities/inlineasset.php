<?php
/* static class that reads asset text to output */
class InlineAsset
{
    /* root path for assets */
    private static $_path = 'asset/';

    /* get svg from resource and read to output */
    public static function svg( string $name ): void { readfile( self::$_path . $name . '.svg' ); }
}
