<?php

require_once ("DatabaseConnection.php");

class Node
{
    public $name;
    public $linked = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function link_to(Node $node, $also = true)
    {
        if (!$this->linked($node)) $this->linked[] = $node;
        if ($also) $node->link_to($this, false);
        return $this;
    }

    private function linked(Node $node)
    {
        foreach ($this->linked as $l) { if ($l->name === $node->name) return true; }
        return false;
    }

    public function not_visited_nodes($visited_names)
    {
        $ret = array();
        foreach ($this->linked as $l) {
            if (!in_array($l->name, $visited_names)) $ret[] = $l;
        }
        return $ret;
    }
}

///* Building Graph */
//$root = new Node('node1');
//$db = DatabaseConnection::getInstance();
//$arrik = [];
//$result = $db->query("SELECT parent_id, id FROM al_tree")->fetchAll();
//foreach ($result as $value) {
//        $arrik[$value["id"]] = $value["parent_id"];
//}
////print_r($arrik);
////
////$nodes = [];
////
////foreach (array_keys($arrik) as $v) {
////    $name = "node{$v}";
////    $$name = new Node($name);
////    $nodes[] =  $$name;
////}
////
////
////foreach ($nodes as $value) {
////    $id = $value->name[4];
////    if($arrik[$id] != null) {
////        $nodes[$arrik[$id]-1]->link_to($value, false);
////    }
////}

$arrik = [];

/* Searching Path */
function dfs(Node $node, $path = '', $visited = array())
{
    $visited[] = $node->name;
    $not_visited = $node->not_visited_nodes($visited);
    global $arrik;
    if (empty($not_visited)) {
        echo 'path : ' . $path . '->' . $node->name . PHP_EOL;
        $arrik[] = $path.'->'.$node->name;
        return;
    }
    foreach ($not_visited as $n) dfs($n, $path . '->' . $node->name, $visited);

}


//dfs($nodes[0]);
//foreach (array_keys($arrik) as $value) {
//    $arrik[$value] = explode("->", $arrik[$value]);
//    unset($arrik[$value][0]);
//    unset($arrik[$value][1]);
//}
//print_r($arrik);
//
//foreach ($arrik as $value) {
//    foreach ($arrik as $items) {
//        foreach ($items as $item) {
//            $sql = $db->prepare("DELETE FROM al_tree WHERE id=?");
//            $sql->bindValue(1, $item[4], PDO::PARAM_INT);
//            $sql->execute();
//        }
//
//    }
//}
// path : ->root->node1->node3
// path : ->root->node1->node4->node5->node2->node6
// path : ->root->node2->node5->node4->node1->node3
// path : ->root->node2->node6
