<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config
{
    /**
     * Database name
     * @return string
     */
    public static function DB_NAME()
    {
        return 'restaurant_db';
    }

    /**
     * Database port
     * @return int
     */
    public static function DB_PORT()
    {
        return 3306;
    }

    /**
     * Database username
     * @return string
     */
    public static function DB_USER()
    {
        return 'root';
    }

    /**
     * Database password
     * @return string
     */
    public static function DB_PASSWORD()
    {
        return '';
    }

    /**
     * Database host
     * @return string
     */
    public static function DB_HOST()
    {
        return '127.0.0.1';
    }

    /**
     
     * @return string
     */
    public static function JWT_SECRET()
    {
        return 'your_secret_key_change_this_in_production';
    }
}
?>