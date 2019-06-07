<?php


function queryMyrror($param){

$json = null;

if($param == "today"){
	$json = file_get_contents('today.json');
}else if($param == "yesterday"){
	$json = file_get_contents('yesterday.json');
}else{
	$json = file_get_contents('past.json');
}
$result = json_decode($json,true);
return $result;


}


?>