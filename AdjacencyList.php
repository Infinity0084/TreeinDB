<?php


class AdjacencyList {

    private $tablename = null;
    private ?array $tableScheme=null;
    private ?PDO $dbConn=null;

    public function __construct($tablename=null, $tableScheme=null, $dbConn=null)
    {
        $this->dbConn = $dbConn;
        $this->tableScheme = $tableScheme;
        $this->tablename = $tablename;
    }

    public function getTableScheme()
    {
        return $this->tableScheme;
    }

    public function setTableScheme(array $tableScheme) {
        $this->tableScheme = $tableScheme;
    }

    public function getTablename()
    {
        return $this->tablename;
    }

    public function setTablename($tablename): void
    {
        $this->tablename = $tablename;
    }

    public function Create(array $values) : bool
    {

            try{
                $this->dbConn->beginTransaction();
                $stringa = "";
                $skipid=false;

                foreach(array_keys($this->tableScheme) as $value) {
                    if(!$skipid) {$skipid =true; continue;}
                    $stringa .= $value.",";
                }
                $stringa = rtrim($stringa, ",");

                $stringa2 = "";
                foreach($values as $value) {
                    $stringa2 .= "?,";
                }
                $stringa2 = rtrim($stringa2, ",");


                $sql = $this->dbConn->prepare("INSERT INTO $this->tablename ($stringa) VALUES ($stringa2)");
                $i = 1;
                foreach (array_keys($this->tableScheme) as $value)
                {
                    $paramType = 0;
                    switch ($this->dbConn[$value]) {

                        case "float":
                        case "int":
                            $paramType = PDO::PARAM_INT;
                            break;

                        case "string":
                            $paramType = PDO::PARAM_STR_CHAR;
                            break;

                        case "bool":
                            $paramType = PDO::PARAM_BOOL;
                            break;

                        default:
                            $paramType = PDO::PARAM_STR_CHAR;
                    }

                    $sql->bindParam($i, $value, $paramType);
                    $i++;
                }

                $sql->execute();

                $this->dbConn->commit();

            }   catch (Exception $e) {
                $this->dbConn->rollBack();
                return false;
            }

        return false;
    }

    public function Read($id=null)
    {
        if(filter_var($id, FILTER_VALIDATE_INT)) {return 0;}

            try {
                $sql = $id==null ? "SELECT * FROM $this->tablename LIMIT 50" :
                    "SELECT * FROM $this->tablename WHERE id=$id LIMIT 50";

                $results = $this->dbConn->query($sql)->fetchAll();
                return $results;
            } catch (Exception $e) {
                error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            }
    }

    public function ReadDescendants($id) {
        if(filter_var($id, FILTER_VALIDATE_INT)) {return 0;}

        try {
            $sql = "SELECT * FROM $this->tablename WHERE parent_id=$id LIMIT 50";

            $results = $this->dbConn->query($sql)->fetchAll();
            return $results;

        } catch (Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
        }
    }

    public function Update($id, $values)
    {
        if(filter_var($id, FILTER_VALIDATE_INT)) {return 0;}

        try {
            $this->dbConn->beginTransaction();
            $sql = "UPDATE $this->tablename SET $values[1]=$values[2] WHERE id=$values[0]";
            if($result = $this->dbConn->exec($sql)) {
                $this->dbConn->commit();
                return true;
            }

            $this->dbConn->rollBack();
            return false;

        } catch (Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            $this->dbConn->rollBack();
            return false;
        }
    }

    public function Delete($id=null)
    {
        try {
            $this->dbConn->beginTransaction();
            $id = $this->dbConn->query("SELECT id FROM $this->tablename WHERE id=$id")->fetch();
            $id = $id["id"];

            $sql = "DELETE FROM $this->tablename WHERE parent_id=$id";

            if($this->dbConn->exec($sql)) {
                $sql = "DELETE FROM $this->tablename WHERE id = $id";
                if($this->dbConn->exec($sql)) {
                    $this->dbConn->commit();
                    return true;
                }
            }

            $this->dbConn->rollBack();
            return false;

        } catch (Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            $this->dbConn->rollBack();
            return false;
        }
    }

    private function checkParentExisting($values) {
        try {
            $exist = $this->dbConn->query("SELECT * FROM ".$this->tablename."WHERE id=".end($values))->fetch();
            if($exist) {return true;} else {return false;}
        } catch(Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            return false;
        }
    }
}