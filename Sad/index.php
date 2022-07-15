<!DOCTYPE html>
<head>
    <title>Test Page</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://unpkg.com/gojs/release/go-debug.js"></script>
    <script src="https://unpkg.com/gojs@2.2.12/extensions/Figures.js"></script>
    <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous"></script>
    <script src="test.js"></script>
</head>
<body onload="init()">    
    
    <div id="myDiagramDiv" style="width: 100%; height: 700px; background-color: #DAE4E4;">
    </div>
    <div id="propertiesPanel" style="; background-color: aliceblue; border: 1px solid black;">
        <b>Properties</b><br>
<?php
//            $fr = fopen("mySavedModel.json", "r");
//            $scheme = fread($fr, filesize("mySavedModel.json"));
//            $scheme = json_decode($scheme, true);
//            fclose($fr);
//
//            foreach (array_keys($scheme["nodeDataArray"][0]) as $value){
//                $capitalized = ucfirst($value);
//                echo "$capitalized: <input type=\"text\" id=\"$value\" value=\"\" onchange=\"updateData(this.value, '$value')\"/><br>";
//            }
//
//        ?>
        Name: <input type="text" id="name" value="" onchange="updateData(this.value, 'name')"><br>
<!--        Title: <input type="text" id="title" value="" onchange="updateData(this.value, 'title')"><br>-->
<!--        Comments: <input type="text" id="comments" value="" onchange="updateData(this.value, 'comments')"><br>-->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>    
</body>