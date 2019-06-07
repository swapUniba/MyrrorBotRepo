<?php

function attivitaFisica($text,$confidence){

$param = "today";
$json_data = queryMyrror($param);
$result = null;
$max = 0;
$activity = array(0,0,0);
$answer = "";
foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];

       if($timestamp >= $max && $value2['nameActivity'] != "calories"  && $value2['nameActivity'] != "steps" && 
          $value2['nameActivity'] != "minutesSedentary"  && $value2['nameActivity'] != "distance"){

         	switch ($value2['nameActivity']) {
         	case 'fairly':

         		$activity[0] = $value2['minutesFairlyActive']; 		
                $max = $timestamp;

         		break;

         	case 'minutesLightlyActive':
         			
         		$activity[1] = $value2['minutesLightlyActive'];
         		$max = $timestamp;
         		break;

         	case 'veryActive':
         			
         		$activity[2] = $value2['minutesVeryActive'];
         		$max = $timestamp;
         		break;

         	default:
         		# code...
         		break;
         }

         }

	}
		
	}
}

if(rand(1,2) == 1){

	$answer = "ti sei allenato facendo <br>";

}else{

	$answer = "Ecco il tuo resoconto giornaliero: <br>";
}

$answer .= "Attività molto attiva:". $activity[2] ."minuti <br> "."Attività poco attiva: ".$activity[1] ."minuti <br> ";

$answer .= "Attività abbastanza attiva:". $activity[0]."minuti <br>";

if($activity[0] == 0 && $activity[1] == 0 && $activity[2] == 0){
	$answer = "oggi non ti sei allenato";
}


return $answer;

}

function getCalories(){

$param = "today";
$json_data = queryMyrror($param);
$result = null;


foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		$max = 0;
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] == "calories"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}

if( isset($result['activityCalories'])){
switch (rand(1,3)) {
	case 1:
		$answer = "Hai bruciato". $result['activityCalories']." calorie";
		break;

	case 2:
		$answer = "Le calorie che hai bruciato oggi sono:".$result['activityCalories'];
		break;
	
	default:
		$answer = "Ecco le tue calorie bruciate: " .$result['activityCalories'];
		break;
}	
}else{
	$answer = "informazione non trovata";
}

#echo $answer;

return $answer;

}

function getSteps(){

$param = "today";
$json_data = queryMyrror($param);
$result = null;
$max = -1;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] == "steps"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}

 if(isset($result['steps'])){
 	 $activityValue = $result['steps'];

 	switch ($result['steps']) {
 		case 1:
 			 $answer = "hai fatto un totale di ".$activityValue." passi"; 
 			break;

 		case 2:
 			$answer = "Ecco il tuo numero di passi giornalieri ".$activityValue;
 			break;

 		default:
 			$answer = "	Hai totalizzato " .$activityValue. " passi";
 			break;
 	}
    
}else{
	$answer = "informazione non trovata";
}


return $answer;


}

function getSedentary(){

$param = "today";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		$max = 0;
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         if($timestamp > $max && $value2['nameActivity'] == "minutesSedentary"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}

if(isset($result['minutesSedentary'])){

	$activityValue = $result['minutesSedentary'];
	if(rand(1,2) == 1){
        $answer = "sei stato sedentario per ".$activityValue." minuti"; 
	}else{
        $answer = "I Minuti sedentari trascorsi durante la giornata sono: ".$activityValue;
	}
	
}else{
	$answer = "informazione non trovata";
}

return $answer;



}