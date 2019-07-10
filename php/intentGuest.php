<?php
namespace Google\Cloud\Samples\Dialogflow;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Guzzle\Http\Exception\ClientErrorResponseException;

require __DIR__.'/vendor/autoload.php';


include 'SpotifyIntent.php';
include 'Video.php';
include 'News.php';
include 'Meteo.php';

$city = "Roma";
header('Content-type: text/plain; charset=utf-8');
 ini_set('display_errors', 1);
//Controllo se la variabile 'testo' ricevuta è nulla
if (isset($_POST{'testo'})) {
    $testo = $_POST{'testo'};
}

if(isset($_POST{'city'})){
    $city = $_POST{'city'};
}



function detect_intent_texts($projectId,$city,$email, $text, $sessionId, $languageCode = 'it-IT')
{
    // new session
    $test = array('credentials' => 'myrrorbot-4f360-cbcab170b890.json');
    $sessionsClient = new SessionsClient($test);
    $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
    
    // create text input
    $textInput = new TextInput();
    $textInput->setText($text);
    $textInput->setLanguageCode($languageCode);

    // create query input
    $queryInput = new QueryInput();
    $queryInput->setText($textInput);
    
    // get response and relevant info
    $response = $sessionsClient->detectIntent($session, $queryInput);
    $queryResult = $response->getQueryResult();
 

    $parameters=json_decode($queryResult->getParameters()->serializeToJsonString(), true);
    
    $queryText = $queryResult->getQueryText();
    $intent = $queryResult->getIntent();
    
    //risposta intent
    $fulfilmentText = $queryResult->getFulfillmentText();

    if(!is_null($intent)){
        $displayName = $intent->getDisplayName(); //Nome dell'intent
        $confidence = $queryResult->getIntentDetectionConfidence(); //Livello di confidence
        selectIntent($email,$displayName,$confidence,$text,$fulfilmentText,$parameters,$city);
        
    }else{

        $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";

        //Stampo la risposta relativa all'intent non identificato
        $arr = array('intentName' => "Non identificato", 'confidence' => "0",'answer' => $answer);
        printf(json_encode($arr,'UTF8'));  //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

    }
    
    $sessionsClient->close();
}



function selectIntent($email,$intent, $confidence,$text,$resp,$parameters,$city){

    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence >= 0.60){              

        $answer = null;

        switch ($intent) {

          
                
             case 'meteo binario':
             //$city = "Bari";
                $answer = binaryWeather($city,$parameters,$text);
                break; 

             case 'Meteo odierno':
             //$city = "Bari";
                $answer = getTodayWeather($city,$parameters,$text);
                break; 

             case 'Previsioni meteo':
               //$city = "Bari";
                $answer = getWeather($city,$parameters,$text);
                break;

            case 'Default Welcome Intent':
                $answer = $resp;
                break;

            default:
               
                    $answer = "Questa funzione è disponibile solo dopo aver effettuato il login a myrror";

               
                break;
        }

    }else {
            $answer = "Questa funzione è disponibile solo dopo aver effettuato il login a myrror ";

    }


    //SPOTIFY --> Valori soglia diversi
    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence >= 0.50 && ($intent == 'Canzone per nome' || $intent == 'Canzone per artista' || $intent == 'Canzoni in base al genere'
        ||  $intent == 'Canzone per nome subintent'  || $intent == 'Canzone per artista subintent' || 
        $intent == 'Canzoni in base al genere subintent' 
      
         )){

        switch ($intent) {
            case 'Canzone per nome':
                $answer = getMusicByTrack($resp,$parameters,$text,$email);
                break;

            case 'Canzone per nome subintent':
                $par = array('music-artist' => $resp);
                $answer = getMusicByArtist($resp,$par,$text,$email);
                break;

            case 'Canzone per artista':
                $answer = getMusicByArtist($resp,$parameters,$text,$email);
                break;

            case 'Canzone per artista subintent':
                $par = array('music-artist' =>  $resp);  
                $answer = getMusicByArtist($resp,$par,$text,$email);
                break;
           
            case 'Canzoni in base al genere':
                $answer = getMusicByGenre($resp,$parameters,$text,$email);
                break;

            case 'Canzoni in base al genere subintent':
                $par  = array('GeneriMusicali' =>  $resp);
                $answer = getMusicByGenre($resp,$par,$text,$email);
                break;
     

            default:
                  $answer = "Questa funzione è disponibile solo dopo aver effettuato il login a myrror ";

                break;
        }
    }


    //YOUTUBE --> Valori soglia diversi
    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence >= 0.50 && ( $intent == 'Ricerca Video'     )){
        switch ($intent) {

            case 'Ricerca Video':
                $answer = getVideoBySearch($resp,$parameters,$text,$email);
                break;

            default:
                  $answer = "Questa funzione è disponibile solo dopo aver effettuato il login a myrror ";

                break;
        }
    }
    
        //GOOGLE-NEWS--> Valori soglia diversi
    if($confidence >= 0.50  && ($intent == 'Notizie in base ad un argomento' ||  $intent == 'Notizie odierne' || $intent == 'Ricerca articolo'  )){

        switch ($intent) {
             case 'Notizie in base ad un argomento':
                $answer = getNewsTopic($parameters);
                
                break;

        

            case 'Notizie odierne':
                $answer =getTodayNews();   
              
                break;

            case 'Ricerca articolo':
               $answer = cercaNews($parameters);   

                break;  

            default:
                  $answer = "Questa funzione è disponibile solo dopo aver effettuato il login a myrror ";

                break;
        }

    }

    //Stampo la risposta
    $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);

    if ($arr['intentName'] == 'Canzone per nome') {
        printf(json_encode($arr)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
    }else{
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
    }
}

//date_default_timezone_set('Europe/Madrid'); //Imposto la stessa timezone di Dialogflow (per gli orari)

try {
  detect_intent_texts('myrrorbot-4f360',$city,"",$testo,'123456');
} catch (ClientErrorResponseException $exception) {
    $responseBody = $exception->getResponse()->getBody(true);
    echo $responseBody;
}



