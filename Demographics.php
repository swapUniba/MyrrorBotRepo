<?php

//IDENTITA' UTENTE
function identitaUtente($resp,$parameters,$text){

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {
	
		if(isset($value1['name'])){

			foreach ($value1['name'] as $key2 => $value2) {

				if ($key2 == "value") {
					$result = $value2;
				} 	
        	}	
		}
	}

	
     
	if (isset($result)) {

		$answer = str_replace("X",$result,$resp);


	}else{
		$answer = "Nome non presente";
	}

	return $answer;
}



//ETA'
function getEta($resp,$parameters,$text){

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;
	$answer = "";

	foreach ($json_data as $key1 => $value1) {
	
		if(isset($value1['dateOfBirth'])){

			foreach ($value1['dateOfBirth'] as $key2 => $value2) {

				if ($key2 == "value") {
					$result = $value2;
				} 	
        	}	
		}
	}

	if($result == null){
		$answer = "Data di compleanno non disponibile";
	}else{
		$today = date("Y-m-d");
		$diff = abs(strtotime($today) - strtotime($result));
    	$years = floor($diff / (365*60*60*24));


		$answer = str_replace("X",$years,$resp);
	}

	return $answer;
}



//LUOGO DI NASCITA
function getCountry($resp,$parameters,$text){

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {
		if(isset($value1['country'])){

			foreach ($value1['country'] as $key2 => $value2) {
				if ($key2 == "value") {
					$result = $value2;
				} 	
        	}	
		}
	}

	if (isset($result)) {

		$answer = str_replace("X",$result,$resp);

	}else{
		$answer = "Luogo di nascita non presente";
	}

	return $answer;
}



//ALTEZZA
function getHeight($resp,$parameters,$text){

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {
		if(isset($value1['height'])){

			foreach ($value1['height'] as $key2 => $value2) {
				if ($key2 == "value") {
					$result = $value2;
				} 	
        	}	
		}
	}

	if (isset($result)) {


		$answer = str_replace("X",$result,$resp);


	}else{
		$answer = "Altezza non presente";
	}

	return $answer;
}


//PESO
function getWeight($resp,$parameters,$text){ 

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {
		if(isset($value1['weight'])){

			foreach ($value1['weight'] as $key2 => $value2) {
				if ($key2 == "value") {
					$result = $value2;
				} 	
        	}	
		}
	}

	if (isset($result)) {

		$answer = str_replace("X",$result,$resp);

	}else{
		$answer = "Peso non presente";
	}

	return $answer;
}



//LAVORO
function lavoro($resp,$parameters,$text){

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;

	foreach ($json_data as $key1 => $value1) {

		if(isset($value1['industry'])){

			$max = 0;

			foreach ($value1['industry'] as $key2 => $value2) {

				$timestamp = $value2['timestamp'];
				$industry = $value2['value'];
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$industry = $value2['value'];
         		}	
        	}	
		}
	}

	if (isset($industry)) {

		$answer = str_replace("X",$industry,$resp);


	}else{
		$answer = "Lavoro non presente";
	}

	return $answer;

}


//EMAIL
function email($resp,$parameters,$text){

	$param = "";
	$json_data = queryMyrror($param);
	$result = null;
    

	foreach ($json_data as $key1 => $value1) {

		if(isset($value1['email'])){
           
			$max = 0;

			foreach ($value1['email'] as $key2 => $value2) {

				$timestamp = $value2['timestamp'];
				$email = $value2['value'];
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$email = $value2['value'];
         		}	
        	}	
		}
	}

	if (isset($email)) {
	
		$answer = str_replace("X",$email,$resp);

	}else{
		$answer = "Email non presente";
	}

	return $answer;
}

