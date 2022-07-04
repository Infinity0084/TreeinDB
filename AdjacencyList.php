<?php

class AdjacencyList extends TreeManagerDB
{

    public function Create($tablename ,...$values) : bool
    {
        parent::checkCRUDcondition();

    }

    public function Read($tablename, ...$values)
    {

    }

    public function Update($tablename, ...$values)
    {

    }

    public function Delete($tablename, ...$values)
    {

    }

    private function checkParentExisting($tablename, $id, ...$values) {
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