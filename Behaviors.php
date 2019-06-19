<?php
/*
@startDate data iniziale dell'intervallo in cui cercare
@endDate data finale dell'intervallo in cui cercare
la funzione ricerca all'interno del file json i dati i minuti
di attività fisica svolti 'fairlyActive' 'lightlyActive' 'veryActive'.
Viene effettuata una media di tutti e 3 i valori e viene restituito un array con 
le medie dei 3 valori , in caso non vengano trovati viene restituito un array di 0
return array di valori con l'attività fisica media
*/
function attivitaInterval($startDate,$endDate){

 $activity = array(0,0,0);
 $param = "";
 $json_data = queryMyrror($param);
 $count =  array(0,0,0);
 $sum =  array(0,0,0);

	foreach ($json_data as $key1 => $value1) {

	if(isset($value1['fromActivity'])){
		
     	
	foreach ($value1['fromActivity'] as $key2 => $value2) {
        
             $timestamp = $value2['timestamp'];
          $date = date('Y-m-d',$timestamp/1000);
       
       if($date <= $endDate && $date >= $startDate && $value2['nameActivity'] != "calories" 
        && $value2['nameActivity'] != "steps" 	&& $value2['nameActivity'] != "minutesSedentary" 
         && $value2['nameActivity'] != "distance"){
   
            
         	switch ($value2['nameActivity']) {
         	case 'fairly':

         		$sum[0] += $value2['minutesFairlyActive'];
         		$count[0]++; 		
                

         		break;

         	case 'minutesLightlyActive':
         			
         		$sum[1] += $value2['minutesLightlyActive'];
         		$count[1]++;
         		
         		break;

         	case 'veryActive':
         			
         		$sum[2] += $value2['minutesVeryActive'];
         	    $count[2]++;

         		break;

         	default:
         		# code...
         		break;
         }

         }

	}
		
	}
}

if($count[0] != 0 && $count[1] != 0 && $count[2] != 0 ){
	 $activity[0] = intval($sum[0] / $count[0]);
	 $activity[1] = intval($sum[1] / $count[1]);
	 $activity[2] = intval($sum[2] / $count[2]);
}
    
return $activity;
}
/*
@data data da cercare nel file
il metodo effettua una ricerca all'interno del file json
della data specificata, se vengono trovati i dati dell'attività
fisica corrispondenti a quella data saranno restitiuti 
tramite un array i dati presenti alle voci minutesfairlyActive
minutesVeryActive e minutesLightlyActive.Se non ci sono informazioni
riguardanti la data scelta verranno presi i dati dell'ultima data disponibile
return array con i 3 valori riguardanti i minuti di attività fisica
e la data corrispondente 
*/
function attivitaData($data){

   $activity = array(0,0,0,"");
   $param = "";
   $json_data = queryMyrror($param);


foreach ($json_data as $key1 => $value1) {

	if(isset($value1['fromActivity'])){
		
     	
	foreach ($value1['fromActivity'] as $key2 => $value2) {
        
          $timestamp = $value2['timestamp'];
          $date = date('Y-m-d',$timestamp/1000);
       
       if($date == $data && $value2['nameActivity'] != "calories"  && $value2['nameActivity'] != "steps" && 
          $value2['nameActivity'] != "minutesSedentary"  && $value2['nameActivity'] != "distance"){
   
            $activity[3] = $date;
         	switch ($value2['nameActivity']) {
         	case 'fairly':

         		$activity[0] = $value2['minutesFairlyActive']; 		
                

         		break;

         	case 'minutesLightlyActive':
         			
         		$activity[1] = $value2['minutesLightlyActive'];
         		
         		break;

         	case 'veryActive':
         			
         		$activity[2] = $value2['minutesVeryActive'];
         	
         		break;

         	default:
         		# code...
         		break;
         }

         }

	}
		
	}
}

if($activity[0] == 0 && $activity[1] == 0 && $activity[2] == 0){
	//dati non trovati per il giorno selezionato

$max = 0;

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
 $activity[3] = date('Y-m-d',$max/1000);


}

return $activity;

}
/*
@resp risposta standard da Dialogflow
@parameters parametri con le info sulle date 
@text domanda scritta dall'utente
La funziona controlla parameters per verificare se c'è una sola
data oppure un intervallo di tempo, in base a questo vengono
chiamati metodi diversi attivitaInterval in caso di 
intervalli di tempo e attivitaData in caso di una singola data 
come i giorni di oggi e ieri. Tramite i dati ottenuti verrà
costruita una risposta
return risposta da stampare

*/
function attivitaFisica($resp,$parameters,$text){

$answer = "";

if(isset($parameters['date-period']['startDate'])){
	//dati periodo di tempo
$startDate = substr($parameters['date-period']['startDate'],0,10);
$endDate = substr($parameters['date-period']['endDate'],0,10);
    $arr = attivitaInterval($startDate,$endDate);
    if($arr[0] == 0 && $arr[1] == 0 && $arr[2] == 0){

    	 $activity = attivitaData($startDate);

    if($activity[0] == 0 && $activity[1] == 0 && $activity[2] ==  0)
    	return "non ti sei allenato";
	
	
		$answer = "gli ultimi dati disponibili sono del ".$activity[3]."<br>";
		$answer .= "Attività molto attiva:". $activity[2] ."minuti <br> "."Attività poco attiva: "
         .$activity[1] ."minuti <br> ";

        $answer .= "Attività abbastanza attiva:". $activity[0]."minuti <br><br>";

    }else{

       //risposta con intervallo 
    	$answer = "Ecco i tempi medi dei tuoi allenamenti:<br>";
    	$answer .= "Attività molto attiva: ".$arr[0]." minuti <br>";
        $answer .= "Attività poco attiva: ".$arr[1]." minuti <br>";
        $answer .= "Attività abbastanza attiva: ".$arr[2]." minuti <br>";


    }

}elseif (isset($parameters['date'])) {
	#dati oggi - ieri 


	$date = substr($parameters['date'],0,10);
    $activity = attivitaData($date);

    if($activity[0] == 0 && $activity[1] == 0 && $activity[2] ==  0)
    	return "non ti sei allenato";
	
	if($date  == $activity[3]){
        $answer = "ti sei allenato facendo <br>";
     
     $answer .= "Attività molto attiva:". $activity[2] ."minuti <br> "."Attività poco attiva: "
         .$activity[1] ."minuti <br> ";

     $answer .= "Attività abbastanza attiva:". $activity[0]."minuti <br><br>";

	}else{
		$answer = "gli ultimi dati disponibili sono del ".$activity[3]."<br>";
		$answer .= "Attività molto attiva:". $activity[2] ."minuti <br> "."Attività poco attiva: "
         .$activity[1] ."minuti <br> ";

        $answer .= "Attività abbastanza attiva:". $activity[0]."minuti <br><br>";


	}
	

}else{
	//ultimi dati trovati
  $activity = attivitaData("");

 
  $answer = "gli ultimi dati disponibili sono del ".$activity[3]."<br>";
  $answer .= "Attività molto attiva:". $activity[2] ."minuti <br> "."Attività poco attiva: "
        .$activity[1] ."minuti <br> ";

  $answer .= "Attività abbastanza attiva:". $activity[0]."minuti <br><br>";

}


return $answer;

}

/*
@resp risposta standard da Dialogflow
@parameters parametri con le info sulle date 
@text domanda scritta dall'utente
questa funzione se trova in parameters i dati su un periodo di 
tempo avvia attivitaInterval per ottenere i dati medi sull'attività
fisica, la risposta sarà costruita in base ai token
riconosciuti nella frase e sarà affermativa se il valore è maggiore di 30.
Se nei parametri c'è una sola data viene chiamata la funzione 
attivitaData dopo un controllo lessicale viene effettuato un 
controllo per verificare che l'attività fisica svolta sia maggiore di 30 
minuti costruendo così una risposta affermativa o negativa a seconda 
dei casi
return risposta da stampare 
*/
function attivitaFisicaBinary($resp,$parameters,$text){

if(isset($parameters['date-period']['startDate'])){

  $startDate = substr($parameters['date-period']['startDate'],0,10);
  $endDate = substr($parameters['date-period']['endDate'],0,10);
  $arr = attivitaInterval($startDate,$endDate);
  $sum = $arr[0] + $arr[1] + $arr[2];

  if(strpos($text, 'abbastanza')){
        
        if($sum >= 30 ){
           $answer ="Si, fai abbastanza attività fisica.In media ".$sum." minuti.";
        }else{
           $answer="No,non fai abbastanza attività fisica.In media ".$sum." minuti.";
        }
        
  }elseif(strpos($text,'dovrei fare')  || strpos($text,'fare di più') || strpos($text,'fare più')) {
  	
  	    if($sum >= 30 ){
           $answer ="No,fai abbastanza attività fisica.In media ".$sum." minuti.";
        }else{
           $answer="Si,non fai abbastanza attività fisica.In media ".$sum." minuti.";
        }
        
  }

}elseif (isset($parameters['date'])) {
   
   $date = substr($parameters['date'],0,10);
   $activity = attivitaData($date);
   $sum = $activity[0] + $activity[1] + $activity[2];

     if(strpos($text, 'abbastanza')){
        
        if($sum >= 30 ){
           $answer ="Si, hai fatto abbastanza attività fisica. ".$sum." minuti.";
        }else{
           $answer="No,non hai fatto abbastanza attività fisica. ".$sum." minuti.";
        }
        
  }elseif(strpos($text,'dovrei fare')  || strpos($text,'fare di più') || strpos($text,'fare più')) {
  	
  	    if($sum >= 30 ){
           $answer ="No,hai fatto abbastanza attività fisica. ".$sum." minuti.";
        }else{
           $answer="Si,non hai fatto abbastanza attività fisica. ".$sum." minuti.";
        }
        
  }



}else{

    $answer = "informazione non trovata";
}

return $answer;

}

/*
@startDate data iniziale dell'intervallo
@endDate data finale dell'intervallo
la funzione effettua una ricerca nel file json cercando le calorie 
bruciate dall'utente nel periodo di tempo specificato,
se non vengono trovate delle informazioni in quell'intervallo 
vengono presi in considerzione tutti i dati presenti nel file.
Viene effettuata così una media delle calorie bruciate
return media calorie bruciate 
*/
function caloriesInterval($startDate,$endDate){

$param = "";
$json_data = queryMyrror($param);
$result = null;
$sum = 0;
$count = 0;


foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
	
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($startDate <= $date2 && $date2 <= $endDate  && $value2['nameActivity'] == "calories"){
         
           $sum += $value2['activityCalories'];
           $count++;
         }

	}	
		
	}
}

if($count != 0){
   $result = intval($sum/$count);
}
else{
	$result = 0;
}

return $result;

}

function caloriesDay($data){

$param = "";
$json_data = queryMyrror($param);
$result = null;


foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
	
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($data == $date2  && $value2['nameActivity'] == "calories"){
        
           $result = $value2;
           

         }

	}
	
		
	}
}

if(isset($result['activityCalories'])){
   
   return $result['activityCalories'];

}else{

$max = 0;
foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
		
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($timestamp > $max  && $value2['nameActivity'] == "calories"){
         
           $result = $value2;
           $max = $timestamp;

         }

	}
	
		
	}
}


if(isset($result['activityCalories'])){
   
   return $result['activityCalories'];

}else{
	return 0;
}

}



}

/*
@resp risposta standard ricevuta da dialogflow
@parameters parametri con i dati sul tempo 
@text domanda dell'utente
la funzione analizza parameters e decide quale funzione 
invocare per costruire la risposta.
Se in parameters c'è un intervallo di date viene chiamata 
la funzione caloriesInterval, se invece viene trovata 
solo una singola data chiamiamo caloriesDay sostituendo in resp
il valore corrispondente dei battiti.
return risposta da stampare
*/
function getCalories($resp,$parameters,$text){

$answer = "";
if(isset($parameters['date-period']['startDate'])){
	//dati periodo di tempo
$startDate = substr($parameters['date-period']['startDate'],0,10);
$endDate = substr($parameters['date-period']['endDate'],0,10);

$calAv = caloriesInterval($startDate,$endDate);

	$answer = "In media hai bruciato ".$calAv." calorie";

	


}else{
	if ($parameters['date']) {
	$date = substr($parameters['date'],0,10);
}else{
	$date = date('Y-m-d');
}

$cal = caloriesDay($date);

    $answer = str_replace('X', $cal, $resp);

}

return $answer;

}


/*
@resp risposta standard ricevuta da dialogflow
@parameters parametri con i dati sul tempo 
@text domanda dell'utente
la funzione effettua il calcolo del fabbisogno giornaliero
dell'uomo, a seconda dei parametri verrà chiamata la funzione
per gli intervallli di tempo caloriesInterval oppure
quella per i giorni singoli caloriesDay.
Successivamente vengono analizzati i token presenti nella frase,
se il valore delle calorie bruciate è maggiore del fabbisogno 
energetico l'utente ha bruciato abbastanza calorie
la risposta viene formulata di conseguenza.
return risposta da stampare 
*/
function getCaloriesBinary($resp,$parameters,$text){

$peso = 80;
$eta = 22;
$altezza = 185;

$metabolismo = 66.5 + (13.8 * $peso) + (5 * $altezza) - (6.8 * $eta);


if(isset($parameters['date-period']['startDate'])){
	//dati periodo di tempo
$startDate = substr($parameters['date-period']['startDate'],0,10);
$endDate = substr($parameters['date-period']['endDate'],0,10);

$calAv = caloriesInterval($startDate,$endDate);

if(strpos($text, 'abbastanza')){

	if($calAv >= $metabolismo)
	 $answer = "si, bruci abbastanza calorie";
    else
	 $answer = "no,dovresti bruciare più calorie";

}elseif (strpos($text, 'più')) {

	if($calAv >= $metabolismo)
	 $answer = "no, bruci abbastanza calorie";
    else
	 $answer = "si,dovresti bruciare più calorie";

}

	


}else{

	if ($parameters['date']) {
	$date = substr($parameters['date'],0,10);
    }else{
	$date = date('Y-m-d');
    }

$cal = caloriesDay($date);



if(strpos($text, 'abbastanza')){

	if($cal >= $metabolismo)
	 $answer = "si,hai bruciato abbastanza calorie";
    else
	 $answer = "no,dovresti bruciare più calorie";

}elseif (strpos($text, 'più')) {

	if($cal >= $metabolismo)
	 $answer = "no,hai bruciato abbastanza calorie";
    else
	 $answer = "si,dovresti bruciare più calorie";

}

}

return $answer;
}

/*
@day giorno da cercare
la funzione cerca nel file il numero di passi in 
un determinato giorno e li restituisce in output.
Se non vengono trovati i dati allora viene preso in 
considerazioe l'ultimo giorno disponibile.
*/
function stepsDay($day){

$result = null;
$param = "";
$json_data = queryMyrror($param);

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
			
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($day == $date2 && $value2['nameActivity'] == "steps"){     
           $result = $value2;   
         }
	}
		
	}
}

if(isset($result['steps'])){

return  array($day,$result['steps']);


}else{

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
 $date2 = date('Y-m-d',$max/1000);
return  array($date2,$result['steps']);

}else{
	return array("",0);
}

}




}
/*
@startDate data iniziale dell'intervallo
@endDate data finale dell'intervallo
la funzione effettua una ricerca del numero di passi 
effettuati dall'utente in un determinato intervallo di tempo
se non vengono trovati dati in quell'intervallo vengono
considerati i dati nell'intero file.
Viene effettuata una media dei passi effettuati.
return media passi

*/
function stepsInterval($startDate,$endDate){

$result = null;
$param = "";
$json_data = queryMyrror($param);
$sum = 0;
$count = 0;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
			
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($startDate <= $date2 && $date2 <= $endDate && $value2['nameActivity'] == "steps"){     
           $sum += $value2['steps'];
           $count++;

         }
	}
		
	}
}

if ($count != 0) {
	return array($startDate,intval($sum/$count));
}else{

    $sum = 0;
	foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
			
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if( $value2['nameActivity'] == "steps"){     
           $sum += $value2['steps'];
           $count++;

         }
	}
		
	}
}
	
if ($count != 0) {
	return array("",intval($sum/$count));
}else{
    return array("",0);
}

}

}
/*
@resp risposta standard ricevuta da dialogflow
@parameters parametri con i dati sul tempo 
@text domanda dell'utente
La funzione se in parameters è presente un intervallo di tempo
chiama il metodo stepsInterval per ottnere una media dei passi 
effettuati e costruisce la risposta di conseguenza;
 se è presente una singola data viene chiamata stepsDay
che restituisce i passi nel giorno selezionato in questo caso verrà
utilizzata la risposta resp a cui verranno aggiunti i dati otteuti,
return risposta da stampare
*/
function getSteps($resp,$parameters,$text){

if(isset($parameters['date-period']['startDate'])){

$startDate = substr($parameters['date-period']['startDate'],0,10);
$endDate = substr($parameters['date-period']['endDate'],0,10);

$arr = stepsInterval($startDate,$endDate);

if($arr[0] == $startDate){
	//risposta corretta
	$answer = "hai fatto una media di ".$arr[1]." passi giornalieri";

}else{
	//intervallo completo
     $answer = "i dati presenti non risalgono al periodo indicato. <br>";
     $answer .= "in media fai ".$arr[1]." passi giornalieri";
}
}else{

if(isset($parameters['date'])){

$date = substr($parameters['date'],0,10);
$arr = stepsDay($date);

if($arr[0] == $date){
	$answer = str_replace('X', $arr[1], $resp);
}else{
	$answer = "gli ultimi dati disponibili risalgono al ".$arr[0]." <br>";
	$answer .= str_replace('X', $arr[1], $resp);
}

}else{

$today = date('Y-m-d');
$arr = stepsDay($today);

if($arr[0] == $date){
	$answer = str_replace('X', $arr[1], $resp);
}else{
	$answer = "gli ultimi dati disponibili risalgono al ".$arr[0]." <br>";
	$answer .= str_replace('X', $arr[1], $resp);
}

}
}

return $answer;

}
/*
@resp risposta standard ricevuta da dialogflow
@parameters parametri con i dati sul tempo 
@text domanda dell'utente
La funzione prende inizialmetnte una media dei passi presenti nel file
Se in parameters è presente un intervallo di tempo
chiama il metodo stepsInterval per ottnere una media dei passi 
effettuati nella costruzione della risposta verifica che la media dei passi
nel periodo selezionato sia maggiore della media generale, in tal caso la 
risposta sarà affermativa.
Se è presente una singola data viene chiamata stepsDay
che restituisce i passi nel giorno selezionato in questo caso verrà
confrontato il numero di passi ottenuti con la media ricavata in precedenza,
se questo valore è maggiore la risposta sarà affermativa.
return risposta da stampare
*/
function getStepsBinary($resp,$parameters,$text){

$answer = "";
//media generale passi
$average = stepsInterval("","");

if(isset($parameters['date-period']['startDate'])){

$startDate = substr($parameters['date-period']['startDate'],0,10);
$endDate = substr($parameters['date-period']['endDate'],0,10);

$intAv = stepsInterval($startDate,$endDate);

if($intAv[0] == $startDate){

  if($intAv[1] >= $average[1]){
     $answer = "Si, hai fatto abbastanza passi. La tua media giornaliera è di ".$intAv[1]." passi";
  }else{
     $answer = "No,non hai fatto abbastanza passi. La tua media giornaliera è di ".$intAv[1]." passi";
  }

}else{

$answer = "non ci sono dati risalenti al periodo specificato <br> ";
  
 $answer .= " La tua media giornaliera è di ".$intAv[1]." passi";
  
  
  

}


}elseif(isset($parameters['date'])){

$date = substr($parameters['date'], 0,10);
$arr = stepsDay($date);

if($arr[1] >= $average[1]){

$answer = "Si,hai fatto abbastanza passi.Ne hai fatti ".$arr[1];	
}else{
$answer = "No,non hai fatto abbastanza passi.Ne hai fatti ".$arr[1];	
}

}else{

$date = date('Y-m-d');
$arr = stepsDay($date);
$answer = "Gli ultimi dati disponibili risalgono al ".$arr[0]."<br>";	
if($arr[1] >= $average[1]){
$answer .= "hai effettuato abbastanza passi ".$arr[1];
}else{
$answer .= "non hai effettuato abbastanza passi ".$arr[1];	
}

}

return $answer;

}

/*
@resp risposta standard ricevuta da dialogflow
@parameters parametri con i dati sul tempo 
@text domanda dell'utente
La funzione in base ai valori presenti in parameters decide se
invocare sedentaryDay per i dati di un singolo giorno, altrimenti
se non ci sono informazioni sulla data viene invocata sendetaryDay 
con la data di oggi, infine costruisce la risposta 
sostituendo i valori di sedentarietà ottenuti a resp, 
return risposta
*/
function getSedentary($resp,$parameters,$text){


$answer = "";
$date = null;
if(isset($parameters['date'])){
$date = substr($parameters['date'], 0,10);
$arr = SedentaryDay($date);


}else{
	$date = date('Y-m-d');
    $arr = SedentaryDay($date);
}

if($arr[0] == $date){
//dati giorno scelto
$answer = str_replace('X', $arr[1], $resp);

}else{
//ultimi dati
$answer = "gli ultimi dati risalgono al ".$arr[0]. " <br> ";
$answer .= str_replace('X', $arr[1], $resp);

}


return $answer;



}

/*
@resp risposta standard ricevuta da dialogflow
@parameters parametri con i dati sul tempo 
@text domanda dell'utente
La funzione prende i dati dell'ultima settimana
presenti nel file json chiamando sedentaryInterval
La risposta viene costruita analizzando i token presenti
nella frase, se i minuti di sedentarietà in una settimana saranno
9930 l'utente sarà definito sedentario
return risposta da stampare

*/

function getSedentaryBinary($resp,$parameters,$text){

$answer = "";
$startWeek = date("Y-m-d",strtotime("-7 days"));
$endWeek = date('Y-m-d');

$result = sedentaryInterval($startWeek,$endWeek);

if($result == null){
 return "non ci sono dati nell'ultima settimana";	
}

if(strpos($text, 'seduto') || strpos($text, 'fermo') || strpos($text, 'sedentario')){
 
 if($result >= 9930)
 	$answer = "Si, sei sedentario";
 else
 	$answer = "No,sei attivo";
   
}else{

 if($result >= 9930)
 	$answer = "No, sei sedentario";
 else
 	$answer = "Si,sei attivo";

}
 
 return $answer;

}
/*
@startDate data iniziale intervallo
@endDate data finale intervallo
la funziona cerca tutti i dati sui minuti di sedentarietà
nell' intervallo di tempo specificato, i minuti vengono
sommati in result e vengono restituiti 
*/
function sedentaryInterval($startDate,$endDate){

$param = "";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
	
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($date2 >= $startDate && $date2 <= $endDate && $value2['nameActivity'] == "minutesSedentary"){
           $result += $value2["minutesSedentary"];
         }
	}
		
	}
}

return $result;



}
/*
@date data da cerare nel file
la funzione cerca i minuti di sedentarietà nel file
corrispondenti alla data passata come parametro se li
trova li restituisce altrimenti restituisce i dati dell'ultima data
disponibile
*/
function SedentaryDay($date){


$param = "";
$json_data = queryMyrror($param);
$result = null;

foreach ($json_data as $key1 => $value1) {
	
	if(isset($value1['fromActivity'])){
	
		
	foreach ($value1['fromActivity'] as $key2 => $value2) {
     
         $timestamp = $value2['timestamp'];
         $date2 = date('Y-m-d',$timestamp/1000);
         if($date2 == $date && $value2['nameActivity'] == "minutesSedentary"){
           $result = $value2;
         }
	}
		
	}
}

if(isset($result['minutesSedentary'])){

 return array($date,$result['minutesSedentary']);

}else{

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

 $date2 = date('Y-m-d',$timestamp/1000);
return array($date2,$result['minutesSedentary']);

}else{

return array("",0);

}


}

}



//Prendo gli ultimi dati sull'attività fisica
function getLastAttivitaFisica($resp,$parameters,$text){

  $valori = array(); //Array che contiene i valori sull'attività fisica

  //Ultimi dati trovati
  $activity = attivitaData("2019-06-19");

  $valori = [
    'abbastanzaAttiva' => $activity[0],
    'pocoAttiva' => $activity[1],
    'moltoAttiva' => $activity[2],
  ];

  return $valori;

}