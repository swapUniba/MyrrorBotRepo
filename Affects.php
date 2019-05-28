<?php

function getSentiment(){


$param = "?f=Affects";
$json_data = queryMyrror($param);
$result = null;
$max = "";
 
foreach ($json_data['affects'] as $key1 => $value1) {

     	   
		$date = substr($value1['date'], 10);
	    if($date > $max){
          
           $result = $value1;
           $max = $date;

   	    }
		
	}

	return $result;

}


   
