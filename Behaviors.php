<?php

function attivitaFisica($text,$confidence){

$param = "?f=Behaviors";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		$max = 0;
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] != "calories"  && $value2['nameActivity'] != "steps" && 
          $value2['nameActivity'] != "minutesSedentary"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}

return $result;

}

function getCalories(){

$param = "?f=Behaviors";
$json_data = queryMyrror($param);
$result = null;


foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		$max = 0;
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] == "calories"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}


return $result;

}

function getSteps(){

$param = "?f=Behaviors";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		$max = -1;
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] == "steps"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}

return $result;


}

function getSedentary(){

$param = "?f=Behaviors";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		$max = 0;
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] == "minutesSedentary"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}

return $result;



}