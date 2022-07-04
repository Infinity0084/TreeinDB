<?php

class AdjacencyList extends TreeManagerDB
{


    public function Create() : bool
    {
        try {
            parent::Create();
        } catch (Exception $e){
            return false;
        }

    }

    public function Read()
    {

    }

    public function Update()
    {

    }

    public function Delete()
    {

    }
}