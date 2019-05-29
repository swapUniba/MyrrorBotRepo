<?php

#include "myrrorlogin.php";
function getCardio(){

$param = "?f=PhysicalStates";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {

if(isset($value1['heart'])){

   $max = -1;
   foreach ($value1['heart'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
       if($timestamp > $max){
       	
       	$result = $value2;
       	$max = $timestamp;

       }

     
 
   }
    
}

}

if(isset($result['restingHeartRate'])){
   $heart = $result['restingHeartRate'];
   
   switch (rand(1,3)) {
     case '1':
        $answer = "il tuo battito cardiaco è ".$heart; 
       break;

      case '2':
       $answer = "Ecco il tuo battito cardiaco: ".$heart;
       break;

     default:
      $answer = "Il tuo battito cardiaco in condizioni di riposo è ".$heart;
       break;
   }

}else{
  $answer = "informazione non trovata";
}

return $answer;

}




function getSleep(){

$param = "?f=PhysicalStates";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {

if(isset($value1['sleep'])){
$max = -1;
   foreach ($value1['sleep'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
       if($timestamp > $max){
       	
       	$result = $value2;
       	$max = $timestamp;
       	
       }

   }
    
}

}

if($result['minutesAsleep'] != null){

 $minutesAsleep = $result['minutesAsleep'];
 $timeinbed = $result['timeInBed'];

 switch (rand(1,3)) {

   case 1:
     $answer = "hai dormito ".$minutesAsleep." minuti su ".$timeinbed ." minuti trascorsi nel letto"; 
     break;

   case 2:
      $answer = " Oggi hai dormito ". $minutesAsleep ." minuti su ".$timeinbed." trascorsi nel letto";
     break;
   
   default:
     $answer = " Hai trascorso nel letto ".$timeinbed. " minuti dormendo per " .$minutesAsleep." minuti";
     break;
 }



}else{
  $answer = "informazione non trovata";
}

return $answer;

}