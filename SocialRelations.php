<?php

//CONTATTI (Per adesso restituisce solamente l'elenco dei contatti)
function contatti($text,$confidence){

	$param = "past";
	$json_data = queryMyrror($param);
	$source = ""; //Twitter/Facebook, Instagram

	//Controllo se nella domanda è presente almeno un social network e ne verifico l'unicità
	if (((strpos($text, 'Facebook') !== false) || (strpos($text, 'facebook') !== false)) || ((strpos($text, 'twitter') !== false) || (strpos($text, 'Twitter') !== false)) || ((strpos($text, 'instagram') !== false) || (strpos($text, 'Instagram') !== false)) ) {
		
		//Facebook
		if ((strpos($text, 'Facebook') !== false) || (strpos($text, 'facebook') !== false)){
			if ((strpos($text, 'twitter') == false) && (strpos($text, 'Twitter') == false) && (strpos($text, 'instagram') == false) && (strpos($text, 'Instagram') == false)) {
				
				$source = "facebook";

				$contactIdArray = array();

				//Prendo tutti i contatti di Facebook
				$contactIdArray = getContatti($json_data, $contactIdArray, $source);

    			//Verifico se l'array dei contatti non è vuoto procedo con la selezione dei contatti con cui interagisce maggiormente
    			if(count($contactIdArray) !== 0) {
    				$top3 = contattiFrequenti($contactIdArray);
    				$answer = printAnswer($top3);
    				return $answer;
    			}else{
    				//print_r("Non sono presenti contatti sul social network richiesto");
    				$answer = "Errore nel caricamento dei contatti. Riprova!";
    				return $answer;
    			}


			}else{
				//print_r("Inserisci un solo social network");
				$answer = "Inserisci un solo social network";
    			return $answer;
			}
		}


		//Twitter
		if ((strpos($text, 'Twitter') !== false) || (strpos($text, 'twitter') !== false)){
			if ((strpos($text, 'Facebook') == false) && (strpos($text, 'facebook') == false) && (strpos($text, 'instagram') == false) && (strpos($text, 'Instagram') == false)) {
				
				$source = "twitter";

				$contactIdArray = array();

				//Prendo tutti i contatti di Twitter
				$contactIdArray = getContatti($json_data, $contactIdArray, $source);

    			//Verifico se l'array dei contatti non è vuoto procedo con la selezione dei contatti con cui interagisce maggiormente
    			if(count($contactIdArray) !== 0) {
    				$top3 = contattiFrequenti($contactIdArray);
    				$answer = printAnswer($top3);
    				return $answer;
    			}else{
    				//print_r("Non sono presenti contatti sul social network richiesto");
    				$answer = "Errore nel caricamento dei contatti. Riprova!";
    				return $answer;
    			}

			}else{
				//print_r("Inserisci un solo social network");
				$answer = "Inserisci un solo social network";
    			return $answer;
			}
		}


		//Instagram
		if ((strpos($text, 'Instagram') !== false) || (strpos($text, 'instagram') !== false)){
			if ((strpos($text, 'Facebook') == false) && (strpos($text, 'facebook') == false) && (strpos($text, 'twitter') == false) && (strpos($text, 'Twitter') == false)) {
				$source = "instagram";

				$contactIdArray = array();

				//Prendo tutti i contatti di Instagram
				$contactIdArray = getContatti($json_data, $contactIdArray, $source);

    			//Verifico se l'array dei contatti non è vuoto procedo con la selezione dei contatti con cui interagisce maggiormente
    			if(count($contactIdArray) !== 0) {
    				$top3 = contattiFrequenti($contactIdArray);
    				$answer = printAnswer($top3);
    				return $answer;
    			}else{
    				//print_r("Non sono presenti contatti sul social network richiesto");
    				$answer = "Errore nel caricamento dei contatti. Riprova!";
    				return $answer;
    			}

			}else{
				//print_r("Inserisci un solo social network");
				$answer = "Inserisci un solo social network";
    			return $answer;
			}
		}
	}else{
		//print_r("Inserisci il nome di un social network");
		$answer = "Inserisci il nome di un social network";
    	return $answer;
	}

}

//Calcolo i contatti con i quali l'utente ha interagito di più
function contattiFrequenti($contactIdArray){
	
	$contattiFrequenti = array();

	$contattiFrequenti = array_count_values($contactIdArray); //Genera un array associativo: nome->(occorrenze di nome)
	arsort($contattiFrequenti);//Ordino l'array in relazione al maggior numero di interazioni

	$top3 = array_slice($contattiFrequenti, 0, 3);//Prendo i primi tre

	return $top3;
}

//Prendo tutti i contatti di una determinata source
function getContatti($json_data, $contactIdArray, $source){

	foreach ($json_data as $key1 => $value1) {

		if($key1 == "socialRelations"){

			foreach ($value1 as $key => $value) {

				if ($value['source'] == $source) {
					if (isset($value['contactId'])) {
						$contactId = $value['contactId']; //Prendo il contatto id
						$contactIdArray[] = $contactId; //Inserisco il contatto nell'array
					}
				}
				
			}
        }	
    }

    return $contactIdArray;

}

//Stampa risposta
function printAnswer($top3){

	if (isset($top3)) {
		switch (rand(1,2)) {
			case '1':
				$answer = "<br>I tuoi contatti sono: ";

        		foreach ($top3 as $key => $value) {
   					//echo "$key: $value\n";
   					$answer = $answer . "<br>" . $key . ": " . $value;;
				}
				break;
			case '2':
				$answer = "<br>Ecco i tuoi contatti frequenti: ";
				foreach ($top3 as $key => $value){
   					$answer = $answer . "<br>" . $key . ": " . $value;;
        		}
				break;
		}

	}else{
		$answer = "Contatti non presenti";
	}

	return $answer;
}
