<?php

require_once ("DatabaseConnection.php");
require_once ("TableManager.php");

class AdjacencyList {

    private TableManager $tableScheme;

    public function __construct($tablename)
    {
        $this->tableScheme = new TableManager($tablename);
    }

    public function getTablename()
    {
        return $this->tableScheme->getTableName();
    }

    public function setTablename($tablename): void
    {
        unset($this->tableScheme);
        $this->tableScheme = new TableManager($tablename);
    }

    public function Create(array $values) : bool
    {
        $schema = $this->tableScheme->getTableScheme();
        if(count($values) != count($schema)) {return false;}

        $db = DatabaseConnection::getInstance()->getConnection();
        $db->beginTransaction();

            try{
                $schemeNames = array_keys($schema);
                unset($schemeNames[0]);
                if(!$this->checkParentExisting(end($values))){return 0;}

                $tablename = $this->tableScheme->getTableName();

                $schemeValues = join(",", $schemeNames);
                $params = [];
                foreach($schemeNames as $value) {
                    $params[] = "?";
                }
                $params = join(",", $params);

                $sql = $db->prepare("INSERT INTO $tablename ($schemeValues) VALUES ($params)");
                $i = 1;
                foreach ($schemeNames as $value)
                {
                    $paramType = 0;
                    switch ($schema[$value]) {

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

                    $sql->bindValue($i, $value, $paramType);
                    $i++;
                }

                $sql->execute();

                $db->commit();
                return true;
            }   catch (Exception $e) {
                $db->rollBack();
                return false;
            }
    }

    public function Read()
    {
        $tablename = $this->tableScheme->getTableName();
            try {
                $sql = "SELECT * FROM $tablename LIMIT 50";

                return DatabaseConnection::getInstance()->getConnection()->query($sql)->fetchAll();
            } catch (Exception $e) {
                error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            }
    }

    public function ReadDescendants($id) {
        if(filter_var($id, FILTER_VALIDATE_INT)) {return 0;}

        try {
            $sql = "SELECT * FROM $this->tableScheme->getTableName() WHERE parent_id=$id LIMIT 50";

            return DatabaseConnection::getInstance()->getConnection()->query($sql)->fetchAll();

        } catch (Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
        }
    }

    public function Update($id, $field, $newValue)
    {
        if(filter_var($id, FILTER_VALIDATE_INT) && $id <= 0) {return 0;}
        if(!in_array($field, array_keys($this->tableScheme->getTableScheme()))) {return 0;}
        if($field=="parent_id" && !$this->checkParentExisting($id)) {return 0;}

        $db = DatabaseConnection::getInstance()->getConnection();
        $tablename = $this->tableScheme->getTableName();

        try {
            $db->beginTransaction();

            $sql = "UPDATE $tablename SET $field=? WHERE id=$id";

            $sqlPreparedQuery = $db->prepare($sql);

            $paramType=null;

            switch ($this->tableScheme->getTableScheme()[$field]) {

                case "float":
                case "int":
                    $paramType = PDO::PARAM_INT;
                    break;

                case "string":
                    $paramType = PDO::PARAM_STR;
                    break;

                case "bool":
                    $paramType = PDO::PARAM_BOOL;
                    break;

                default:
                    $paramType = PDO::PARAM_STR_CHAR;
            }


            $sqlPreparedQuery->bindValue(1, $newValue, $paramType);

            if($result = $sqlPreparedQuery->execute()) {
                $db->commit();
                return true;
            }

            $db->rollBack();
            return false;

        } catch (Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            $db->rollBack();
            return false;
        }
    }

    public function Delete($id)
    {
        if(filter_var($id, FILTER_VALIDATE_INT) && $id > 0) {return 0;}

        $db = DatabaseConnection::getInstance()->getConnection();
        try {
            $db->beginTransaction();
            $id = $db->query("SELECT id FROM $this->tableScheme->getTableName() WHERE id=$id")->fetch();
            $id = $id["id"];

            $sql = "DELETE FROM $this->tableScheme->getTableName() WHERE parent_id=$id";

            if($db->exec($sql)) {
                $sql = "DELETE FROM $this->tableScheme->getTableName() WHERE id = $id";
                if($db->exec($sql)) {
                    $db->commit();
                    return true;
                }
            }

            $db->rollBack();
            return false;

        } catch (Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            $db->rollBack();
            return false;
        }
    }

    private function checkParentExisting($parent_id) {
        $db = DatabaseConnection::getInstance()->getConnection();
        try {
            $tablename = $this->tableScheme->getTableName();
            $exist = $db->prepare("SELECT * FROM $tablename WHERE id=?");
            $exist->bindValue(1, $parent_id, PDO::PARAM_INT);

            if($exist->execute()) {return true;} else {return false;}
        } catch(Exception $e) {
            error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
            return false;
        }
    }
}