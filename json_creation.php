<?php 
include "myrrorlogin.php";

$response = queryMyrror("");
$fp = fopen('past.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);

$yesterday = date('Y-m-d',strtotime("-1 days"));
$response = queryMyrror("?fromDate=".$yesterday."&toDate=".$yesterday);
$fp = fopen('yesterday.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);

$today = date('Y-m-d');
$response = queryMyrror("?fromDate=".$today);
$fp = fopen('today.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);

?> 