<?php

require ("AdjacencyList.php");

$operation = $_GET["Message"];


$db = DatabaseConnection::getInstance();
$list = new AdjacencyList("al_tree");

switch ($operation["operation"])
{
    case "Update":
        echo "True";
        $list->Update($operation["id"], $operation["field"], $operation["value"]);
        break;

    case "Delete":
        $list->Delete($operation["id"]);
        break;

    default:
        break;
}

