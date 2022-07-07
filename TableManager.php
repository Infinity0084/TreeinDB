<?php

class TableManager
{
    private static ?TableManager $instance=null;
    private array $dbJsonSchema = [];

    public function makeDBScheme($pdoConnection=null) : bool {
        if(empty($pdoConnection)) {return false;}

        try {
            $result = $pdoConnection->query("SHOW tables")->fetchAll();
            $tables = [];
            foreach ($result as $value) {
                $tables[] = $value[0];
            }

            $columns = [];
            $num = 0;

            foreach($tables as $value){
                $rs = $pdoConnection->query('SELECT * FROM '.$value.' LIMIT 0');
                for ($i = 0; $i < $rs->columnCount(); $i++) {
                    $col = $rs->getColumnMeta($i);

                    $value = "";
                    switch ($col['native_type'])
                    {
                        case "LONG":
                            $value = "int";
                            break;

                        case "DECIMAL":
                            $value = "float";
                            break;

                        case "TINY":
                        case "BOOL":
                        case "BOOLEAN":
                        case "BINARY":
                            $value = "bool";
                            break;

                        default:
                            $value="string";
                            break;
                    }

                    $columns[$num][$col['name']] = $value;
                };
                $num++;
            }
            $this->dbJsonSchema = $columns;
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new TableManager();
        }

        return self::$instance;
    }

    public function getDBScheme() {
        return $this->dbJsonSchema;
    }
}