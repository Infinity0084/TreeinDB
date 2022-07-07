<?php

class DatabaseConnection
{
    private static ?DatabaseConnection $instance=null;
    private ?PDO $dbConn=null;

    private array $dbJsonSchema = [];

    private function __construct() {
        $this->setConnection();
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

    public function setConnection($filename="config.txt") {
        try {
        $config = file_get_contents($filename);
        $config = json_decode($config, true);

            $this->dbConn = new PDO("$config[db]:host=$config[host];port=$config[port];dbname=$config[dbname]",
                $config["username"], $config["password"]);
        } catch (Exception $e) {
            error_log(3, $e->getMessage()."\n", dirname(__FILE__)."/log.txt");
        }
    }

}