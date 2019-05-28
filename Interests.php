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

	return $categorieArray;

}