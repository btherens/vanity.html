<?php

class Db
{

    public static $name;

    private static $db;
    private static $TZoffset;

    public static function init()
    {
        if (!self::$db)
        {
            try
            {
                self::$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                /* check connection */
                if (mysqli_connect_errno())
                {
                    die('Connection error: ' . mysqli_connect_error());
                }
                self::$db->query("SET time_zone = '" . self::getTZoffset() . "';");
            }
            catch (Exception $e)
            {
                die('Connection error: ' . $e->getMessage());
            }
            self::$name = DB_NAME;
        }
        return self::$db;
    }

    private static function getTZoffset()
    {
        if (!self::$TZoffset)
        {
            $now = new DateTime();
            $mins = $now->getOffset() / 60;
            $sgn = ($mins < 0 ? -1 : 1);
            $mins = abs($mins);
            $hrs = floor($mins / 60);
            $mins -= $hrs * 60;
            self::$TZoffset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
        }
        return self::$TZoffset;
    }
}
