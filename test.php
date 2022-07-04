<?php



//try {
//    $conn = new PDO("mysql:host=localhost;port=3306;dbname=new_database", "root", "A159159_z");
//    echo "Database connection established"."\n";
//    $result = $conn->query("SHOW tables")->fetchAll();
//    $tables = [];
//    foreach ($result as $value) {
//        $tables[] = $value[0];
//    }
//
//    $columns = [];
//    $num = 0;
//    foreach($tables as $value){
//            $rs = $conn->query('SELECT * FROM '.$value.' LIMIT 0');
//            for ($i = 0; $i < $rs->columnCount(); $i++) {
//            $col = $rs->getColumnMeta($i);
//
//            $columns[$num][$col['name']] = $col['native_type'] == "LONG" || $col['native_type'] == "TINY"
//                                                               ? "number" : "string";
//            };
//            $num++;
//        }
//    $columns = json_encode($columns);
//    print_r($columns);
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

