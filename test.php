<?php

require ("AdjacencyList.php");

$test = new AdjacencyList("al_tree");

$result = $test->Read();
print_r($result);
$newresult = [];
$newresult["class"] = "go.GraphLinksModel";
$newresult["nodeDataArray"] = [];
$newresult["linkDataArray"] = [];

$vstavkaData = [];
$vstavkaLink = [];
foreach($result as $value) {
    $vstavkaData["key"] = $value["id"];
    unset($value[0]);
    if($value["parent_id"] != null) {
        $vstavkaLink["to"] = $value["id"];
        $vstavkaLink["from"] = $value["parent_id"];
        $newresult["linkDataArray"][] = $vstavkaLink;
    }
    unset($value["parent_id"]);


    foreach (array_keys($value) as $item) {
        $vstavkaData[$item] = $value[$item];
    }
    $newresult["nodeDataArray"][] = $vstavkaData;
}

$newresult =json_encode($newresult);
$file = fopen("mySavedModel.json", "w");
fwrite($file, $newresult);
fclose($file);
header("Content-type: application/json");
echo $newresult;



//require ("./DatabaseConnection.php");
//require ("./TableManager.php");
//
//$result = DatabaseConnection::getInstance();
//$result = $result->getConnection();
//$result = $result->query("SELECT * FROM cities")->fetchAll();
//print_r($result);
//
//$conn = DatabaseConnection::getInstance();
//$conn = $conn->getConnection();
//
//$manager = TableManager::getInstance();
//$manager->makeDBScheme($conn);
//$result = $manager->getDBScheme();
//print_r($result);



//
//try {
//    $conn = new PDO("mysql:host=localhost;port=3306;dbname=new_database", "root", "A159159_z");
//    echo "Database connection established"."\n";
//    $id = $conn->query("SELECT id FROM cities WHERE id=2")->fetch();
//
//} catch (PDOException $e) {
//        echo "Connection failed: ".$e->getMessage();
//    }

//    $fields = ["cities", "regions"];
//    $fields2 = ["id", "int"];
//
//    $arr = array(
//        "DB" => "MySql",
//        "tables" => []
//        );
//    foreach ($fields as $value) {
//            $arr["tables"][$value] = $fields2;
//
//    }
//    print_r($arr);
//    echo "<br>";
//    foreach($arr["tables"] as $value){
//        print_r($value);
//        echo "<br>";
//    }

//
//    try {
//        $conn = new PDO("mysql:host=localhost;port=3306;dbname=new_database", "root", "A159159_z");
//        echo "Database connection established"."<br>";
//
//        $showall = $conn->query('SHOW TABLES from new_database');
//        $results = [];
//        foreach($showall as $value){
//            array_push ($results, $value["Tables_in_new_database"]);
//            echo "<br>";
//        }
//        print_r($results);
//        unset($showall);
//
//        foreach($results as $value){
//            $rs = $conn->query('SELECT * FROM '.$value.' LIMIT 0');
//            for ($i = 0; $i < $rs->columnCount(); $i++) {
//            $col = $rs->getColumnMeta($i);
//            $columns[] = $col['name'];
//            $columns2[$value] = $col['native_type'];
//            }
////            print_r($columns);
////            echo "<br>";
//            echo "<br>";
//            print_r($columns2);
////            echo "<br>";
//        }
//
//
//
//
//    }
//    catch (PDOException $e) {
//        echo "Connection failed: ".$e->getMessage();
//    }

