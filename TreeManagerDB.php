<?php
abstract class TreeManagerDB {

    protected ?PDO $pdoConnection=NULL;
    protected array $dbJsonSchema = [];

    protected bool $connected = false;
    protected bool $empty = true;

    public function __construct(?PDO $pdoconn=NULL,) {

        if (!isset($pdoconn)) {$this->connectDB($pdoconn);}
        $this->getDBScheme();
    }

    public function connectDB(PDO $pdoconn=NULL,): bool
    {
        try {
            $this->empty = false ? $pdoconn->lastInsertId() : true;
            $this->connected = true;
            return true;
        }
        catch (PDOException $e) {
            $this->selfErrLog($e->getMessage());
            return false;
        }
    }

    public function getDBScheme() : bool {
        if(!$this->connected) {$this->selfErrLog("Database is not connected"); return false;}

        try {
            $result = $this->pdoConnection->query("SHOW tables")->fetchAll();
            $tables = [];
            foreach ($result as $value) {
                $tables[] = $value[0];
            }
            if (empty($tables)) {$this->selfErrLog("Database has no schema"); return false;}

            $columns = [];
            $num = 0;

            foreach($tables as $value){
                $rs = $this->pdoConnection->query('SELECT * FROM '.$value.' LIMIT 0');
                for ($i = 0; $i < $rs->columnCount(); $i++) {
                    $col = $rs->getColumnMeta($i);
                    $columns[$num][$col['name']] = $col['native_type'] == "LONG" || $col['native_type'] == "TINY"
                        ? "number" : "string";
                };
                $num++;
            }
            $this->dbJsonSchema = $columns;
            if (empty($this->dbJsonSchema)) {$this->selfErrLog("Database has no schema"); return false;}

            return true;

            } catch (Exception $e) {
            $this->selfErrLog($e->getMessage());
            return false;
        }

    }

    public function getStatus() : void {
        echo "Connected: ".$this->connected."\n";
        echo "Empty: ".$this->empty."\n";
        if(!empty($this->dbJsonSchema)) {print_r(json_encode($this->dbJsonSchema));}
    }

    abstract public function Create();

    public function Read() {
        $this->checkCRUDcondition();
    }

    public function Update() {
        $this->checkCRUDcondition();
    }

    public function Delete() {
        $this->checkCRUDcondition();
    }

    private static function selfErrLog(string $message): void
    { error_log(3, $message."\n",
        dirname(__FILE__)."/log.txt");
    }

    private static function selfExceptions(string $message) : void {
        throw new Exception($message);
    }

    private function checkCRUDcondition() : void {
        if(!$this->connected) {
            $this->selfErrLog("Database is not connected");
            $this->selfExceptions("Database is not connected");
        }
        if (empty($this->dbJsonSchema)) {
            $this->selfErrLog("Database has no schema");
            $this->selfExceptions("Database has no schema");
        }
    }

}