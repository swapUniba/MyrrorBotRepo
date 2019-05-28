<?php

//CONTATTI (Per adesso restituisce solamente l'elenco dei contatti)
function contatti($text,$confidence){

	$param = "?f=SocialRelations";
	$json_data = queryMyrror($param);

	$contactIdArray = array();

	foreach ($json_data as $key1 => $value1) {

		if($key1 == "socialRelations"){
			foreach ($value1 as $key => $value) {
				if (isset($value['contactId'])) {

					$contactId = $value['contactId']; //Prendo il contatto id

					$contactIdArray[] = $contactId; //Inserisco il contatto nell'array
				}
			}
        }	
    }

	return $contactIdArray;

}