<?php

$arrat = [];
$arrat2 = [];
$arrat2["from"] = 1;
$arrat2["to"] = 2;
$arrat["TEST"] = [];
$arrat["TEST"][] = $arrat2;
$arrat["TEST"][] = $arrat2;
print_r($arrat);
