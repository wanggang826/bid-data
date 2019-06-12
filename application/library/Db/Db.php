<?php


class Db_Db
{
    //database connection pool
    protected static $connPool = [];

    //db_Mysqli instance
    protected static $instance;

    //database configuration
    protected static $config = [];

    //current connection
    protected static $dbCurrent = null;

    protected function __construct($config)
    {
        //Todo
    }

    public static function GetDb()
    {
        return self::$instance;
    }

}