<?php

//IDENTITA' UTENTE
function identitaUtente($text,$confidence){

$param = "?f=Demographics&l=10";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['name'])){

		foreach ($value1['name'] as $key2 => $value2) {

			if ($key2 == "value") {
				
				//print_r($value2);
				$result = $value2;

			} 	
        }	
	}
}

	return $result;

}


//LAVORO
function lavoro($text,$confidence){

	$param = "?f=Demographics";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {

		if(isset($value1['industry'])){

			$max = 0;

			foreach ($value1['industry'] as $key2 => $value2) {

				$timestamp = $value2['timestamp'];
				$industry = $value2['value'];

				//print_r($timestamp + "<br>");
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$industry = $value2['value'];
           			//print_r($timestamp + "<br>");
         		}	
        	}	
		}
	}

	return $industry;

}


//EMAIL
function email($text,$confidence){

	$param = "?f=Demographics";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {

		if(isset($value1['email'])){

			$max = 0;

			foreach ($value1['email'] as $key2 => $value2) {

				$timestamp = $value2['timestamp'];
				$email = $value2['value'];

				//print_r($timestamp + "<br>");
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$email = $value2['value'];
           			//print_r($timestamp + "<br>");
         		}	
        	}	
		}
	}

	return $email;

}




/*foreach ($json_data as $key1 => $value1) {

	//echo $key1;
	echo "\n";

	if($key1 == "demographics"){

		//echo '<pre>'; print_r($value1); echo '</pre>';

		foreach ($value1 as $item){
  			if (isset($item['dateOfBirth'])) {
  				echo "trovataaaa";
  			}
		}
	}
}*/

