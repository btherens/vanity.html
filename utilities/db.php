<?php

class Db
{
    /* connected database name */
    public static $name;

    /* static mysqli object */
    private static $db;
    /* the calculated time zone offset to use as a session configuration when opening database connections */
    private static $TZoffset;

    /* return the live database connection in use for this session, or create one if we have not yet  */
    public static function init(): mysqli
    {
        /* only create new connection if we don't have one yet */
        if (!self::$db)
        {
            try
            {
                /* initiate sql object using deployment variables */
                self::$db = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
                /* check connection and throw fatal exception if we cannot connect */
                if ( mysqli_connect_errno() ) { die( 'Connection error: ' . mysqli_connect_error() ); }
                self::$db->query( "SET time_zone = '" . self::getTZoffset() . "';" );
            }
            /* throw connection issues */
            catch ( Exception $e ) { die( 'Connection error: ' . $e->getMessage() ); }
            /* set name property based on connection parameter */
            self::$name = DB_NAME;
        }
        /* return mysqli object */
        return self::$db;
    }
    /* determine server's timezone in hours offset from UTC and return */
    private static function getTZoffset(): string
    {
        /* calculate offset if it has not already been determined */
        if ( !self::$TZoffset )
        {
            /* use getOffset() method from DateTime to determine PHP session's time offset from UTC */
            $mins = ( new DateTime() )->getOffset() / 60;
            /* convert offset to formatted time zone offset syntax ( e.g. '-5:00' ) */
            self::$TZoffset = sprintf( '%+d:%02d', floor( $mins / 60 ), abs( $mins ) - ( floor( abs( $mins ) ) ) );
        }
        /* return result */
        return self::$TZoffset;
    }
}
