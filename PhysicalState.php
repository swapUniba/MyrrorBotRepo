<?php

function getCardio(){

$param = "?f=PhysicalStates";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {

if(isset($value1['heart'])){

   $max = -1;
   foreach ($value1['heart'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
       if($timestamp > $max){
       	
       	$result = $value2;
       	$max = $timestamp;

       }

     
 
   }
    
}

}

return $result;

}


function getSleep(){


$param = "?f=PhysicalStates";
$json_data = queryMyrror($param);


$result = null;

foreach ($json_data as $key1 => $value1) {

if(isset($value1['sleep'])){
$max = -1;
   foreach ($value1['sleep'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
       if($timestamp > $max){
       	
       	$result = $value2;
       	$max = $timestamp;
       	
       }

   }
    
}

}
return $result;

}