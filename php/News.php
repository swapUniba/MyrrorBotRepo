<?php
/*
Parameters contiene gli argomenti restituiti da dialogflow
Tramite parameters controlliamo quale sia il parametro individuato
da dialogflow e costruiamo di conseguenza il link di ricerca in 
base alla categoria.
Se viene trovato almeno un articolo della categoria scelta il suo
URL verrà restituito , altrimenti viene restituito un messaggio d'errore
*/
function getNewsTopic($parameters){

$link = "https://newsapi.org/v2/top-headlines?country=it&category=";
$apiKey = "&apiKey=17c1953c3cc7450d958ff14f9e262c02";
$val = "";

if ($parameters['Sports'] != null) {
	$val = $parameters['Sports'];
	$link .= "Sports&q=".$val. $apiKey;
}elseif ($parameters['Health'] != null) {
	$val = $parameters['Health'];
	$link .= "Health&q=".$val. $apiKey;
}elseif ($parameters['Science'] != null) {
	$val = $parameters['Science'];
	$link .= "Science".$apiKey;
}elseif ($parameters['Entertainment'] != null) {
	$val = $parameters['Entertainment'];
	$link .= "Entertainment&q=".$val. $apiKey;
}elseif ($parameters['Technology'] != null) {
	$val = $parameters['Technology'];
	$link .= "Technology".$apiKey;
}elseif ($parameters['Business'] != null) {
	$val = $parameters['Business'];
	$link .= "Business&q=".$val. $apiKey;
}else{
	return "nessun articolo trovato";
}



$json = googleNewsQuery($link);
$url = "";

if(!isset($json['articles'] ))
   return "nessun articolo trovato";

foreach ($json['articles'] as $key => $value) {
	$url = $value['url'];
	$image = $value['urlToImage'];
	$title = $value['title'];
    if($url != "")
	return array('url' => $url,'image' => $image, 'title' => $title );
}

if($url == ""){
$every= "https://newsapi.org/v2/everything?q=".$val
."&language=it&sortBy=publishedAt&apiKey=17c1953c3cc7450d958ff14f9e262c02";
$json = googleNewsQuery($every);
foreach ($json['articles'] as $key => $value) {
	$url = $value['url'];
	$image = $value['urlToImage'];
	$title = $value['title'];
    if($url != "")
	return array('url' => $url,'image' => $image, 'title' => $title );
}

if($url = "")
	return "sfortunatamente non sono stati trovati articoli";

}

}
/*
il metodo chiama getInterestsList per ottenere la lista
dei 30 maggiori interessi dell'utente , per ogni interesse 
effettua una ricerca tramite googleNewsQuery fin quando 
non viene trovato un articolo,una volta trovato l'articolo
viene restituito l'url altrimenti un messaggio d'errore
*/

function getInterestsNews($email){


$list = getInterestsList($email);
//echo $list[0];
foreach ($list as $key => $value){

	$link = "https://newsapi.org/v2/everything?q=".$key."&sortBy=publishedAt&apiKey=17c1953c3cc7450d958ff14f9e262c02";
	$link = str_replace(' ', '%20', $link);
	$json = googleNewsQuery($link);
	
	if(!isset($json['articles'] ))
        return "nessun articolo trovato";


	if($json['totalResults'] != 0){
		foreach ($json['articles'] as $key => $value) {
			$image = $value['urlToImage'];
	        $title = $value['title'];
		    $url =  $value['url'];
			return array('url' => $url,'image' => $image, 'title' => $title );
			
		}
		
	}
    
}

/*
se non viene trovato alcun articolo relativo agli interessi
principali dell'utente vengono restituite le notizie odierne
*/
return getTodayNews();


}

function explainNews($email){

$list = getInterestsList($email);
//echo $list[0];
foreach ($list as $key => $value){

	$link = "https://newsapi.org/v2/everything?q=".$key."&sortBy=publishedAt&apiKey=17c1953c3cc7450d958ff14f9e262c02";
	$link = str_replace(' ', '%20', $link);
	$json = googleNewsQuery($link);
	
	if(!isset($json['articles'] ))
        return "Non è possibile fornire una spiegazione &#x1f614";


	if($json['totalResults'] != 0){
		foreach ($json['articles'] as $key2 => $value2) {
			$image = $value2['urlToImage'];
	        $title = $value2['title'];
		    $url =  $value2['url'];

			return "Ti ho consigliato questo articolo perchè sei interessato a ".$key ."  &#x1f600";
			
		}
		
	}
    
}

/*
se non viene trovato alcun articolo relativo agli interessi
principali dell'utente vengono restituite le notizie odierne
*/
return "Non ho trovato articoli adatti a te e ti ho dato l'ultima notizia  &#x1f600";

}

/*
Questo metodo usa googleNewsQuery($link) per ottenere
l'elenco delle maggiori notizie odierne e ne resituisce la prima
in output
*/

function getTodayNews(){

	$link = "https://newsapi.org/v2/top-headlines?country=it&apiKey=17c1953c3cc7450d958ff14f9e262c02";
	$json = googleNewsQuery($link);
$url = "";

if(!isset($json['articles'] ))
   return "nessun articolo trovato";

foreach ($json['articles'] as $key => $value) {
	$url = $value['url'];
	$image = $value['urlToImage'];
	$title = $value['title'];
    if($url != "")
	return array('url' => $url,'image' => $image, 'title' => $title );
}

if($url = "")
	return "nessun articolo trovato";

}
/*
@parameters parametri con i dati sul testo da cercare
la funzione prende il parametro any individuato da DIalogflow ed effettua una
ricerca tramite il metodo GoogleNewsQuery.
Se vengono trovati articoli ne viene restituito il link,
altrimenti viene restituita una notizia di oggi tramite il
metodo getTodayNews
*/
function cercaNews($parameters){

if(isset($parameters['any'])){
$val = $parameters['any'];	
$val = str_replace(' ', '%20', $val);
$link = "https://newsapi.org/v2/everything?q=".$val.
"&sortBy=publishedAt&Language=it&apiKey=17c1953c3cc7450d958ff14f9e262c02";
$json = googleNewsQuery($link);

if(!isset($json['articles'] ))
   return "nessun articolo trovato";

foreach ($json['articles'] as $key => $value) {
	$url = $value['url'];
	$image = $value['urlToImage'];
	$title = $value['title'];
    if($url != "")
	  return array('url' => $url,'image' => $image, 'title' => $title );
}
if($url = "")
	return getTodayNews();

}else{
	return getTodayNews();
}

}
/*
la funzione usa il parametro $link pre fare
una chiamata REST A GOOGLE NEWS e restituisce il risultato
*/
function googleNewsQuery($link){

$ch = curl_init();
$json_data = null;

curl_setopt($ch, CURLOPT_URL,$link);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);
$result = json_decode($server_output,true);
curl_close ($ch);

return $result;

}


?>