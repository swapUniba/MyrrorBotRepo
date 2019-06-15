<?php

#include "myrrorlogin.php";
function cardioToday($parameters,$data){

$param = "";
$json_data = queryMyrror($param);
$result = null;
$dateR = null;

foreach ($json_data as $key1 => $value1) {

if(isset($value1['heart'])){

   foreach ($value1['heart'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
       $tempDate = date('Y-m-d',$timestamp/1000);
      
       if($tempDate == $data){        
        $result = $value2;
        $dateR = $tempDate;
       }
   }
}

}

if(isset($result['restingHeartRate'])){
   $heart = $result['restingHeartRate'];

}else{

foreach ($json_data as $key1 => $value1) {

if(isset($value1['heart'])){

   $max = -1;
   foreach ($value1['heart'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
        $tempDate = date('Y-m-d',$timestamp/1000);
       if($timestamp > $max){
        
        $result = $value2;
        $max = $timestamp;
        $dateR = $tempDate;

       }
   }
}

}

if(isset($result['restingHeartRate'])){
   $heart = $result['restingHeartRate'];

}else{
  $heart = 0;
}

}

return  array('date' => $dateR, 'heart' => $heart);

}

function cardioInterval($startDate,$endDate){

$param = "";
$json_data = queryMyrror($param);
$count = 0;
$sum = 0;

foreach ($json_data as $key1 => $value1) {

if(isset($value1['heart'])){

   foreach ($value1['heart'] as $key2 => $value2) {
   
       $timestamp = $value2['timestamp'];
       $tempDate = date('Y-m-d',$timestamp/1000);
       if($tempDate <= $endDate && $tempDate >= $startDate){        
        $sum  += $value2['restingHeartRate'];
        $count++;
       }
   }
}

}

if ($count != 0) {
  $average = $sum/$count;
}else{
  $average = 0;
}

return $average;

}

function getCardio($resp,$parameters,$text){


$answer = "";
$today = date("Y-m-d"); 
$yesterday = date("Y-m-d",strtotime("-1 days")); 


if(isset($parameters['date']) ){

  $date1 = substr($parameters['date'],0,10);
  if($today ==  $date1){
    //dati oggi
    $arr = cardioToday($parameters,$today);
    
    if($arr['date'] == $today){
      //risposta oggi
      $answer = str_replace('X',$arr['heart'],$resp);
    }else{
      //risposta standard
      $answer = "gli ultimi dati disponibili sono del ".$arr['date']
      ." battito cardiaco ".$arr['heart']." bpm";
    }

  }elseif($yesterday ==  $date1){
    //dati ieri
     $arr = cardioToday($parameters,$yesterday);
      if($arr['date'] == $yesterday){
      //risposta oggi
      $answer = "il tuo battito cardiaco era ".$arr['heart']." bpm";
    }else{
      //risposta standard
      $answer = "gli ultimi dati disponibili sono del ".$arr['date']
      ." battito cardiaco ".$arr['heart']." bpm";
    }
 }elseif(isset($parameters['date-period']['startDate'])){
     //dati ultimo giorno trovato
  $startDate =  substr($parameters['date-period']['startDate'],0,10);
  $endDate =  substr($parameters['date-period']['endDate'],0,10);
  $average = cardioInterval($startDate,$endDate);
  if($average != 0){
    $answer = "In media, il tuo battito cardiaco è ".$average." bpm.";
  }else{
    $arr = cardioToday($parameters,"");
    $answer = "gli ultimi dati disponibili sono del ".$arr['date']
      ." battito cardiaco ".$arr['heart']." bpm";
  }

  }else{
     $arr = cardioToday($parameters,"");
     $answer = "gli ultimi dati disponibili sono del ".$arr['date']
      ." battito cardiaco ".$arr['heart']." bpm";
  }

}elseif (isset($parameters['date-period']['startDate'])) {
  //dati intervallo di tempo
  
  $startDate =  substr($parameters['date-period']['startDate'],0,10);
  $endDate =  substr($parameters['date-period']['endDate'],0,10);
  $average = cardioInterval($startDate,$endDate);
  if($average != 0){
    $answer = "In media, il tuo battito cardiaco è ".$average." bpm.";
  }else{
    $arr = cardioToday($parameters,"");
    $answer = "gli ultimi dati disponibili sono del ".$arr['date']
      ." battito cardiaco ".$arr['heart']." bpm";
  }

}else{
 //dati ultimo giorno trovato
   $arr = cardioToday($parameters,"");
   $answer = "gli ultimi dati disponibili sono del ".$arr['date']
      ." battito cardiaco ".$arr['heart']." bpm";
}



return $answer;

}

function getCardioBinary($resp,$parameters,$text){

$answer = "";
$today = date("Y-m-d");
//$today = "2019-03-27";
$yesterday = date("Y-m-d",strtotime("-1 days")); 
if(isset($parameters['date-period']['startDate'])){

  $startDate =  substr($parameters['date-period']['startDate'],0,10);
  $endDate =  substr($parameters['date-period']['endDate'],0,10);
  $average = cardioInterval($startDate,$endDate);

  if($average == 0){
    $answer = "non sono stati trovati dati nel periodo selezionato"; 
  }else{
      if(strpos($text, 'buono') || strpos($text, 'buone') || strpos($text, 'bene') || strpos($text, 'ottimo') || strpos($text, 'nella norma') || strpos($text, 'buona')){
   
   if($average >= 60 && $average <= 100){
     $answer = "Si, in media le tue pulsazioni sono nella norma: ".$average." bpm";
   }else{
     $answer = "No, in media le tue pulsazioni non sono nella norma: ".$average." bpm";
   }



  }elseif (strpos($text, 'pessimo') || strpos($text, 'cattivo') || strpos($text, 'cattive') ||
   strpos($text, 'male ') || strpos($text, 'fuori norma') ) {
    
    if($average >= 60 && $average <= 100){
     $answer = "No, in media le tue pulsazioni sono nella norma: ".$average." bpm";
   }else{
     $answer = "Si, in media le tue pulsazioni non sono nella norma: ".$average." bpm";
   }

  }
  }

}elseif (isset($parameters['date'])) {
  $date1 = substr($parameters['date'],0,10);
  switch ($date1) {
    case $today:

      $arr = cardioToday($parameters,$today);

    if(strpos($text, 'buono') || strpos($text, 'buone') || strpos($text, 'bene') || strpos($text, 'ottimo') 
      || strpos($text, 'nella norma') || strpos($text, 'buona')){

      if($arr['date'] == $today){

          if($arr['heart'] >= 60 && $arr['heart'] <= 100)
            $answer = "Si,le tue pulsazioni sono nella norma: ".$arr['heart']." bpm";
          else
             $answer = "No,le tue pulsazioni non sono nella norma ".$arr['heart']." bpm";
      }else{

         if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile".$arr['date'].
                      "le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile".$arr['date'].
             "le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
           
      }
              
      }elseif (strpos($text, 'pessimo') || strpos($text, 'cattivo') || strpos($text, 'cattive') ||
             strpos($text, 'male ') || strpos($text, 'fuori norma') ) {

         if($arr['date'] == $today){
          if($arr['heart'] >= 60 && $arr['heart'] <= 100)
            $answer = "No,le tue pulsazioni sono nella norma: ".$arr['heart']." bpm";
          else
             $answer = "Si,le tue pulsazioni non sono nella norma ".$arr['heart']." bpm";
      }else{
         if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile".$arr['date'].
                      "le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile".$arr['date'].
             "le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
           
      }

      }

      break;
  case $yesterday:
    
      $arr = cardioToday($parameters,$yesterday);  
      if(strpos($text, 'buono') || strpos($text, 'buone') || strpos($text, 'bene') || strpos($text, 'ottimo') || strpos($text, 'nella norma') || strpos($text, 'buona')){

      if($arr['date'] == $yesterday){
          if($arr['heart'] >= 60 && $arr['heart'] <= 100)
            $answer = "Si,le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
          else
             $answer = "No,le tue pulsazioni non erano nella norma ".$arr['heart']." bpm";
      }else{
         if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile ".$arr['date'].
                      " le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile ".$arr['date'].
             " le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
           
      }
              
      }elseif (strpos($text, 'pessimo') || strpos($text, 'cattivo') || strpos($text, 'cattive') ||
             strpos($text, 'male ') || strpos($text, 'fuori norma') ) {

         if($arr['date'] == $yesterday){
          if($arr['heart'] >= 60 && $arr['heart'] <= 100)
            $answer = "No,le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
          else
             $answer = "Si,le tue pulsazioni non erano nella norma ".$arr['heart']." bpm";
      }else{
         if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile ".$arr['date'].
                      " le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile ".$arr['date'].
             " le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
           
      }

      }
   
      break;
    default:
         //ultima data disponibile
         $arr = cardioToday($parameters,"");
        if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile ".$arr['date'].
                      " le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile ".$arr['date'].
             " le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
      break;
  }
  
}else{
     $arr = cardioToday($parameters,$today);
         if(strpos($text, 'buono') || strpos($text, 'buone') || strpos($text, 'bene') || strpos($text, 'ottimo') || strpos($text, 'nella norma') || strpos($text, 'buona')){

      if($arr['date'] == $today){
          if($arr['heart'] >= 60 && $arr['heart'] <= 100)
            $answer = "Si,le tue pulsazioni sono nella norma: ".$arr['heart']." bpm";
          else
             $answer = "No,le tue pulsazioni non sono nella norma ".$arr['heart']." bpm";
      }else{
         if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile ".$arr['date'].
                      " le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile ".$arr['date'].
             " le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
           
      }
              
      }elseif (strpos($text, 'pessimo') || strpos($text, 'cattivo') || strpos($text, 'cattive') ||
             strpos($text, 'male ') || strpos($text, 'fuori norma') ) {

         if($arr['date'] == $today){
          if($arr['heart'] >= 60 && $arr['heart'] <= 100)
            $answer = "No,le tue pulsazioni sono nella norma: ".$arr['heart']." bpm";
          else
             $answer = "Si,le tue pulsazioni non sono nella norma ".$arr['heart']." bpm";
      }else{
         if($arr['heart'] >= 60 && $arr['heart'] <= 100){
            $answer = "nell'ultima data disponibile ".$arr['date'].
                      " le tue pulsazioni erano nella norma: ".$arr['heart']." bpm";
         }else{

             $answer = "nell'ultima data disponibile ".$arr['date'].
             " le tue pulsazione non erano nella norma ".$arr['heart']." bpm";
         }
           
      }

      }
}

return $answer;

}


function getSleepBinary($resp,$parameters,$text){


$yesterday = date("Y-m-d",strtotime("-1 days")); 
if(isset($parameters['date'])  ||  isset($parameters['Passato'])){
$date1 = substr($parameters['date'],0,10);

if($date1 >= $yesterday){
//dati di ieri
  
 $answer = yestSleepBinary($resp,$parameters,$text,$yesterday);


}else if($parameters['Passato']){
  //dati di ieri
  
  $answer = yestSleepBinary($resp,$parameters,$text,$yesterday);
  //$answer = yestSleepBinary($resp,$parameters,$text,'2019-02-22');

}else{
  //dati storici
  $answer = pastSleepBinary($resp,$parameters,$text);
}

}else{
  //dati storici
   $answer = pastSleepBinary($resp,$parameters,$text);
}

return $answer;

}

function pastSleepBinary($resp,$parameters,$text){

$param = "";
$json_data = queryMyrror($param);
$result = "";

$count = 0;
$sumInBed = 0;
$sumAsleep = 0;
foreach ($json_data as $key1 => $value1) {
  if(isset($value1['sleep'])){
    
       //ricerca per periodo   
   foreach ($value1['sleep'] as $key2 => $value2) {
        
       $sumInBed += $value2['timeInBed'];
       $sumAsleep += $value2['minutesAsleep'];
       $count++;         
}
}

}

if($count == 0){
  //non ci sono riferimenti per quel periodo
  return "non sono stati trovati dati";
}
$asleepAV = intval($sumAsleep/$count);
$inBedAV =intval($sumInBed/$count);

if(strpos($text, 'abbastanza')){

   if($asleepAV >= 390)
     $result = "Si, dormi abbastanza. In media dormi " .$asleepAV. " minuti ";
   else
     $result = "No,non dormi abbastanza. In media dormi " .$asleepAV. " minuti ";

}elseif (strpos($text, 'tanto')) {

   if($asleepAV >= 390)
     $result = "Si, dormi tanto. In media dormi " .$asleepAV. " minuti ";
   else
     $result = "No,non dormi tanto. In media dormi " .$asleepAV. " minuti ";

}elseif (strpos($text, 'bene')) {

   if($asleepAV >= 390)
     $result = "Si, dormi bene. In media dormi " .$asleepAV. " minuti ";
   else
     $result = "No,non dormi bene. In media dormi " .$asleepAV. " minuti ";

}elseif (strpos($text, 'di meno')) {

      if($asleepAV >= 480)
     $result = "Si, dovresti dormire di meno. In media dormi " .$asleepAV. " minuti ";
   else
     $result = "No,dormi abbastanza. In media dormi " .$asleepAV. " minuti ";

}elseif (strpos($text, 'di più')) {

   if($asleepAV >= 390)
     $result = "No,non dovresti dormire di più. In media dormi " .$asleepAV. " minuti ";
   else
     $result = "Si,dovresti dormire di più. In media dormi " .$asleepAV. " minuti ";
  
}elseif (strpos($text, 'poco')){
  
   if($asleepAV >= 390)
     $result = "No, dormi abbastanza. In media dormi " .$asleepAV. " minuti ";
   else
     $result = "Si,dovresti dormire di più. In media dormi " .$asleepAV. " minuti ";

}else{
   $result = " dormi in media ". $asleepAV ." minuti ";
}

 return $result;
}

function yestSleepBinary($resp,$parameters,$text,$data){

$param = "";
$json_data = queryMyrror($param);
$result = null;
$flag = false;

//cerco data di ieri
foreach ($json_data as $key1 => $value1) {
  if(isset($value1['sleep'])){

        foreach ($value1['sleep'] as $key2 => $value2) {

             $timestamp = $value2['timestamp'];
             $tempDate = date('Y-m-d',$timestamp/1000);
             if($data == $tempDate)
               $result = $value2;
        }
  }
}

if($result['minutesAsleep'] != null){
  //risposta con data di ieri corretta
 $minutesAsleep = $result['minutesAsleep'];
 $timeinbed = $result['timeInBed'];
 $flag = true;

}else{
//risposta standard con ultima data
//algoritmo ultima data
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

$data2 = date('Y-m-d',$timestamp/1000);

if($result['minutesAsleep'] != null){
 $data = $data2;
 $minutesAsleep = $result['minutesAsleep'];
 $timeinbed = $result['timeInBed'];

}else{
  return "informazione non trovata";
}

}


if(strpos($text, 'abbastanza') ){

  if($minutesAsleep >= 390 ){
     
     if($flag == true){
        $answer = "Si, hai dormito abbastanza. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data.", hai dormito abbastanza "
        .$minutesAsleep." minuti";
     }
     
  }else{
      if($flag == true){
        $answer = "No,non hai dormito abbastanza. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data." e non hai dormito abbastanza "
        .$minutesAsleep." minuti";
     }

  }

}elseif( strpos($text, 'bene')){

    if($minutesAsleep >= 390 ){
     
     if($flag == true){
        $answer = "Si, hai dormito bene. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data.", hai dormito bene "
        .$minutesAsleep." minuti";
     }
     
  }else{
      if($flag == true){
        $answer = "No,non hai dormito bene. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data." e non hai dormito bene "
        .$minutesAsleep." minuti";
     }

  }

}elseif (strpos($text, 'tanto')) {

    if($minutesAsleep >= 390 ){
     
     if($flag == true){
        $answer = "Si, hai dormito tanto. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data.", hai dormito tanto "
        .$minutesAsleep." minuti";
     }
     
  }else{
      if($flag == true){
        $answer = "No,non hai dormito tanto. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data." e non hai dormito tanto "
        .$minutesAsleep." minuti";
     }

  }
  
}elseif(strpos($text, 'meno')){

    if($minutesAsleep >= 480 ){
     
     if($flag == true){
        $answer = "Si, dovresti dormire di meno. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data.",dovresti dormire di meno. Hai dormito "
        .$minutesAsleep." minuti";
     }
     
  }else{
      if($flag == true){
        $answer = "No,non hai dormito abbastanza. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data." e non hai dormito abbastanza "
        .$minutesAsleep." minuti";
     }

  }

}elseif(strpos($text,'di più')){

      if($minutesAsleep >= 390 ){
     
     if($flag == true){
        $answer = "No, non dovresti dormire di più. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data.",non dovresti dormire di più.Hai dormito "
        .$minutesAsleep." minuti";
     }
     
  }else{
      if($flag == true){
        $answer = "Si, dovresti dormire di più. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data."  dovresti dormire di più.Hai dormito "
        .$minutesAsleep." minuti";
     }

  }

}elseif (strpos($text,'poco')) {

      if($minutesAsleep >= 390 ){
     
     if($flag == true){
        $answer = "No, hai dormito abbastanza. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data.",hai dormito abbastanza "
        .$minutesAsleep." minuti";
     }
     
  }else{
      if($flag == true){
        $answer = "Si, dovresti dormire di più. Hai dormito ". $minutesAsleep ." minuti ";
     }else{
        $answer ="gli ultimi dati disponibili risalgono a ".$data." dovresti dormire di più.Hai dormito "
        .$minutesAsleep." minuti";
     }

  }

}else{
      $answer = "hai dormito ". $minutesAsleep ." minuti ";
  }

return $answer;

}


function fetchYesterdaySleep($resp,$data){

$param = "";
$json_data = queryMyrror($param);
$result = null;

//cerco data di ieri
foreach ($json_data as $key1 => $value1) {
  if(isset($value1['sleep'])){

        foreach ($value1['sleep'] as $key2 => $value2) {

             $timestamp = $value2['timestamp'];
             $tempDate = date('Y-m-d',$timestamp/1000);
             if($data == $tempDate)
               $result = $value2;
        }
  }
}

if($result['minutesAsleep'] != null){
  //risposta con data di ieri corretta
 $minutesAsleep = $result['minutesAsleep'];
 $timeinbed = $result['timeInBed'];
 $answer = str_replace('X',$minutesAsleep,$resp);
 $answer = str_replace('Y', $timeinbed, $answer);
 return $answer;

}else{
//risposta standard con ultima data
//algoritmo ultima data
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

$data2 = date('Y-m-d',$timestamp/1000);
$answer = "i dati sono stati selezionati in base all'ultima data disponiblie ".$data2."<br>";
if($result['minutesAsleep'] != null){
  $answer .= $resp;

 $minutesAsleep = $result['minutesAsleep'];
 $timeinbed = $result['timeInBed'];
 $answer = str_replace("X",$minutesAsleep,$answer);
 $answer = str_replace('Y', $timeinbed, $answer);

}else{
  $answer = "informazione non trovata";
}

 return $answer;

}

}

function getSleep($resp,$parameters,$text){

$yesterday = date("Y-m-d",strtotime("-1 days")); 
$timestamp = strtotime($yesterday);



if(isset($parameters['date'])  ||  isset($parameters['Passato']) || isset($parameters['date-period']) ){
$date1 = substr($parameters['date'],0,10);

//echo $yesterday;
if($date1 == $yesterday){
  //dati di ieri 
 $answer = fetchYesterdaySleep($resp,$yesterday);
  //$answer = fetchYesterdaySleep($resp,'2019-02-22');
}else if(isset($parameters['date-period']['endDate']) && isset($parameters['date-period']['startDate'])){
 
 
 
foreach ($parameters['date-period'] as $keyP => $valueP) {

  if($keyP == 'endDate' )
    $endDate = substr($valueP,0,10);
  else
    $startDate = substr($valueP,0,10);
  
}

$answer = fetchPastSleep($endDate,$startDate);

}else if(isset($parameters['Passato'])){
//dati di ieri
   
$answer = fetchYesterdaySleep($resp,$yesterday);

}else{
   
//dati storici
  $answer = fetchPastSleep("","");
}

}else{
  
//dati storici
  $answer = fetchPastSleep("","");
}

return $answer;

}

function fetchPastSleep($endDate,$startDate){

$param = "";
$json_data = queryMyrror($param);
$result = "";

$count = 0;
$sumInBed = 0;
$sumAsleep = 0;
foreach ($json_data as $key1 => $value1) {
  if(isset($value1['sleep'])){
     if($endDate != "" && $startDate != ""){
       //ricerca per periodo
      
    foreach ($value1['sleep'] as $key2 => $value2) {
      $timestamp = $value2['timestamp']; 
      $data = date('Y-m-d',$timestamp/1000);

      if($data >= $startDate && $data <= $endDate){
         $sumInBed += $value2['timeInBed'];
         $sumAsleep += $value2['minutesAsleep'];
         $count++;  
      }
    }
      $result = "dal ".$startDate ." al ".$endDate;

     }else{


   foreach ($value1['sleep'] as $key2 => $value2) {
   
       
       $sumInBed += $value2['timeInBed'];
       $sumAsleep += $value2['minutesAsleep'];
       $count++;      
       
       
   }   
}
}


}

if($count == 0){
  //non ci sono riferimenti per quel periodo
  return fetchPastSleep("","");
}
$asleepAV = intval($sumAsleep/$count);
$inBedAV =intval($sumInBed/$count);

$result .= " in media hai dormito ".$asleepAV ." minuti trascorrendo nel letto ".$inBedAV." minuti";
return $result;


}







