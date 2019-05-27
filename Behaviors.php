<?php

include "myrrorlogin.php";

function attivitaFisica($text,$confidence){

$param = "?f=Behaviors&l=10";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){

	
		$max = 0;
		


	
	foreach ($value1['fromActivity'] as $key2 => $value2) {

     
         $timestamp = $value2['timestamp'];
		 
		 

         if($timestamp > $max ){
         
           $result = $value2;
           $max = $timestamp;

         }

		// print($timestamp."\n");

	}
	
		
	}
}




return $result;




}