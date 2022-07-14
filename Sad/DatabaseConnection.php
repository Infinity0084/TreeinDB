<?php

require_once ("config.php");

class DatabaseConnection
{
    private static ?DatabaseConnection $instance=null;
    private ?PDO $dbConn=null;


    private function __construct() {
            $this->dbConn = new PDO(DB.":host=".db_HOST.";port=".db_PORT.";dbname=".db_NAME, db_USERNAME,
                db_PASSWORD, db_OPTIONS);
    }

    private function __clone(){
        throw new Exception("Can't clone Singleton");
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new DatabaseConnection();
        }

        return self::$instance->getConnection();
    }

    private function getConnection() {
        return $this->dbConn;
    }

}