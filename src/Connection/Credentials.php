<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 12/09/2018
 * Time: 11:33
 */

namespace App\Connection;

class Credentials
{
    protected static $host;
    protected static $dbName;
    protected static $user;
    protected static $password;
    protected static $driver;
    protected static $charset;

    /**
     * Credentials constructor.
     */
    public function __construct()
    {
        define("ENVIRONMENT", 'DEV');

        switch (ENVIRONMENT) {
            case 'DEV':
                self::$host = 'mysql.a4quality.com';
                self::$dbName = 'a4quality06';
                self::$user = 'a4quality06';
                self::$password = 'WYJBKlHQHEDjm76i';
                self::$driver = 'pdo_mysql';
                self::$charset = 'utf8';
                break;
            case 'UAT':
                self::$host = 'mysql.a4quality.com';
                self::$dbName = 'a4quality04';
                self::$user = 'a4quality04';
                self::$password = 'GTkprzBIe80aTrQ';
                self::$driver = 'pdo_mysql';
                self::$charset = 'utf8';
                break;
            case 'PROD_OLD':
                self::$host = 'mysql.a4quality.com';
                self::$dbName = 'a4quality03';
                self::$user = 'a4quality03';
                self::$password = 'xqDJGyrBcMVvz7AFw8YDCK1';
                self::$driver = 'pdo_mysql';
                self::$charset = 'utf8';
                break;
            case 'PROD':
                self::$host = 'mysql.a4quality.com';
                self::$dbName = 'a4quality07';
                self::$user = 'a4quality07';
                self::$password = 'whllYjWoC2goypBrqBuaM6VXMiGTRp';
                self::$driver = 'pdo_mysql';
                self::$charset = 'utf8';
                break;
        }
    }

    /**
     * @return string
     */
    public static function getHost()
    {
        if (!self::$host) {
            new Credentials();
        }
        return self::$host;
    }

    /**
     * @return string
     */
    public static function getDbname()
    {
        if (!self::$dbName) {
            new Credentials();
        }
        return self::$dbName;
    }

    /**
     * @return string
     */
    public static function getUser()
    {
        if (!self::$user) {
            new Credentials();
        }
        return self::$user;
    }

    /**
     * @return string
     */
    public static function getPassword()
    {
        if (!self::$password) {
            new Credentials();
        }
        return self::$password;
    }

    /**
     * @return string
     */
    public static function getDriver()
    {
        if (!self::$driver) {
            new Credentials();
        }
        return self::$driver;
    }

    /**
     * @return string
     */
    public static function getCharset()
    {
        if (!self::$charset) {
            new Credentials();
        }
        return self::$charset;
    }
}
