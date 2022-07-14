<?php

require_once ("DatabaseConnection.php");
require_once ("TableManager.php");

class AdjacencyList {

    private TableManager $tableScheme;
    private static array $acceptedConditions = [">", "<", "=", "<=>", "<>", "!=", ">=", "<="];

    public function __construct($tablename)
    {
        $this->tableScheme = new TableManager($tablename);
    }

    public function getTablename()
    {
        return $this->tableScheme->getTableName();
    }

    public function Create(array $values) : bool
    {
        $schema = $this->tableScheme->getTableScheme();
        if(count($values) < count($schema) ||  count($values) > count($schema)) {throw new Exception("Oh shit here we go again!");}

        $db = DatabaseConnection::getInstance();

        $schemeNames = array_keys($schema);
        unset($schemeNames[0]);
        if(!$this->checkParentExisting(end($values))){return 0;}

        $tablename = $this->tableScheme->getTableName();

        $schemeValues = join(",", $schemeNames);
        $params = array_fill(0, count($schemeNames), "?");

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

        if($sql->execute()) {
            $db->commit();
            return $db->lastInsertId();
        }
        return false;

    }

    public function Read(array $values=null)
    {
        $tablename = $this->getTablename();
        $sql = "SELECT * FROM $tablename";

        if($values == null) {return DatabaseConnection::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);}

        $schema = $this->tableScheme->getTableScheme();
        $schemaFields = array_keys($schema);

        $preConditionStrings = [];

        foreach ($values as $value) {
            if(!in_array($value[0], $schemaFields)) {throw new Exception("Field $value[0] doesn't exist in scheme");}
            if(!in_array($value[1], AdjacencyList::$acceptedConditions)) {throw new Exception("This $value[1] condition sign not accepted");}

            switch ($schema[$value[0]]) {

                case "number":
                    if(!filter_var($value[3], FILTER_VALIDATE_INT)) {throw new Exception("$value[3] doesn't integer");}
                    break;

                case "numberF":
                    if(!filter_var($value[3], FILTER_VALIDATE_FLOAT)) {throw new Exception("$value[3] doesn't float");}
                    break;

                case "bool":
                    if(!filter_var($value[3], FILTER_VALIDATE_BOOL)) {throw new Exception("$value[3] doesn't bool");}
                    break;

                default:
                    break;
            }
                $preConditionStrings[] = "$value[0]$value[1]$value[2]";
        }

        $conditionsStrings = implode(" && ", $preConditionStrings);

        return DatabaseConnection::getInstance()->query($sql." $conditionsStrings")->fetchAll();
    }

    public function ReadDescendants($id) {
        throw new Exception("This class doesn't implement this function!");
//        if(filter_var($id, FILTER_VALIDATE_INT) && $id <= 0) {throw new Exception("id should be positive integer");}
//        $db = DatabaseConnection::getInstance();
//
//            $sql =$db->prepare("SELECT * FROM $this->tableScheme->getTableName() WHERE parent_id=? LIMIT 50");
//            $sql->bindValue(1, $id, PDO::PARAM_INT);
//
//            return DatabaseConnection::getInstance()->query($sql)->fetchAll();

    }

    public function Update($id, $field, $newValue)
    {
        if(filter_var($id, FILTER_VALIDATE_INT) && $id <= 0) {throw new Exception("id should be positive integer!");}
        if(!in_array($field, array_keys($this->tableScheme->getTableScheme()))) {throw new Exception("Field: $field not found in the scheme!");}
        if($field=="parent_id" && !$this->checkParentExisting($id)) {throw new Exception("Where's my papa ? I can't see him!");}

        $db = DatabaseConnection::getInstance();
        $tablename = $this->tableScheme->getTableName();


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
            return true;
        }

        return false;

    }

    public function Delete($id)
    {
        if(filter_var($id, FILTER_VALIDATE_INT) && $id <= 0) {throw new Exception("id should be positive integer!");}

        $db = DatabaseConnection::getInstance();

            $tablename = $this->tableScheme->getTableName();

            if(!$this->checkParentExisting($id)) {throw new Exception("Id not found !");}

            $sql = $db->prepare("DELETE FROM $tablename WHERE id=?");
            $sql->bindValue(1, $id, PDO::PARAM_INT);

            if($result = $sql->execute()) {
                return true;
            }
            return false;

    }

    public function DeleteDescendants($id) {
       if(filter_var($id, FILTER_VALIDATE_INT) && $id <= 0) {throw new Exception("id should be positive integer!");}

///* Building Graph */
        $root = new Node('node1');
        $db = DatabaseConnection::getInstance();
        $arrik = [];
        $result = $db->query("SELECT parent_id, id FROM al_tree")->fetchAll();
        foreach ($result as $value) {
            $arrik[$value["id"]] = $value["parent_id"];
        }
        print_r($arrik);

        $nodes = [];

        foreach (array_keys($arrik) as $v) {
            $name = "node{$v}";
            $$name = new Node($name);
            $nodes[] =  $$name;
        }


        foreach ($nodes as $value) {
            $id = $value->name[4];
            if($arrik[$id] != null) {
                $nodes[$arrik[$id]-1]->link_to($value, false);
            }
        }
        dfs($nodes[$id]);
        foreach (array_keys($arrik) as $value) {
            $arrik[$value] = explode("->", $arrik[$value]);
            unset($arrik[$value][0]);
            unset($arrik[$value][1]);
        }
        print_r($arrik);

        foreach ($arrik as $value) {
            foreach ($arrik as $items) {
                foreach ($items as $item) {
                    $sql = $db->prepare("DELETE FROM al_tree WHERE id=?");
                    $sql->bindValue(1, $item[4], PDO::PARAM_INT);
                    $sql->execute();
                }

            }
        }

///////////////////////////////////////////
//       $db = DatabaseConnection::getInstance();
//       try {
//           $db->beginTransaction();
//           $tablename = $this->tableScheme->getTableName();
//
//           if(!$this->checkParentExisting($id)) {throw new Exception("Parent doesn't exist!");}
//
//           $sql = $db->prepare("DELETE FROM $tablename WHERE parent_id=?");
//           $sql->bindValue(1, $id, PDO::PARAM_INT);
//
//           if($result = $sql->execute()) {
//               $db->commit();
//               return true;
//           }
//        return false;
//       } catch (Exception $e) {
//           error_log(3, $e->getMessage(), dirname(__FILE__)."/log.txt");
//           $db->rollBack();
//           return false;
//       }

    }

    private function checkParentExisting($parent_id) {
        $db = DatabaseConnection::getInstance();
        $tablename = $this->tableScheme->getTableName();
        $exist = $db->prepare("SELECT * FROM $tablename WHERE id=?");
        $exist->bindValue(1, $parent_id, PDO::PARAM_INT);

        if($exist->execute()) {return true;} else {return false;}

    }


}