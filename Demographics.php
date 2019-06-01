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
					$result = $value2;
				} 	
        	}	
		}
	}

	if (isset($result)) {
		switch (rand(1,3)) {
			case '1':
				$answer = $result;
				break;
			case '2':
				$answer = "Ti chiami " . $result;
				break;
			default:
				$answer = "Il tuo nome è " . $result;
				break;
		}

	}else{
		$answer = "Nome non presente";
	}

	return $answer;
}



//ETA'
function getEta(){

	$param = "?f=Demographics&l=10";
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

    	switch (rand(1,2)) {
			case '1':
    			$answer = "Hai ". $years . " anni";
				break;
			case '2':
				$answer = "La tua età è " . $years . " anni";
				break;
			default:
				break;
		}
	}

	return $answer;
}



//LUOGO DI NASCITA
function getCountry(){

	$param = "?f=Demographics&l=10";
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
		switch (rand(1,2)) {
			case '1':
				$answer = "Il tuo paese è " . $result;
				break;
			case '2':
				$answer = "Il tuo luogo di nascita è " . $result;
				break;
			default:
				break;
		}

	}else{
		$answer = "Luogo di nascita non presente";
	}

	return $answer;
}



//ALTEZZA
function getHeight(){

	$param = "?f=Demographics&l=10";
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
		switch (rand(1,2)) {
			case '1':
				$answer = "Sei alto " . $result . " cm";
				break;
			case '2':
				$answer = "La tua altezza è " . $result . " cm";
				break;
			default:
				break;
		}

	}else{
		$answer = "Altezza non presente";
	}

	return $answer;
}


//PESO
function getWeight(){

	$param = "?f=Demographics&l=10";
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
		switch (rand(1,2)) {
			case '1':
				$answer = "Pesi " . $result . " kg";
				break;
			case '2':
				$answer = "Il tuo peso è " . $result . " kg";
				break;
			default:
				break;
		}

	}else{
		$answer = "Peso non presente";
	}

	return $answer;
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
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$industry = $value2['value'];
         		}	
        	}	
		}
	}

	if (isset($industry)) {
		switch (rand(1,3)) {
			case '1':
				$answer = "Il tuo lavoro è " . $industry;
				break;
			case '2':
				$answer = "Lavoro: " . $industry;
				break;
			default:
				$answer = "Lavori come " . $industry;
				break;
		}

	}else{
		$answer = "Lavoro non presente";
	}

	return $answer;

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
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$email = $value2['value'];
         		}	
        	}	
		}
	}

	if (isset($email)) {
		switch (rand(1,3)) {
			case '1':
				$answer = "Il tuo indirizzo email è " . $email;
				break;
			case '2':
				$answer = "Email " . $email;
				break;
			default:
				$answer = "La tua email è " . $email;
				break;
		}

	}else{
		$answer = "Email non presente";
	}

	return $answer;
}

