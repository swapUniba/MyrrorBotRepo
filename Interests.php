<?php

//INTERESSI
function interessi($text,$confidence){

	$param = "today";
	$json_data = queryMyrror($param);

	$categorieArray = array();

	foreach ($json_data as $key1 => $value1) {

		if($key1 == "interests"){
			foreach ($value1 as $key => $value) {
				if (isset($value['value'])) {//Se è valorizzata la variabile 'value'

					$categoria = $value['value']; //Prendo la categoria

   	 				//Controllo ed elimino la dicitura "Category:" da alcuni item
					if (strpos($categoria, 'Category:') !== false) {
    					$categoria = substr($categoria,9); //Elimino le prime 9 lettere
					}

					$categorieArray[] = $categoria; //Inserisco la categoria nell'array

					//Se l'array delle categorie non è vuoto, trovo gli interessi più frequenti
					if (count($categorieArray) != 0) {
						$top5 = interessiFrequenti($categorieArray);
					}
				}
			}
        }	
    }


	if (isset($top5)) {
		switch (rand(1,3)) {
			case '1':
				$answer = "<br>I tuoi interessi sono: ";

				if (count($top5) != 0) {
					foreach ($top5 as $key => $value){
   						$answer = $answer . "<br>" . $key . ": " . $value;;
        			}
				}else {
					$answer = "Errore nel caricamento degli interessi. Riprova!";
				}
	
				break;
			case '2':
				$answer = "<br>Sei interessato a: ";
				if (count($top5) != 0) {
					foreach ($top5 as $key => $value){
   						$answer = $answer . "<br>" . $key . ": " . $value;;
        			}
				}else {
					$answer = "Errore nel caricamento degli interessi. Riprova!";
				}
				break;
			default:
				$answer = "Ecco qui i tuoi interessi: ";
				if (count($top5) != 0) {
					foreach ($top5 as $key => $value){
   						$answer = $answer . "<br>" . $key . ": " . $value;;
        			}
				}else {
					$answer = "Errore nel caricamento degli interessi. Riprova!";
				}
        		break;
		}

	}else{
		$answer = "Interessi non presenti. Riprova!";
	}

	//A volte la richiesta non restituisce nessun elenco perciò dovrà essere rifatta
	if ($answer == null) {
		$answer = "Errore nel caricamento degli interessi. Riprova.";
	}

	//printf($answer);

	return $answer;

}


//Calcolo i contatti con i quali l'utente ha interagito di più
function interessiFrequenti($categorieArray){
	
	$interessiFrequenti = array();

	$interessiFrequenti = array_count_values($categorieArray); //Genera un array associativo: nome->(occorrenze di nome)
	arsort($interessiFrequenti);//Ordino l'array in relazione al maggior numero di interazioni

	$top5 = array_slice($interessiFrequenti, 0, 5);//Prendo i primi cinque

	return $top5;
}

