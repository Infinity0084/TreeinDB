<?php

require_once ("config.php");

class DatabaseConnection
{
    private static ?DatabaseConnection $instance=null;
    private ?PDO $dbConn=null;


    private function __construct() {
        try {
            $this->dbConn = new PDO(DB.":host=".db_HOST.";port=".db_PORT.";dbname=".db_NAME, db_USERNAME,
                db_PASSWORD, db_OPTIONS);
        } catch (Exception $e) {
            error_log(3, $e->getMessage()."\n", dirname(__FILE__)."/log.txt");
        }
    }

    private function __clone(){
        throw new Exception("Can't clone Singleton");
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new DatabaseConnection();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->dbConn;
    }

}