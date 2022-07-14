<?php

require_once ("DatabaseConnection.php");

class TableManager
{
    private string $tableName;
    private array $tableScheme;

    public function __construct($tableName) {
       $this->tableName = $tableName;
    }

    public function getTableScheme() : array {
        if(empty($this->tableScheme)) {
            $dbConn = DatabaseConnection::getInstance();
            $result = $dbConn->query("SHOW tables")->fetchAll();

            $tables = [];
            foreach ($result as $value) {
                if($value[0]==$this->tableName) {$tables[] = $value[0]; break;}
            }
            $columns = [];

            $rs = $dbConn->query('SELECT * FROM '.$tables[0].' LIMIT 0');
            for ($i = 0; $i < $rs->columnCount(); $i++) {
                $col = $rs->getColumnMeta($i);

                $value = "";
                switch ($col['native_type'])
                {
                    case "LONG":
                    case "TINY":
                    case "LONGLONG":
                        $value = "number";
                        break;

                    case "DECIMAL":
                    case "FLOAT":
                        $value = "numberF";
                        break;

                    case "BOOL":
                    case "BOOLEAN":
                    case "BINARY":
                        $value = "bool";
                        break;

                    default:
                        $value="string";
                        break;
                }
                $columns[$this->tableName][$col['name']] = $value;
            };
            $this->tableScheme = $columns;
            if($this->tableScheme==null ) {throw new Exception("Couldn't make Database schema");}
        }

        return $this->tableScheme[$this->getTableName()];

    }

    public function getTableName() : string {
        return $this->tableName;
    }

}