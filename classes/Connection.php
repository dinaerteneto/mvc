<?php

namespace classes;

class Connection
{

    public static $instance;

    private function __construct() {
        //
    }

    public static function getConnection() {
        if (!isset(self::$instance)) {
            self::$instance = new \PDO('mysql:host=127.0.0.1;dbname=capitani', 'root', '', [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_EMPTY_STRING);
        }

        return self::$instance;
    }
}