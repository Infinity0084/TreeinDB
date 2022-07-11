<?php

require_once ("DatabaseConnection.php");

class TableManager
{
    private array $tableScheme;

    public function __construct($tableName) {
        $dbConn = DatabaseConnection::getInstance()->getConnection();
        $result = $dbConn->query("SHOW tables")->fetchAll();
        $this->tableName = $tableName;
        $tables = [];
        foreach ($result as $value) {
            if($value[0]==$tableName) {$tables[] = $value[0]; break;}
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
                case "BOOL":
                case "BOOLEAN":
                case "BINARY":
                    $value = "bool";
                    break;

                default:
                    $value="string";
                    break;
            }

            $columns[$tableName][$col['name']] = $value;
        };
        $this->tableScheme = $columns;
    }

    public function getTableScheme() : array {
        return $this->tableScheme[$this->tableName];
    }

    public function getTableName() : string {
        return array_key_first($this->tableScheme);
    }
}