<?php

//INTERESSI
function interessi($text,$confidence){

	$param = "?f=Interests";
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
				}
			}
        }	
    }


	if (isset($categorieArray)) {
		switch (rand(1,3)) {
			case '1':
				$answer = "I tuoi interessi sono: ";
				foreach ($categorieArray as $item){
            		$answer = $answer . "\r\n" . $item ;
        		}
				break;
			case '2':
				$answer = "Sei interessato a: ";
				foreach ($categorieArray as $item){
            		$answer = $answer . "\r\n" . $item ;
        		}
				break;
			default:
				$answer = "Ecco qui i tuoi interessi: ";
				foreach ($categorieArray as $item){
            		$answer = $answer . "\r\n" . $item ;
        		}
        		break;
		}

	}else{
		$answer = "Interessi non presenti";
	}

	//A volte la richiesta non restituisce nessun elenco perciò dovrà essere rifatta
	if ($answer == null) {
		$answer = "Errore nel caricamento degli interessi. Riprova.";
	}

	return $answer;

}