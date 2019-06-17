<?php

//Restuisce l'elenco dei maggiori interessi dell'utente. N.B. Non vengono presi in considerazione quelli che hanno la dicitura "Category:"
function interessi($resp,$parameters){

	$param = "";
	$json_data = queryMyrror($param);

	$categorieArray = array();

	foreach ($json_data as $key1 => $value1) {

		if($key1 == "interests"){
			foreach ($value1 as $key => $value) {
				if (isset($value['value'])) {//Verifico se è valorizzata la variabile 'value'

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

    //Se è valorizzato l'array, stampo gli interessi
	if (isset($top5)) {
		$answer = "<br>" . $resp;

		if (count($top5) != 0) {
			foreach ($top5 as $key => $value){
   				$answer = $answer . "<br>" . $key . ": " . $value;;
        	}
		}else {
			$answer = "Errore nel caricamento degli interessi. Riprova!";
		}

	}else{
		$answer = "Interessi non presenti. Riprova!";
	}

	//A volte la richiesta non restituisce nessun elenco perciò dovrà essere rifatta
	if ($answer == null) {
		$answer = "Errore nel caricamento degli interessi. Riprova.";
	}

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

