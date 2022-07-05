<?php

class AdjacencyList extends TreeManagerDB
{

    public function Create($tablename ,...$values) : bool
    {
        parent::checkCRUDcondition();
        if($this->checkParentExisting($tablename, $values)) {
            try{
                $this->pdoConnection->beginTransaction();
                $stringa = "";
                $skipid=false;

                foreach(array_keys($this->dbJsonSchema[$tablename]) as $value) {
                    if(!$skipid) {$skipid =true; continue;}
                    $stringa .= $value.",";
                }
                $stringa = rtrim($stringa, ",");
                $stringa2 = "";
                foreach($values as $value) {
                    $stringa2 .= "?,";
                }
                $stringa2 = rtrim($stringa2, ",");


                $sql = $this->pdoConnection->prepare("INSERT INTO $tablename ($stringa) VALUES ($stringa2)");
                $i = 1;
                foreach (array_keys($this->dbJsonSchema[$tablename]) as $value)
                {
                    $paramType = 0;
                    switch ($this->dbJsonSchema[$tablename][$value]) {
                        case "number":
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

                $this->pdoConnection->commit();

            }   catch (Exception $e) {
                $this->selfErrLog($e->getMessage());
                $this->pdoConnection->rollBack();
                return false;
            }
        }
        return false;
    }

    public function Read($tablename, ...$values)
    {
        $this->checkCRUDcondition();
        try {
            $results = $this->pdoConnection->query("SELECT * FROM $tablename LIMIT 50")->fetchAll();
            return $results;
        } catch (Exception $e) {
            $this->selfErrLog($e->getMessage());
        }
        return 0;
    }

    public function Update($tablename, ...$values)
    {
        $this->checkCRUDcondition();

        try {
            $this->pdoConnection->beginTransaction();
            $sql = "UPDATE $tablename SET $values[1]=$values[2] WHERE id=$values[0]";
            if($result = $this->pdoConnection->exec($sql)) {
                $this->pdoConnection->commit();
                return true;
            }

            $this->pdoConnection->rollBack();
            return false;

        } catch (Exception $e) {
            $this->selfErrLog($e->getMessage());
            $this->pdoConnection->rollBack();
            return false;
        }
    }

    public function Delete($tablename, ...$values)
    {
        try {
            $this->pdoConnection->beginTransaction();
            $sql = "DELETE FROM $tablename WHERE id = $values[0]";

            if($this->pdoConnection->exec($sql)) {
                $this->pdoConnection->commit();
                return true;
            }

            $this->pdoConnection->rollBack();
            return false;

        } catch (Exception $e) {
            $this->selfErrLog($e->getMessage());
            $this->pdoConnection->rollBack();
            return false;
        }
    }

    private function checkParentExisting($tablename, ...$values) {
        $exist = 0;
        try {
            $exist = $this->pdoConnection->query("SELECT * FROM ".$tablename.
                "WHERE ".$this->dbJsonSchema[$tablename][0]."=".end($values))->fetch();

            if($exist) {return true;} else {return false;}
        } catch(Exception $e) {
            $this->selfErrLog($e->getMessage());
            return false;
        }
    }
}