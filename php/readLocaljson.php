<?php


function queryMyrror($param){

$json = null;

if($param == "today"){
	$json = file_get_contents('../fileMyrror/today.json');
}else if($param == "yesterday"){
	$json = file_get_contents('../fileMyrror/yesterday.json');
}else{
	$json = file_get_contents('../fileMyrror/past.json');
}
$result = json_decode($json,true);
return $result;


}


?>